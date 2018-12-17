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
        // 'jquery/jquery-ui-timepicker-addon',
        'posComponent',
        'model/checkout/checkout',
        'model/checkout/checkout/payment',
        'model/checkout/cart',
        'model/checkout/cart/totals',
        'helper/general',
        'model/appConfig'
    ],
    function ($, ko, Component, CheckoutModel, PaymentModel, CartModel, Totals, Helper, AppConfig) {
        "use strict";
        return Component.extend({
            shippingArrivalDate: ko.observable(''),
            shippingArrivalComments: ko.observable(''),

            shippingArrivalTimeSlot: ko.observableArray(window.webposConfig.deliverydate),
            selectedShippingArrivalTime: ko.observable(),

            defaults: {
                template: 'ui/checkout/checkout'
            },
            initialize: function(){
                this._super();
                var self = this;

                self.cannotAddPayment = ko.pureComputed(function(){
                    return (PaymentModel.showCcForm() || CheckoutModel.remainTotal() <= 0 || !PaymentModel.hasSelectedPayment())?true:false;
                });
                Helper.observerEvent('go_to_checkout_page', function(){
                    var autoCheckPromotion =  Helper.isAutoCheckPromotion();
                    if(autoCheckPromotion == true){
                        CheckoutModel.autoCheckPromotion();
                    }

                    $('#shipping_arrival_date').datepicker();
                });
                Helper.observerEvent('hide_payment_popup', function(){
                    $(AppConfig.ELEMENT_SELECTOR.CHECKOUT_ADD_PAYMENT_POPUP).posOverlay({
                        onClose: function(){
                            self.showPaymentPopup(false);
                        }
                    }).close();
                });
            },

            showPaymentPopup: ko.observable(false),
            loading: ko.pureComputed(function(){
                return (CheckoutModel.loading() == true || CheckoutModel.autoCheckingPromotion() == true)?true:false;
            }),
            cartTotal: ko.pureComputed(function(){
                return Helper.convertAndFormatPrice((Totals.grandTotal()) ? Totals.grandTotal():0);
            }),
            remainTotal: ko.pureComputed(function(){
                return Helper.convertAndFormatPrice((CheckoutModel.remainTotal()) ? Math.abs(CheckoutModel.remainTotal()) : 0);
            }),
            selectedShippingTitle: ko.pureComputed(function(){
                return (CheckoutModel.selectedShippingTitle())?CheckoutModel.selectedShippingTitle():"";
            }),
            selectedShippingCode: ko.pureComputed(function(){
                return (CheckoutModel.selectedShippingCode())?CheckoutModel.selectedShippingCode():"";
            }),
            selectedShippingPrice: ko.pureComputed(function(){
                return (CheckoutModel.selectedShippingPrice())?CheckoutModel.selectedShippingPrice():"";
            }),
            shippingHeader: ko.pureComputed(function() {
                return "Shipping: "+CheckoutModel.selectedShippingTitle();
            }),
            shipAble: ko.pureComputed(function(){
                return (CartModel.isVirtual())?false:true;
            }),
            checkoutButtonLabel: ko.pureComputed(function(){
                var label = Helper.__("Place Order");
                if(Helper.toNumber(CheckoutModel.remainTotal()) > 0 && CheckoutModel.createInvoice() != true
                    && CheckoutModel.selectedPayments().length > 0){
                    label = Helper.__("Mark as partial");
                }
                return label;
            }),
            remainTitle: ko.pureComputed(function () {
                if(CheckoutModel.remainTotal() > 0)
                    return 'Remain money';
                return 'Expected Change';
            }),
            canPaid: ko.pureComputed(function(){
                var canCreate = true;
                if(CheckoutModel.remainTotal() > 0) {
                    canCreate = false;
                }
                CheckoutModel.createInvoice(canCreate);
                var createInvoiceButton = $(AppConfig.ELEMENT_SELECTOR.CHECKOUT_CREATE_INVOICE_BUTTON);
                if(createInvoiceButton.length > 0 && createInvoiceButton.find(AppConfig.ELEMENT_SELECTOR.UI_SELECT_INPUT) != undefined){
                    var bootstrapSlide = createInvoiceButton.find(AppConfig.ELEMENT_SELECTOR.UI_SELECT_INPUT);
                    if(canCreate == true){
                        bootstrapSlide.addClass(AppConfig.CLASS.CHECKED);
                    }else{
                        bootstrapSlide.removeClass(AppConfig.CLASS.CHECKED);
                    }
                }
                var needApproval = (CheckoutModel.cartDiscountAmount() < 0)?true:false;
                var needApproveButton = $(AppConfig.ELEMENT_SELECTOR.CHECKOUT_NEED_APPROVE_BUTTON);
                if(needApproveButton.length > 0 && needApproveButton.find(AppConfig.ELEMENT_SELECTOR.UI_SELECT_INPUT) != undefined){
                    var bootstrapSlide = needApproveButton.find(AppConfig.ELEMENT_SELECTOR.UI_SELECT_INPUT);
                    if(needApproval == true){
                        bootstrapSlide.addClass(AppConfig.CLASS.CHECKED);
                    }else{
                        bootstrapSlide.removeClass(AppConfig.CLASS.CHECKED);
                    }
                }
                CheckoutModel.needApproval(needApproval);
                return canCreate;
            }),
            missedReferenceNumber: ko.observable(false),
            placeOrder: function(){
                var self = this;
                CheckoutModel.shippingArrivalDate(self.shippingArrivalDate());
                CheckoutModel.shippingArrivalComments(self.shippingArrivalComments());
                CheckoutModel.shippingArrivalTimeSlot(self.selectedShippingArrivalTime());

               
                if(CheckoutModel.placingOrder() == true){
                    Helper.alert({
                        priority: "warning",
                        title: "Message",
                        message: "Placing order, please wait..."
                    });
                    return false;
                }
                // Islam Kuywait 2018
                
                if(CheckoutModel.shippingArrivalDate() == undefined || CheckoutModel.shippingArrivalDate() == ""){
                    
                    Helper.alert({
                        priority: "danger",
                        title: "Message",
                        message: "Please select the Arrival Date"
                    });
                    return false;
                }
                if(CheckoutModel.shippingArrivalTimeSlot() == undefined || CheckoutModel.shippingArrivalTimeSlot() == ""){
                    
                    Helper.alert({
                        priority: "danger",
                        title: "Message",
                        message: "Please select the Arrival Time"
                    });
                    return false;
                }
                
             
                if((!CheckoutModel.selectedPayments() || CheckoutModel.selectedPayments().length <= 0 )
                    && Totals.grandTotal() > 0 && !CheckoutModel.paymentCode()){
                      
              
                    Helper.alert({
                        priority: "danger",
                        title: "Message",
                        message: "Please select the payment method"
                    });
                    return false;
                }
                
                if(CheckoutModel.selectedPayments() ){
                  
                    var items = CheckoutModel.selectedPayments();
                    // Added By Ryan to add validation to Reference Number
                    ko.utils.arrayForEach(items, function(item, index) {
                        if(item.is_reference_number == 1 && (!item.reference_number || item.reference_number == 'undefined' ) ){
                            Helper.alert({
                                priority: "danger",
                                title: "Message",
                                message: "Please Add Reference Number To the "+ item.title +"  payment method"
                            });
                            self.missedReferenceNumber(true);
                            return false;
                        }
                    });
                }

                if(self.missedReferenceNumber())
                {
                    self.missedReferenceNumber(false);
                    return false;
                }


                if(!CheckoutModel.selectedShippingCode()){
                    CheckoutModel.useWebposShipping();
                }

                // if (!this.validateForm('#form-checkout-creditcard')) {
                //     return;
                // }
                self.shippingArrivalDate('');
                self.shippingArrivalComments('');
                self.selectedShippingArrivalTime('');
                // Islam Kuywait 2018 _3
                if(Helper.isOnlineCheckout()){
                    if(!CartModel.hasOnlineQuote()){
                        Helper.alert({
                            priority: "danger",
                            title: "Message",
                            message: "The quote does not exist for online checkout"
                        });
                        return false;
                    }
                    CheckoutModel.placeOrderOnline();
                    return true;
                }
                if(!CartModel.canCheckoutStorecredit()){
                    Helper.alert({
                        priority: "danger",
                        title: "Message",
                        message: "Please select customer!"
                    });
                    return false;
                }

                CheckoutModel.createOrder();

            },
            initCheckboxStyle: function(){
                $(".ios").iosCheckbox();
            },
            afterRenderCheckout: function(){
                CheckoutModel.initDefaultData();
            },
            needApproval: function(data,event){
                var needApproval = (event.target.checked) ? true : false;
                CheckoutModel.needApproval(needApproval);
            },
            needWaitting: function(data,event){
                var needWaitting = (event.target.checked) ? true : false;
                CheckoutModel.needWaitting(needWaitting);
            },

            // changeDeliveryTime: function(data,event){
            //     var shippingArrivalTimeSlot = (event.target.value);
            //     console.log(shippingArrivalTimeSlot);
            //     CheckoutModel.shippingArrivalTimeSlot(shippingArrivalTimeSlot);
            // },

            createInvoice: function(data,event){
                var createInvoice = (event.target.checked) ? true : false;
                CheckoutModel.createInvoice(createInvoice);
            },
            createShipment: function(data,event){
                var createShipment = (event.target.checked)?true:false;
                CheckoutModel.createShipment(createShipment);
            },
            validateForm: function (form) {
                return $(form).validation() && $(form).validation('isValid');
            },
            addMorePayments: function () {
                var self = this;
                self.showPaymentPopup(true);
                $(AppConfig.ELEMENT_SELECTOR.CHECKOUT_ADD_PAYMENT_POPUP).posOverlay({
                    onClose: function(){
                        self.showPaymentPopup(false);
                    }
                });
            },
            showPayments: ko.pureComputed(function(){
                if(Totals.grandTotal() > 0 || Totals.hasSpecialDiscount() == true)
                    return true;
                return false;
            }),
            payAble: function () {
                if(Totals.grandTotal() > 0)
                    return true;
                return false;
            }
        });
    }
);
