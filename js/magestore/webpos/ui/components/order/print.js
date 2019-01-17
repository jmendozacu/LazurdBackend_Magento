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
// Islam Elgarhy Print As Magento 
define(
    [
        'jquery',
        'ko',
        'ui/components/order/action',
        'model/sales/order/total',
        'helper/general',
        'helper/datetime',
        'action/notification/add-notification',
        'model/directory/currency',
        'lib/jquery/jquery-barcode'
    ],
    function ($, ko, Component, orderTotal, Helper, DateHelper, AddNoti, currency) {
        "use strict";

        return Component.extend({
            containerId: 'container-print-order',
            totalsCode: ko.observableArray(),
            configs: ko.observableArray(),
            baseRate: ko.observableArray(),
            currentRate: ko.observableArray(),
            customerAdditionalInfomation: ko.observableArray(),
            currentCurrencyCode: window.webposConfig.currentCurrencyCode,
            baseCurrencyCode: window.webposConfig.baseCurrencyCode,
            defaults: {
                template: 'ui/order/print',
            },

            regionHTML: ko.observable(),
            blockHTML: ko.observable(),
            avenueHTML: ko.observable(),
            streetHTML: ko.observable(),
            buildingHTML: ko.observable(),
            flatHTML: ko.observable(),
            receiverNameHTML: ko.observable(),
            receiverTelephoneHTML: ko.observable(),
            senderNameHTML: ko.observable(),

            initialize: function () {
                this._super();
                var self = this;
                self.initDefaultData();
                ko.pureComputed(function () {
                    return self.orderData();
                }).subscribe(function () {
                    if (self.orderData()) {
                        self.initDefaultData();
                        $("#webpos_sales_order_receipt_barcode").html("").barcode(self.getOrderData('increment_id'), "code128", {
                            output: "css",
                            bgColor: "#FFFFFF",
                            color: "#000000",
                            barWidth: 1,
                            barHeight: 20
                        });
                    }
                });

            },

            initDefaultData: function () {
                var self = this;
                //alert(JSON.stringify(this.getOrderData('items')));

                //console.log(JSON.stringify(self.orderData()));

                var discountLabel = (self.getOrderData().discount_description) ? ('Discount' +
                    '(' + self.orderData().discount_description + ')') : 'Discount';
                var totalsCode = [
                    { code: 'subtotal', title: 'Subtotal', required: true, sortOrder: 1, isPrice: true },
                    { code: 'shipping_amount', title: 'Shipping', required: true, sortOrder: 10, isPrice: true },
                    { code: 'tax_amount', title:'Tax', required: true, sortOrder: 20, isPrice: true },
                    { code: 'discount_amount', title: discountLabel, required: false, sortOrder: 30, isPrice: true },
                    { code: 'grand_total', title: 'Grand Total', required: true, sortOrder: 40, isPrice: true },
                    { code: 'total_paid', title: 'Total Paid', required: true, sortOrder: 50, isPrice: true },
                    { code: 'total_due', title: 'Total Due', required: true, sortOrder: 60, isPrice: true }
                ];

                if (self.hasRefundAmount()) {
                    totalsCode.push({ code: 'total_refunded', title: 'Total Refunded', required: true, sortOrder: 60, isPrice: true });
                }

                var eventData = {
                    totals: totalsCode
                };
                Helper.dispatchEvent('prepare_receipt_totals', eventData);
                totalsCode.sort(function (a, b) {
                    if (!a.sortOrder) {
                        a.sortOrder = 2;
                    }
                    if (!b.sortOrder) {
                        b.sortOrder = 2;
                    }
                    return parseFloat(a.sortOrder) - parseFloat(b.sortOrder);
                });
                this.totalsCode(totalsCode);

                var configs = [
                    { code: 'auto_print', value: window.webposConfig["webpos/receipt/auto_print"] },
                    { code: 'font_type', value: window.webposConfig["webpos/receipt/font_type"] },
                    { code: 'footer_text', value: window.webposConfig["webpos/receipt/footer_text"] },
                    { code: 'header_text', value: window.webposConfig["webpos/receipt/header_text"] },
                    { code: 'show_cashier_name', value: window.webposConfig["webpos/receipt/show_cashier_name"] },
                    { code: 'show_comment', value: window.webposConfig["webpos/receipt/show_comment"] },
                    { code: 'show_barcode', value: window.webposConfig["webpos/receipt/show_barcode"] },
                    { code: 'show_receipt_logo', value: window.webposConfig["webpos/receipt/show_receipt_logo"] },
                    { code: 'logo', value: window.webposConfig["webpos/general/webpos_logo"] }
                ];
                this.configs(configs);
            },
            hasRefundAmount: function () {
                return (this.getOrderData('total_refunded') > 0) ? true : false;
            },
            hasGiftCard: function (label) {
                if (label == 'Gift Card') {
                    return ' (' + this.getOrderData('gift_codes') + ')';
                }
                return '';
            },
            formatPrice: function (string) {
                var self = this;
                var rates = currency().getCollection().load();
                var from = self.getOrderData().order_currency_code;
                var to = self.getOrderData().base_currency_code;

                var currentCurrency = window.webposConfig.currentCurrencyCode;
                rates.done(function (data) {
                    var baseRate = '';
                    var baseToCurrent = '';
                    if (data.items.length > 0) {
                        $.each(data.items, function (index, value) {
                            if (value.code == from) {
                                baseRate = (parseFloat(value.currency_rate));
                                self.baseRate(baseRate);
                            }
                            if (value.code == currentCurrency) {
                                baseToCurrent = (parseFloat(value.currency_rate));
                                self.currentRate(baseToCurrent);
                            }
                        });
                    }
                });
                if (self.baseRate() != '' && self.currentRate() != '') {
                    return this.orderIsGift() == true ? '' : Helper.formatPrice(string / self.baseRate() * self.currentRate());
                } else {
                    return this.orderIsGift() == true ? '' : Helper.formatPrice(string);
                }

            },

            currencyConvert: function (string, from, to) {
                return Helper.convertPrice(string, from, to);
            },

            getConfigData: function (code) {
                if (this.configs()) {
                    var config = ko.utils.arrayFirst(this.configs(), function (config) {
                        return (config && config.code == code);
                    });
                    if (config) {
                        return config.value;
                    }
                }
                return "";
            },

            getOrderData: function (key) {
                var self = this;
                var data = false;

                if (self.orderData()) {
                    //alert(JSON.stringify(self.orderData()));
                    data = self.orderData();
                    if (key) {
                        if (typeof data[key] != "undefined") {
                            data = data[key];
                        } else {
                            data = ""
                        }
                    }
                }
                return data;
            },

            isShowBarcode: function () {
                return (this.getConfigData('show_barcode') == 1) ? true : false;
            },
            isAutoPrint: function () {
                return (this.getConfigData('auto_print') == 1) ? true : false;
            },

            getFont: function () {
                return this.getConfigData('font_type');
            },
            getFooterHtml: function () {
                return this.getConfigData('footer_text');
            },
            getHeaderHtml: function () {
                return this.getConfigData('header_text');
            },
            hasHeaderHtml: function () {
                return (this.getConfigData('header_text')) ? true : false;
            },
            isShowCashierName: function () {
                return (this.getConfigData('show_cashier_name') == 1) ? true : false;
            },
            isShowComment: function () {
                return (this.getConfigData('show_comment') == 1 && this.getComment()) ? true : false;
            },
            isShowLogo: function () {
                return (this.getConfigData('show_receipt_logo') == 1 && this.getLogoPath()) ? true : false;
            },
            getLogoPath: function () {
                return this.getConfigData('logo');
            },
            getIncrementId: function () {
                return "#" + this.getOrderData('increment_id');
            },
            getCreatedDate: function () {
                return DateHelper.getDate(this.getOrderData('created_at'));
            },
            getCashierName: function () {
                return this.getOrderData('webpos_staff_name');
            },
            getStoreName: function () {
                return this.getOrderData('store_nameOnly');
            },
            getCreatedTime: function () {
                return DateHelper.getTime(this.getOrderData('created_at'));
            },
            getComment: function () {
                return this.getOrderData('customer_note');
            },
            getShipping: function () {
                return this.getOrderData('shipping_description');
            },
            getShippingHtml: function () {
                var getShippingHtml = this.getShipping() + "</br>";
                if (this.hasDeliveryDate()) {
                    getShippingHtml += "<strong class='printReceiptStrong'> Delivery Time : </strong>" + this.getDeliveryDate() + "</br>";
                }
                if (this.getDeliveryComment() != undefined && this.getDeliveryComment() != "") {
                    getShippingHtml += "<strong class='printReceiptStrong'> Comment : </strong>" + this.getDeliveryComment() + "</br>";
                }
                /*if (this.getDeliveryComment() != undefined && this.getDeliveryComment() != "") {
                    getShippingHtml += "<strong class='printReceiptStrong'> Kitchen Pickup Time : </strong>" + this.getOrderData('shipping_delivery_date') + "</br>";
                }*/
                if (this.getComment() != undefined && this.getComment() != "") {
                    getShippingHtml += "<strong class='printReceiptStrong'> Greeting Card  : </strong>" + this.getComment() + "</br>";
                }
                else
                    getShippingHtml += "<strong class='printReceiptStrong'> Greeting Card  : </strong> ............ </br>";
                return getShippingHtml;


            },
            getShippingAddressHtml: function () {
                var getShippingAddressHtml = '';
                this.regionHTML('');
                this.blockHTML('');
                this.avenueHTML('');
                this.streetHTML('');
                this.buildingHTML('');
                this.flatHTML('');
                this.receiverNameHTML('');
                this.receiverTelephoneHTML('');
                this.senderNameHTML('');

                if (typeof this.getOrderData().extension_attributes !== 'undefined' && this.getShipping().indexOf("Delivery") != -1) {
                    var address = this.getOrderData().extension_attributes.shipping_assignments[0].shipping.address;
                    if (address.region != "") {
                        getShippingAddressHtml += '<strong class="printReceiptStrong"> Area: </strong><span class="printReceiptspan">' + address.region + '</span></br>';
                        this.regionHTML(address.region);
                    }
                    if (address.company) {
                        getShippingAddressHtml += '<strong class="printReceiptStrong">Block: </strong><span class="printReceiptspan">' + address.company + '</span></br>';
                        this.blockHTML(address.company);
                    }
                    if (address.street) {
                        var street = address.street.split("\n");
                        if (typeof street[1] !== 'undefined') {
                            getShippingAddressHtml += '<strong class="printReceiptStrong">Avenue: </strong><span class="printReceiptspan">' + street[1] + '</span></br>';
                            var str = 'Avenue: ' + street[1];
                            this.blockHTML(street[1]);
                        }
                        if (typeof street[0] !== 'undefined') {
                            getShippingAddressHtml += '<strong class="printReceiptStrong">Street: </strong><span class="printReceiptspan">' + street[0] + '</span></br>';
                            var str = 'Street: ' + street[0];
                            this.streetHTML(street[0]);
                        }
                    }

                    if (address.city) {
                        getShippingAddressHtml += '<strong class="printReceiptStrong">Building: </strong><span class="printReceiptspan">' + address.city + '</span></br>';
                        this.buildingHTML(address.city);
                    }
                    if (address.postcode) {
                        getShippingAddressHtml += '<strong class="printReceiptStrong">Flat: </strong><span class="printReceiptspan">' + address.postcode + '</span></br>';
                        this.flatHTML(address.postcode);
                    }
                    if (address.is_gift === '1') {
                        if (address.receiver_name) {
                            getShippingAddressHtml += '<strong class="printReceiptStrong">Receiver Name: </strong> <span class="printReceiptspan">' + address.receiver_name + '</span></br>';
                            this.receiverNameHTML(address.receiver_name);
                        }
                        if (address.lastname) {
                            this.senderNameHTML(address.lastname);
                        }
                    }
                    else {
                        this.receiverNameHTML('');
                    }
                }
                return getShippingAddressHtml;
            },

            // START RYAN MODIFIED BY RYAN TO GET ARRIVAL TIME AND ADD COMMENT TO DELIVERY
            getDeliveryDate: function () {
                // var deliveryTime = this.getOrderData('webpos_delivery_date');
                var deliveryTime = this.getOrderData('shipping_arrival_date');
                return deliveryTime;
                //return (deliveryTime) ? DateHelper.getFullDatetime(deliveryTime) : '';
            },
            hasDeliveryDate: function () {

                // var deliveryTime = this.getOrderData('webpos_delivery_date');
                var deliveryTime = this.getOrderData('shipping_delivery_date');
                return (deliveryTime) ? true : false;
            },
            getOrderStatus: function () {
                var status = this.getOrderData('order_status');
                return status;
            },


            getDeliveryComment: function () {
                // var deliveryTime = this.getOrderData('webpos_delivery_date');
                var deliveryComment = this.getOrderData('shipping_arrival_comments');
                return deliveryComment;
            },
            // END RYAN MODIFIED    
            hasShipping: function () {
                return (this.getOrderData('shipping_amount') > 0) ? true : false;
            },
            getCustomerName: function () {
                var lname = ((this.getOrderData('customer_lastname') != null && this.getOrderData('customer_lastname') != undefined) ? this.getOrderData('customer_lastname') : "");
                return this.getOrderData('customer_firstname') + " " + lname;
            },
            getCustomerTelephone: function () {
                return this.getOrderData('customer_phone_original');
            },
            getCustomerEmail: function () {
                return this.getOrderData('billing_address').email;
            },
            hasCustomerName: function () {
                return (this.getOrderData('customer_firstname') || this.getOrderData('customer_lastname')) ? true : false;
            },
            getWebposChange: function () {
                return this.getOrderData('webpos_change') + " " + this.getOrderData('webpos_change');
            },
            hasWebposChange: function () {
                return (this.getOrderData('webpos_change') > 0) ? true : false;
            },
            getPayment: function () {
                //debugger;
                var payments = [];
                if (this.getOrderData('webpos_order_payments') && this.getOrderData('webpos_order_payments').length > 0) {
                    ko.utils.arrayForEach(this.getOrderData('webpos_order_payments'), function (payment) {
                        if (payment.payment_amount > 0) {
                            payments.push(payment);
                        }
                    });
                }
                return payments;
            },
            hasPayment: function () {
                return (this.getPayment() && this.getPayment().length > 0) ? true : false;
            },
            // Islam  OO 2018
            getItems: function () {
                var items = this.getOrderData('items');
                var itemsOptions = this.getOrderData('items_info_buy');
                var i = 0;
                //debugger;
                if (typeof itemsOptions.items != 'undefined') {
                    if (itemsOptions.items.length > 0) {
                        ko.utils.arrayForEach(itemsOptions.items, function (itemsOption) {
                            if (typeof itemsOption.options_label != 'undefined') {
                                items[i].options_label = new Object();
                                items[i].options_label = itemsOption.options_label;
                                if (itemsOption.optionsWithTitle && items[i].options_label != "") {
                                    var data = itemsOption.optionsWithTitle;
                                    var labels = "";
                                    data = data.filter(function (v) {
                                        return v.value.optionTitle != undefined;
                                    });
                                    if (data) {
                                        if ($.isArray(data)) {
                                            ko.utils.arrayForEach(data, function (label, index) {
                                                if (label && label.value && label.value.optionTitle != undefined) {
                                                    labels += ("<strong class='printReceiptStrong'>" + label.value.optionTitle + "</strong></br><span class='printReceiptspan'>" + label.value.value + "</span>");
                                                    if (index != data.length - 1)
                                                        labels += "</br>";
                                                }
                                            });
                                        } else {
                                            labels = data;
                                        }
                                        items[i].options_label = labels;
                                    }
                                }
                                items[i].options = new Object();
                                items[i].options = itemsOption.options;

                            }
                            i++;
                        });
                    }
                }
                return items;


                /*//alert(JSON.stringify(this.getOrderData('items')));

            	var items = this.getOrderData('items');
            	var itemsOptions = this.getOrderData('items_info_buy');
            	//alert(JSON.stringify(itemsOptions.items));
            	//items.options = new Object();

            	var i = 0;
            	if( typeof itemsOptions.items != 'undefined' ) {
	            	if(itemsOptions.items.length > 0){
	                    ko.utils.arrayForEach(itemsOptions.items, function(itemsOption) {

	                    	if( typeof itemsOption.options_label != 'undefined' ) {

	                    		items[i].options_label = new Object();
	                    		items[i].options_label = itemsOption.options_label;

	                    		items[i].options = new Object();
	                    		items[i].options = itemsOption.options;

	                    	}
	                    	i++;
	                    });
	                }
            	}

            	//alert(JSON.stringify(items));
                return items;
                */
                // Islam  OO 2018
                //return this.getOrderData('items');
                // Islam  OO 2018
            },
            getOrderTotals: function () {
                var self = this;
                var totals = [];

                if (self.totalsCode() && self.totalsCode().length > 0) {
                    ko.utils.arrayForEach(self.totalsCode(), function (data) {
                        var amount = self.getOrderData(data.code);
                        if (data.code == 'gift_voucher_discount' && Helper.isGiftCardEnable()) {
                            amount = -amount;
                        }
                        if (amount || (data.required && data.required == true)) {
                            var value = (data.isPrice == false) ? (amount + ' ' + data.valueLabel) : (self.formatPrice(amount));
                            var total = {
                                'label': data.title,
                                'value': self.orderIsGift() == true ? 'Paid' : value
                            };
                            totals.push(total);
                        }
                    });
                }
                return totals;
            },
            getCustomerAdditionalInfo: function () {
                var self = this;
                if (self.customerAdditionalInfomation() && self.customerAdditionalInfomation().length > 0) {
                    return self.customerAdditionalInfomation();
                }
                return [];
            },
            printReceipt: function () {
                var self = this;
                self.initDefaultData();
                var print_window = window.open('', 'print_offline', 'status=1,width=500,height=700');
                var html = self.toHtml();
                if (print_window) {
                    self.printWindow(print_window);
                    print_window.document.open();
                    print_window.document.write(html);
                    print_window.print();
                } else {
                    AddNoti(self.__("Your browser has blocked the automatic popup, please change your browser setting or print the receipt manually"), true, "warning", self.__('Message'));
                }
            },
            orderIsGift: function () {
                if (typeof this.getOrderData().extension_attributes !== 'undefined') {
                    var isgift = this.getOrderData().extension_attributes.shipping_assignments[0].shipping.address.is_gift;

                    return ((isgift == null || isgift == undefined) ? false : isgift);

                }
                return false;
            }

        });
    }
);
