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
        'posComponent',
        'model/checkout/checkout/shipping',
        'helper/general',
        'mage/calendar'
    ],
    function ($, ko, Component, ShippingModel, Helper) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'ui/checkout/checkout/shipping',
            },
            items: ShippingModel.items,
            isSelected: ShippingModel.isSelected,

            initialize: function () {
                this._super();
                this.initObserver();
            },
            initObserver: function () {
                var self = this;
                Helper.observerEvent('go_to_checkout_page', function () {
                    if (!Helper.isOnlineCheckout()) {
                        ShippingModel.resetShipping();
                    }
                });
            },
            setShippingMethod: function (data, event) {
                // var element = event.currentTarget;
                // var flateRateId  = element.getAttribute('id');
                //
                // if(flateRateId === "flatrate_flatrate"){
                //    $(event.currentTarget).parent(".input-box").find(".customshipping_address").show();
                // }else{
                //   $(".customshipping_address").hide();
                // }
                ShippingModel.saveShippingMethod(data);
            },
            getShippingPrice: function (price, priceType) {
                return ShippingModel.formatShippingPrice(price, priceType);
            },

            getShippingAddress: function () {
                // debugger;
                // Islam  OO 2018
                //return;
                // Islam  OO 2018
                var getShippingAddressHtml = '';
                if (typeof ShippingModel.getShippingAddress().shipping.address !== 'undefined') {

                    var address = ShippingModel.getShippingAddress().shipping.address;

                    if (address.firstname != "" && address.lastname != "") {
                        getShippingAddressHtml += address.firstname + ' ' + address.lastname + ' , ';
                    } else if (address.firstname != "") {
                        getShippingAddressHtml += address.firstname + ' , ';
                    } else if (address.lastname != "" &&  address.lastname !='undefined') {
                        getShippingAddressHtml += address.lastname + ' , ';
                    }
                    //This case when order is gift
                    if (!address.firstname && !address.lastname && address.receiver_name ){
                        getShippingAddressHtml = address.receiver_name;
                    }

                    if (address.street != "") {
                        getShippingAddressHtml += address.street + ' , ';
                    }
                    if (address.city != "") {
                        getShippingAddressHtml += address.city + ' , ';
                    }
                    if (address.postcode != "") {
                        getShippingAddressHtml += address.postcode + ' , ';
                    }
                    if (address.telephone != "") {
                        getShippingAddressHtml += 'T : ' + address.telephone;
                    }
                }

                return getShippingAddressHtml;
            },
            useDeliveryTime: function () {
                return ShippingModel.useDeliveryTime();
            },
            initDate: function () {
                var currentDate = new Date();
                var year = currentDate.getFullYear();
                var month = currentDate.getMonth();
                var day = currentDate.getDate();
                $("#delivery_date").calendar({
                    showsTime: true,
                    controlType: 'select',
                    timeFormat: 'HH:mm TT',
                    showTime: false,
                    minDate: new Date(year, month, day, '00', '00', '00', '00'),
                });
            }
        });
    }
);