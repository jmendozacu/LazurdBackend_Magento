/*
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/*global define*/
define(
    [
        'jquery',
        'model/customer/customer/edit-customer',
        'model/customer/customer-factory',
        'action/notification/add-notification',
        'helper/general',
        'eventManager',
        'model/checkout/checkout',
        'model/customer/current-customer',
    ],
    function ($,
        editCustomerModel,
        CustomerFactory,
        addNotification,
        generalHelper,
        eventManager,
        checkoutModel,
        currentCustomer
    ) {
        'use strict';
        return function () {
            if (!editCustomerModel.email()) {
                //editCustomerModel.email(editCustomerModel.phone() + '@lojena.com');
                editCustomerModel.email(null);
            }
            if (editCustomerModel.validateEditCustomerForm()) {
                var currentData = currentCustomer.data();
                currentData.firstname = editCustomerModel.firstName();
                currentData.lastname = editCustomerModel.lastName();
                 // Islam Repeated Customer 2018
                currentData.email = (editCustomerModel.email() == undefined && editCustomerModel.email() != null ? editCustomerModel.email().trim() : editCustomerModel.email());
                 // Islam Repeated Customer 2018
                currentData.dob = editCustomerModel.dob();
                 // Islam Repeated Customer 2018
                currentData.phone = editCustomerModel.phone().trim();
                 // Islam Repeated Customer 2018
                currentData.group_id = editCustomerModel.group_id();
                currentData.subscriber_status = editCustomerModel.isSubscriberCustomer();
                currentData.full_name = editCustomerModel.firstName() + ' ' + editCustomerModel.lastName();

                var customerDeferred;
                if (generalHelper.isOnlineCheckout()) {

                    customerDeferred = CustomerFactory.get().setMode('online').load(currentData.id);
                } else {

                    customerDeferred = CustomerFactory.get().setMode('offline').load(currentData.id);
                }
                customerDeferred.done(function (data) {
                    var addressData = data.addresses;
                    $.each(addressData, function (index, value) {
                        var address = addressData[index];
                        address.default_billing = false;
                        address.default_shipping = false;
                        if (value.id == editCustomerModel.billingAddressId() && value.id != 0) {

                            if (typeof address.default_billing == 'undefined' || address.default_billing == false) {
                                address.default_billing = true;
                                editCustomerModel.isChangeCustomerInfo(true);
                            }
                        }

                        if (value.id == editCustomerModel.shippingAddressId() && value.id != 0) {
                            if (typeof address.default_shipping == 'undefined' || address.default_shipping == false) {
                                //alert('yes');
                                address.default_shipping = true;
                                editCustomerModel.isChangeCustomerInfo(true);
                            }
                        }
                        addressData[index] = address;
                    });
                    currentData.addresses = addressData;
                    currentCustomer.fullName(editCustomerModel.firstName());
                    // currentCustomer.fullName(editCustomerModel.firstName() + ' ' + editCustomerModel.lastName());

                    if (typeof currentData['columns'] != 'undefined') {
                        delete currentData['columns'];
                    }

                    var billingAddressId = editCustomerModel.billingAddressId();
                    var shippingAddressId = editCustomerModel.shippingAddressId();

                    var currentCustomerId;
                    if (generalHelper.isOnlineCheckout()) {
                        currentCustomerId = currentData.entity_id;
                    } else {
                        currentCustomerId = currentData.id;
                    }

                    var deferred;
                    if (generalHelper.isOnlineCheckout()) {
                        deferred = CustomerFactory.get().setMode('online').getCollection()
                            // islam
                            .addFieldToFilter([['phone', editCustomerModel.phone().trim(), 'eq'], ['email', currentData.email, 'eq'], ['firstname', editCustomerModel.firstName(), 'eq']], 0, 0)
                            // end 
                            //.addFieldToFilter('email', editCustomerModel.email(), 'eq')
                            .addFieldToFilter('entity_id', currentCustomerId, 'neq')
                            .load();
                    } else {
                        deferred = CustomerFactory.get().setMode('offline').getCollection()
                            .addFieldToFilter([['phone', editCustomerModel.phone().trim(), 'eq'], ['email', currentData.email, 'eq'], ['firstname', editCustomerModel.firstName(), 'eq']], 0, 0)
                            // .addFieldToFilter('email', editCustomerModel.email(), 'eq')
                            .addFieldToFilter('id', currentCustomerId, 'neq')
                            .load();
                    }
                    deferred.done(function (data) {
                        var items = data.items;
                        var check = true;
                        if (items.length > 0) {
                            if (/*items[0].firstname == currentData.firstname &&*/ items[0].phone == currentData.phone && items[0].email == currentData.email)
                            {
                                check = false;
                                addNotification(generalHelper.__('The customer phone and email is existed.'), true, 'danger', generalHelper.__('Error'));
                            }
                            else if(items[0].firstname == currentData.firstname)
                            {
                                check = true;
                                //addNotification(generalHelper.__('The customer name is existed.'), true, 'danger', generalHelper.__('Error'));
                            }
                            else if (items[0].phone == currentData.phone)
                            {
                                check = false;
                                addNotification(generalHelper.__('The customer phone is existed.'), true, 'danger', generalHelper.__('Error'));
                            }
                            else if (items[0].email == currentData.email)
                            {
                                check = false;
                                addNotification(generalHelper.__('The customer email is existed.'), true, 'danger', generalHelper.__('Error'));
                            }



                            //addNotification(generalHelper.__('The customer email is existed.'), true, 'danger', generalHelper.__('Error'));
                        } 
                        if(check) {

                            delete currentData['default_billing'];
                            delete currentData['default_shipping'];

                            var deferred = CustomerFactory.get().setData(currentData).setPush(true).save();

                            deferred.done(function (customerDataAfterSave) {
                                var allAddress = customerDataAfterSave.addresses;
                                currentCustomer.setData(customerDataAfterSave);
                                if (billingAddressId != 0) {
                                    var billingAddress = {};
                                    $.each(allAddress, function (index, value) {
                                        if (typeof (value.id) != 'undefined' && value.id == billingAddressId) {
                                            billingAddress = allAddress[index];
                                        }
                                    });
                                    checkoutModel.saveBillingAddress(billingAddress);
                                } else {
                                    if (customerDataAfterSave.id) {
                                        checkoutModel.saveBillingAddress({
                                            'id': 0,
                                            'firstname': customerDataAfterSave.firstname,
                                            'lastname': customerDataAfterSave.lastname,
                                            'phone': customerDataAfterSave.phone
                                        });
                                    } else {
                                        checkoutModel.saveBillingAddress({
                                            'id': 0,
                                            'firstname': '',
                                            'lastname': ''
                                        });
                                    }
                                }
                                if (shippingAddressId != 0) {
                                    var shippingAddress = {};
                                    $.each(allAddress, function (index, value) {
                                        if (typeof (value.id) != 'undefined' && value.id == shippingAddressId) {
                                            shippingAddress = allAddress[index];
                                        }
                                    });
                                    checkoutModel.saveShippingAddress(shippingAddress);
                                } else {
                                    if (customerDataAfterSave.id) {
                                        checkoutModel.saveShippingAddress({
                                            'id': 0,
                                            'firstname': customerDataAfterSave.firstname,
                                            'lastname': customerDataAfterSave.lastname,
                                            'phone': customerDataAfterSave.phone
                                        });
                                    } else {
                                        checkoutModel.saveShippingAddress({
                                            'id': 0,
                                            'firstname': '',
                                            'lastname': ''
                                        });
                                    }
                                }
                                eventManager.dispatch('customer_pull_after', []);
                                $.toaster(
                                    {
                                        priority: 'success',
                                        title: generalHelper.__('Success'),
                                        message: generalHelper.__('The customer is saved successfully.')
                                    }
                                );
                            });
                            editCustomerModel.hideCustomerForm();
                        }
                    });
                    //}

                });

            }
        }
    }
);
