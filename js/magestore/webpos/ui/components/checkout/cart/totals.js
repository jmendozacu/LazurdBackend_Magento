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
define([
    'ko',
    'jquery',
    'posComponent',
    'model/checkout/checkout',
    'model/checkout/cart',
    'model/checkout/cart/totals',
    'model/checkout/cart/items',
    'helper/alert',
    'helper/general',
    'eventManager',
    'action/cart/hold',
    'helper/pole'
], function (ko, $, Component, CheckoutModel, CartModel, Totals, Items, Alert, Helper, Event, Hold, poleDisplay) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'ui/checkout/cart/totals'
        },
        /*save cart 2 lan*/
        initialize: function () {
            this._super();
            var self = this;
            Event.observer('check_button_click', function () {
                self.buttonClick({code: Totals.CHECKOUT_BUTTON_CODE})
            });
        },
        /*save cart 2 lan*/
        isZeroTotal: ko.pureComputed(function () {
            return (Totals.grandTotal()) ? false : true;
        }),
        isOnCartPage: ko.pureComputed(function () {
            return (CartModel.currentPage() == CartModel.PAGE.CART) ? true : false;
        }),
        isOnCheckoutPage: ko.pureComputed(function () {
            return (CartModel.currentPage() == CartModel.PAGE.CHECKOUT) ? true : false;
        }),
        createdOrder: ko.pureComputed(function () {
            return CheckoutModel.isCreatedOrder();
        }),
        getAdditionalInfo: ko.pureComputed(function () {
            return Totals.additionalInfo();
        }),
        displayTotals: Totals.displayTotals,

        getButtons: function () {
            return Totals.getButtons();
        },
        totalItemClick: function (totalItem, event) {
            if (this.createdOrder() == true) {
                return false;
            }
            
            var classes = event.target.getAttribute("class");
            if (totalItem.code() == Totals.ADD_DISCOUNT_TOTAL_CODE || totalItem.code() == Totals.DISCOUNT_TOTAL_CODE) {
                if (Items.isEmpty()) {
                    
                    Alert({
                        priority: "warning",
                        title: "Warning",
                        message: "Please add item(s) to cart!"
                    });

                } else {
                    if (!classes || (classes && classes.indexOf("icon-iconPOS-remove") < 0)) {
                        Event.dispatch('show_cart_discount_popup', '');
                    }
                }
            }
        },
        buttonClick: function (button) {

			var cust=jQuery('.add-customer .icon-iconPOS-change-customer').html();
			var guest=jQuery('.add-customer .title-header-page').html();
			// alert(guest);
			if(cust==undefined && guest==undefined)
			{
				  Alert({
                        priority: "warning",
                        title: "Warning",
                        message: "Please Select Customer"
                    });
                    return;
			}
            
            if(CartModel.isGift())
                CartModel.CheckoutModel().shippingAddress(CartModel.giftAddres());
            if(CartModel.isGift() && (CartModel.CheckoutModel().shippingAddress().is_gift != "1") ){
                Alert({
                    priority: "warning",
                    title: "Warning",
                    message: "Please add Gift Address!"
                });
                return;
            }
           
            poleDisplay('', 'Total: ' + Helper.convertAndFormatPrice(Totals.grandTotal()));
            if (button.code == Totals.HOLD_BUTTON_CODE) {
                
                if (Items.isEmpty()) {
                    Alert({
                        priority: "warning",
                        title: "Warning",
                        message: "Please add item(s) to cart!"
                    });
                    return;
                } else {
                    Hold.execute();
                    return;
                }
            }
           
            if (button.code == Totals.CHECKOUT_BUTTON_CODE) {
                
                if (Items.isEmpty()) {
                    Alert({
                        priority: "warning",
                        title: "Warning",
                        message: "Please add item(s) to cart!"
                    });
                    return;
                } else {
                    if (Helper.isOnlineCheckout()) {
                        /*save cart 2 lan*/
                        if (CartModel.checkButtonClick() == 1) {
                            CartModel.checkButtonClick(2);
                        } else {
                            CartModel.checkButtonClick(3);
                        }
                        /*save cart 2 lan*/
                        CartModel.saveCartBeforeCheckoutOnline();
                    } else {
                        Event.dispatch('go_to_checkout_page', '', true);
                    }
                    return;
                }
            }
        
            

            if (button.code == Totals.BACK_CART_BUTTON_CODE) {
                Event.dispatch('go_to_cart_page', '', true);
            }
        },
        removeDiscount: function () {
            Event.dispatch('remove_cart_discount', '');
        },
        removeTotal: function (el) {
            if (el.actions() && el.actions().remove) {
                if (typeof el.actions().remove == 'string') {
                    if (typeof this[el.actions().remove] == 'function') {
                        this[el.actions().remove]();
                    }
                }
                if (typeof el.actions().remove == 'function') {
                    el.actions().remove();
                }
            }
        }
    });
});