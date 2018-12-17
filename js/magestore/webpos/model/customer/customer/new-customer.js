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

define(
    [
        'jquery',
        'ko',
        "helper/general",
        'model/customer/customer/billing-address',
        'model/customer/customer/shipping-address',
        'model/customer/customer/edit-customer',
        'model/customer/customer-factory',
        'model/checkout/checkout',
        'action/notification/add-notification',
        'action/checkout/select-customer-checkout',
        'eventManager',
        'mage/validation'
    ],
    function (
        $,
        ko,
        generalHelper,
        billingModel,
        shippingModel,
        editCustomerModel,
        CustomerFactory,
        checkoutModel,
        addNotification,
        selectCustomer,
        Event
    ) {
        "use strict";
        return {
            /* Binding billing address information in create customer form*/
            customerGroupArray: ko.observableArray(window.webposConfig.customerGroup),
            isAddCustomer: ko.observable(false),
            /* Create Customer Form*/
            firstNameCustomer: ko.observable(''),
            lastNameCustomer: ko.observable(''),
            emailCustomer: ko.observable(''),
            groupCustomer: ko.observable(''),
            vatCustomer: ko.observable(''),
            dob: ko.observable(''),
            phone: ko.observable(''),
            isSubscriberCustomer: ko.observable(false),
            /* End Form*/

            deleteBillingAddress: function () {
                billingModel.isShowBillingSummaryForm(false);
                billingModel.firstNameBilling('');
                billingModel.lastNameBilling('');
                billingModel.companyBilling('');
                billingModel.phoneBilling('');
                billingModel.street1Billing('');
                billingModel.street2Billing('');
                billingModel.countryBilling('');
                billingModel.regionBilling('');
                billingModel.regionIdBilling(0);
                billingModel.cityBilling('');
                billingModel.zipcodeBilling('');
                billingModel.vatBilling('');
            },

            deleteShippingAddress: function () {
                shippingModel.isShowShippingSummaryForm(false);
                shippingModel.firstNameShipping('');
                shippingModel.lastNameShipping('');
                shippingModel.companyShipping('');
                shippingModel.phoneShipping('');
                shippingModel.street1Shipping('');
                shippingModel.street2Shipping('');
                shippingModel.countryShipping('');
                shippingModel.regionShipping('');
                shippingModel.regionIdShipping(0);
                shippingModel.cityShipping('');
                shippingModel.zipcodeShipping('');
                shippingModel.vatShipping('');
            },

            /* Get billing address data*/
            getBillingAddressData: function () {
                var data = {};
                data.id = Date.now();
                data.firstname = billingModel.firstNameBilling();
                data.lastname = billingModel.lastNameBilling();
                data.street = [
                    billingModel.street1Billing(), billingModel.street2Billing()
                ];
                data.telephone = billingModel.phoneBilling();
                data.company = billingModel.companyBilling();
                data.country_id = billingModel.countryBilling();
                data.city = billingModel.cityBilling();
                data.postcode = billingModel.zipcodeBilling();
                data.region_id = billingModel.regionIdBilling();
                data.region = billingModel.regionObjectBilling();
                //	data.is_gift = billingModel.regionObjectIsGift();
                return data;
            },

            /* Get shipping address data*/
            getShippingAddressData: function () {
                var data = {};
                data.id = Date.now();
                data.firstname = shippingModel.firstNameShipping();
                data.lastname = shippingModel.lastNameShipping();
                data.street = [
                    shippingModel.street1Shipping(), shippingModel.street2Shipping()
                ];
                data.telephone = shippingModel.phoneShipping();
                data.company = shippingModel.companyShipping();
                data.country_id = shippingModel.countryShipping();
                data.city = shippingModel.cityShipping();
                data.postcode = shippingModel.zipcodeShipping();
                data.region_id = shippingModel.regionIdShipping();
                data.region = shippingModel.regionObjectShipping();
                //	data.is_gift = billingModel.regionObjectIsGift();
                return data;
            },

            save: function () {
                debugger;
                var data = {};
                var self = this;

                var domain = document.domain;
                domain = domain.replace('www.', '');

                if (!document.getElementById("email").value || document.getElementById("email").value == 'undefined') {
                    // document.getElementById("email").value = document.getElementById("add-customer-phone").value + '@'+domain;
                    // //this.emailCustomer(document.getElementById("add-customer-phone").value + '@'+domain);@noemail.com
                    // this.emailCustomer(document.getElementById("add-customer-phone").value + '@noemail.com');

                    document.getElementById("email").value = null;
                }
                if (!document.getElementById("first-name").value || document.getElementById("first-name").value == 'undefined') {
                    document.getElementById('first-name').value = this.phone().trim();
                    this.firstNameCustomer('test');
                    this.firstNameCustomer(this.phone().trim());
                }
                // if(!document.getElementById("last-name").value || document.getElementById("last-name").value == 'undefined') {
                // 	document.getElementById('last-name').value = '.';
                // 	this.lastNameCustomer('test');
                //     this.lastNameCustomer('.');
                // }

                document.getElementById('customer_groups').value = '1';
                this.groupCustomer('test');
                this.groupCustomer(1);

                var elements = document.getElementById("form-customer-add-customer-checkout").elements;
                for (var i = 0; i < elements.length; i++) {
                    var element = elements[i];
                    if ("createEvent" in document) {
                        var evt = document.createEvent("HTMLEvents");
                        evt.initEvent("change", false, true);
                        element.dispatchEvent(evt);
                    } else {
                        element.fireEvent("onchange");
                    }
                }

                var elements = document.getElementById("form-customer-add-shipping-address-checkout").elements;
                for (var i = 0; i < elements.length; i++) {
                    var element = elements[i];
                    if ("createEvent" in document) {
                        var evt = document.createEvent("HTMLEvents");
                        evt.initEvent("change", false, true);
                        element.dispatchEvent(evt);
                    } else {
                        element.fireEvent("onchange");
                    }
                }

                var elements = document.getElementById("form-customer-add-billing-address-checkout").elements;
                for (var i = 0; i < elements.length; i++) {
                    var element = elements[i];
                    if ("createEvent" in document) {
                        var evt = document.createEvent("HTMLEvents");
                        evt.initEvent("change", false, true);
                        element.dispatchEvent(evt);
                    } else {
                        element.fireEvent("onchange");
                    }
                }

                //alert(data.is_gift);
                //ryan
                // data.id = 'notsync_' + this.emailCustomer();
                data.id = 'notsync_' + this.firstNameCustomer();
                data.firstname = this.firstNameCustomer();
                data.lastname = this.lastNameCustomer();
                data.full_name = this.firstNameCustomer() + ' ' + this.lastNameCustomer();
                // Islam Repeated Customer 2018
                data.email = this.emailCustomer().trim();
                // Islam Repeated Customer 2018
                data.group_id = this.groupCustomer();
                data.subscriber_status = this.isSubscriberCustomer();
                data.dob = this.dob(); //date_of_birthday
                // Islam Repeated Customer 2018
                data.phone = this.phone().trim();
                // Islam Repeated Customer 2018
                data.addresses = [];
                if (!billingModel.isShowBillingSummaryForm() && !shippingModel.isShowShippingSummaryForm()) {
                    
                    
                    addNotification(generalHelper.__('You must insert Address (Address Required!).'), true, 'danger', generalHelper.__('Error'));
                    return;

                    data.addresses = [];
                    checkoutModel.saveShippingAddress({
                        'id': 0,
                        'firstname': data.firstname,
                        'lastname': data.lastname,
                        'phone': data.phone
                    });
                    checkoutModel.saveBillingAddress({
                        'id': 0,
                        'firstname': data.firstname,
                        'lastname': data.lastname,
                        'phone': data.phone
                    });
                } else {
                    var shippingAddressData = this.getShippingAddressData();
                    var billingAddressData = this.getBillingAddressData();
                    if (shippingModel.isSameBillingShipping()) {
                        shippingAddressData.default_billing = true;
                        shippingAddressData.default_shipping = true;
                        data.addresses.push(shippingAddressData);
                        checkoutModel.saveBillingAddress(shippingAddressData);
                        checkoutModel.saveShippingAddress(shippingAddressData);
                    } else {
                        if (shippingModel.isShowShippingSummaryForm()) {
                            shippingAddressData.default_shipping = true;
                            data.addresses.push(shippingAddressData);
                            checkoutModel.saveShippingAddress(shippingAddressData);
                            if (billingModel.isShowBillingSummaryForm()) {
                                checkoutModel.saveBillingAddress(this.getBillingAddressData());
                            } else {
                                checkoutModel.saveBillingAddress({
                                    'id': 0,
                                    'firstname': data.firstname,
                                    'lastname': data.lastname,
                                    'phone': data.phone
                                });
                            }
                        }
                        if (billingModel.isShowBillingSummaryForm()) {
                            billingAddressData.default_billing = true;
                            data.addresses.push(billingAddressData);
                            checkoutModel.saveBillingAddress(billingAddressData);
                            if (shippingModel.isShowShippingSummaryForm()) {
                                checkoutModel.saveShippingAddress(shippingAddressData);
                            } else {
                                checkoutModel.saveShippingAddress({
                                    'id': 0,
                                    'firstname': data.firstname,
                                    'lastname': data.lastname,
                                    'phone': data.phone
                                });
                            }
                        }
                        if (!billingModel.isShowBillingSummaryForm() && !shippingModel.isShowShippingSummaryForm()) {
                            checkoutModel.saveShippingAddress({
                                'id': 0,
                                'firstname': data.firstname,
                                'lastname': data.lastname,
                                'phone': data.phone
                            });
                            checkoutModel.saveBillingAddress({
                                'id': 0,
                                'firstname': data.firstname,
                                'lastname': data.lastname,
                                'phone': data.phone
                            });
                        }
                    }
                }

                var telephone;
                if (data.addresses.length > 0) {
                    var addresses = data.addresses;
                    telephone = addresses[0].telephone;
                } else {
                    telephone = 'N/A';
                    if (this.phone())
                        telephone = this.phone();
                }
                  // Islam Repeated Customer 2018
                data.telephone = telephone.trim();
                  // Islam Repeated Customer 2018
                if (this.validateCustomerForm()) {
                    var deferred;
                    // Begin islam
                    if (generalHelper.isOnlineCheckout()) {

                        deferred = CustomerFactory.get().setMode('online').getCollection()
                            .addFieldToFilter([['phone', this.phone().trim(), 'eq'], ['email', this.emailCustomer().trim(), 'eq'], ['firstname', this.firstNameCustomer(), 'eq']], 0, 0)
                            .load();
                    }
                    else {
                        deferred = CustomerFactory.get().setMode('offline').getCollection()
                            .addFieldToFilter([['phone', this.phone().trim(), 'eq'], ['email', this.emailCustomer().trim(), 'eq'], ['firstname', this.firstNameCustomer(), 'eq']], 0, 0)
                            .load();
                    }
                    deferred.done(function (filterData) {
                        var items = filterData.items;
                        var check = true;
                        if (items.length > 0) {
                            if (/*items[0].firstname == data.firstname && */ items[0].phone == data.phone && items[0].email == data.email)
                            {
                                check = false
                                addNotification(generalHelper.__('The customer phone and email is existed.'), true, 'danger', generalHelper.__('Error'));
                            }
                            else if(items[0].firstname == data.firstname)
                            {
                                check = true;
                                //addNotification(generalHelper.__('The customer name is existed.'), true, 'danger', generalHelper.__('Error'));
                            }
                            else if (items[0].phone == data.phone)
                            {
                                check = false;
                                addNotification(generalHelper.__('The customer phone is existed.'), true, 'danger', generalHelper.__('Error'));
                            }
                            else if (items[0].email == data.email)
                            {
                                check = false;
                                addNotification(generalHelper.__('The customer email is existed.'), true, 'danger', generalHelper.__('Error'));
                            }
                        }
                        if(check) {
                            if (typeof data['columns'] != 'undefined') {
                                delete data['columns'];
                            }

                            var saveDeferred = CustomerFactory.get().setData(data).setPush(true).save();
                            saveDeferred.done(function (dataOffline) {
                                selectCustomer(dataOffline);
                                var addressData = dataOffline.addresses;
                                var isSetBilling = false;
                                var isSetShipping = false;
                                $.each(addressData, function (index, value) {
                                    if (value.default_billing) {
                                        editCustomerModel.billingAddressId(value.id);
                                        editCustomerModel.setBillingPreviewData(value);
                                        editCustomerModel.isShowPreviewBilling(true);
                                        isSetBilling = true;
                                    }
                                    if (value.default_shipping) {
                                        editCustomerModel.shippingAddressId(value.id);
                                        editCustomerModel.setShippingPreviewData(value);
                                        editCustomerModel.isShowPreviewShipping(true);
                                        isSetShipping = true;
                                    }
                                });
                                if (!isSetBilling) {
                                    editCustomerModel.isShowPreviewBilling(false);
                                }
                                if (!isSetShipping) {
                                    editCustomerModel.isShowPreviewShipping(false);
                                }
                                self.isAddCustomer(true);
                                Event.dispatch('customer_pull_after');
                                $.toaster(
                                    {
                                        priority: 'success',
                                        title: generalHelper.__('Success'),
                                        message: generalHelper.__('The customer is saved successfully.')
                                    }
                                );
                            });
                            self.cancelCustomerForm();
                        }

                    });
                    // End islam
                }
            },

            cancelCustomerForm: function () {
                var customerForm = $('#form-customer-add-customer-checkout');
                customerForm.removeClass('fade-in');
                customerForm.removeClass('show');
                customerForm.addClass('fade');
                // customerForm.validation();
                // customerForm.validation('clearError');
                this.resetFormInfo('form-customer-add-customer-checkout');
                this.resetFormInfo('form-customer-add-shipping-address-checkout');
                this.resetFormInfo('form-customer-add-billing-address-checkout');
                this.resetFormInfo('form-customer-add-address-checkout');


                this.deleteShippingAddress();
                this.deleteBillingAddress();
                billingModel.isShowBillingSummaryForm(false);
                shippingModel.isShowShippingSummaryForm(false);
                shippingModel.isSameBillingShipping(true);
                $('.pos-overlay').removeClass('active');
                $('.notification-bell').show();
                $('#c-button--push-left').show();
                $('.wrap-backover').hide();
            },

            /* Validate Customer Form*/
            validateCustomerForm: function () {
                var form = '#form-customer-add-customer-checkout';
                return $(form).validation({}) && $(form).validation('isValid');
            },

            /* Reset Form*/
            resetFormInfo: function (form) {
                document.getElementById(form).reset();
            }
        };
    }
);
