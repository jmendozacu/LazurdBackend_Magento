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
    'model/checkout/cart',
    'model/checkout/checkout',
    'model/checkout/cart/items',
    'model/customer/current-customer',
    'model/customer/customer/edit-customer',
    'model/customer/customer/new-address',
    'action/customer/edit/save-form',
    'action/customer/change/show-create-customer-form',
    'action/customer/edit/edit-shipping-preview',
    'action/customer/edit/show-shipping-preview',
    'action/checkout/select-shipping-address',
    'model/appConfig',
    'eventManager',
    'helper/general',
    'lib/jquery/jquery.fullscreen'
], function (ko, $, Component,
    CartModel, CheckoutModel, Items, currentCustomer, editCustomerModel, newAddress,
    saveEditCustomerForm, showCreateCustomerForm,
    editShippingPreview, showShippingPreview, selectShipping,
    AppConfig, Event, Helper) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'ui/checkout/cart'
            },
            /**
             * Flag to show additional actions
             */
            showAdditional: ko.observable(false),
            /**
             * Flag to check the items component has been rendered
             */
            renderedItems: ko.observable(false),
            /**
             * Flag to check the totals component has been rendered
             */
            renderedTotals: ko.observable(false),
            /**
             * Current customer name
             */
            currentCustomerName: ko.pureComputed(function () {
                //alert(currentCustomer.fullName());
                return currentCustomer.fullName();
            }),
            /**
             * Current customer ID
             */
            currentCustomerId: ko.pureComputed(function () {
                //alert(CartModel.customerId());
                //console.log(CartModel);
                return CartModel.customerId();
            }),
            /**
             * Flag to check customer ID existing
             */
            isShowCustomerId: ko.pureComputed(function () {
                return (currentCustomer.customerId() != 0 || currentCustomer.customerId() != "");
            }),
            isSelectedCustomer: ko.pureComputed(function () {
                return (CartModel.customerId() != 0 && CartModel.customerId() != "" && CartModel.customerId() != undefined);
            }),

            /* Can edit customer or not*/
            isCanEditCustomer: ko.pureComputed(function () {
      
                return currentCustomer.customerId() != window.webposConfig.guestCustomerId;
            }),
            /**
             * Current page (cart/checkout)
             */
            currentPage: CartModel.currentPage,
            /**
             * Check if is on checkout page
             */
            isOnCheckoutPage: CartModel.isOnCheckoutPage,
            /**
             * Cart title (number of items in cart)
             */
            cartTitle: ko.pureComputed(function () {
                return "Cart (" + CartModel.totalItems() + ")";
            }),
            /**
             * is Gift Flag
             */
            isGift: CartModel.isGift,

            isGiftP: ko.pureComputed(function () {
                return CartModel.isGift();
            }),


            /**
             * Constructor
             */
            initialize: function () {
                this._super();
                var self = this;
                this.renderedPage = ko.pureComputed(function () {
                    return (self.renderedItems() && self.renderedTotals() && CartModel.loading() != true);
                });
                if (!this.currentPage()) {
                    this.currentPage(CartModel.PAGE.CART);
                }
                this.createdOrder = ko.pureComputed(function () {
                    return CheckoutModel.isCreatedOrder();
                });
                Event.observer('go_to_checkout_page', function () {
                    /*save cart 2 lan*/
                    if ((CartModel.checkButtonClick() == 3) || !Helper.isOnlineCheckout()) {
                        CartModel.checkButtonClick(1);
                        self.switchToCheckout();
                    } else {
                        Event.dispatch('check_button_click', '', true);
                    }
                    /*save cart 2 lan*/
                });
                Event.observer('go_to_cart_page', function () {
                    self.switchToCart();
                });
                Event.observer('start_new_order', function () {
                    self.switchToCart();
                    self.emptyCart();
                });
                Event.observer('save_cart_online_after', function (event, data) {
                    if (data && data.response && data.response.status) {
                        Event.dispatch('go_to_checkout_page', '', true);
                    }
                });
                // $('#chk_isGift').iosCheckbox();
                Event.observer('update_gift_work', function () {
                    var chk = $('#chk_isGift');
                    chk.trigger('click');
                });


            },
            /**
             * Animate container to checkout page
             */
            goToCheckoutPage: function () {
                var checkoutSection = $(AppConfig.ELEMENT_SELECTOR.CHECKOUT_SECTION);
                if (checkoutSection.length > 0) {
                    checkoutSection.addClass(AppConfig.CLASS.ACTIVE);
                    var mainContainer = $(AppConfig.MAIN_CONTAINER);
                    if (mainContainer.length > 0) {
                        var categoryWith = mainContainer.find(AppConfig.ELEMENT_SELECTOR.COL_LEFT).width();
                        mainContainer.css({
                            left: "-" + categoryWith + "px"
                        });
                    }
                    $('#popup-change-customer').addClass(AppConfig.CLASS.ACTIVE_ON_CHECKOUT);

                }
            },
            /**
             * Animate container to cart page
             */
            goToCartPage: function () {
                var checkoutSection = $(AppConfig.ELEMENT_SELECTOR.CHECKOUT_SECTION);
                if (checkoutSection.length > 0) {
                    checkoutSection.removeClass(AppConfig.CLASS.ACTIVE);
                    var mainContainer = $(AppConfig.MAIN_CONTAINER);
                    if (mainContainer.length > 0) {
                        mainContainer.css({
                            left: "0px"
                        });
                    }
                    $('#popup-change-customer').removeClass(AppConfig.CLASS.ACTIVE_ON_CHECKOUT);

                }
            },
            /**
             * Hide menu button
             */
            hideMenuButton: function () {
                var showMenuButton = $(AppConfig.ELEMENT_SELECTOR.SHOW_MENU_BUTTON);
                if (showMenuButton.length > 0) {
                    showMenuButton.hide();
                    showMenuButton.addClass(AppConfig.CLASS.HIDE);
                }
            },
            /**
             * Show menu button
             */
            showMenuButton: function () {
                var showMenuButton = $(AppConfig.ELEMENT_SELECTOR.SHOW_MENU_BUTTON);
                if (showMenuButton.length > 0) {
                    showMenuButton.show();
                    showMenuButton.removeClass(AppConfig.CLASS.HIDE);
                }

            },
            /**
             * Animate UI
             */
            transformInterface: function () {
                var self = this;
                switch (self.currentPage()) {
                    case CartModel.PAGE.CART:
                        self.goToCartPage();
                        self.showMenuButton();
                        break;
                    case CartModel.PAGE.CHECKOUT:
                        self.goToCheckoutPage();
                        self.hideMenuButton();
                        break;
                }
            },
            /**
             * Start switch to cart page
             */
            switchToCart: function () {
                this.currentPage(CartModel.PAGE.CART);
                this.transformInterface();
                var mainContainer = $(AppConfig.MAIN_CONTAINER);
                if (mainContainer.length > 0) {
                    mainContainer.addClass(AppConfig.CLASS.SHOW_MENU);
                }
                CheckoutModel.paymentCode('');
                CheckoutModel.selectedPayments([]);
                //1012018islam
                if ($(window).width() < 767)
                    this.GotoProductsInMobile();

            },



            GotoProductsInMobile: function () {
                //1012018islam
                $("#webpos_cart").hide();
                $("#product-list-wrapper").show();
                $("#webpos-notification").show();
                $(".show-menu-button").show();

            },
            /**
             * Start switch to checkout page
             */
            switchToCheckout: function () {
                if (Items.isEmpty()) {
                    return;
                } else {
                    this.currentPage(CartModel.PAGE.CHECKOUT);
                    this.transformInterface();
                    var mainContainer = $(AppConfig.MAIN_CONTAINER);
                    if (mainContainer.length > 0) {
                        mainContainer.removeClass(AppConfig.CLASS.SHOW_MENU);
                    }
                }
            },
            /**
             * Hide list actions
             */
            hideAddtitionalActions: function () {
                this.showAdditional(false);

            },
            /**
             * Show list actions
             */
            showAddtitionalActions: function () {
                this.showAdditional(true);

            },
            /**
             * After render
             */
            afterRenderCart: function () {

            },
            /**
             * Show form change customer
             */
            changeCustomer: function () {
                var commentPopup = $('#popup-change-customer');
                var elementPopupChangeCustomer = $('#popup-change-customer-order');
                if (elementPopupChangeCustomer)
                    elementPopupChangeCustomer.attr('data-reset', 'false');
                if (commentPopup.length > 0) {
                    commentPopup.addClass('fade-in');
                    commentPopup.posOverlay({
                        onClose: function () {
                            commentPopup.removeClass('fade-in');
                            $('.notification-bell').show();
                        }
                    });

                    $('.notification-bell').hide();
                    $('#c-button--push-left').hide();
                }


                Helper.dispatchEvent('checkout_customer_list_show_after', {});
            },
            /**
             * Show form edit customer
             */
            editCustomer: function () {
                // To do nothing while  no customer selected
                if (CartModel.customerId() == '') {
                    showCreateCustomerForm();
                    return;
                }
                else if(!this.isSelectedCustomer())
                {
                    showCreateCustomerForm();
                    return;
                }
                var self = this;
                if (!self.isOnCheckoutPage()) {
                    $('#form-edit-customer').removeClass('fade');
                    $('#form-edit-customer').addClass('fade-in');
                    $('#form-edit-customer').addClass('show');
                    $('#form-edit-customer').posOverlay({
                        onClose: function () {
                            $('#form-edit-customer').addClass('fade');
                            $('#form-edit-customer').removeClass('fade-in');
                            $('#form-edit-customer').removeClass('show');
                        }
                    });

                    $('.notification-bell').hide();
                    $('#c-button--push-left').hide();
                }
            },
            /**
             * Empty cart
             */
            emptyCart: function () {
                if (Helper.isOnlineCheckout() && CartModel.hasOnlineQuote()) {
                    CartModel.removeCartOnline();
                } else {
                    CartModel.emptyCart();
                }
                // $('#chk_isGift').iosCheckbox();           


            },
            /**
             * Show comment popup
             */
            showAddCommentPopup: function () {

                Event.dispatch('show_comment_popup', '');
                this.hideAddtitionalActions();


            },
            /**
             * Enter/exit full screen mode
             */
            toggleFullscreen: function () {
                $(document).toggleFullScreen();
            },

            isGiftChange: function (el, event) {
                
                this.isGift(event.target.checked);
                CartModel.isGift(event.target.checked);

                if (!event.target.checked) {
                    CartModel.giftAddres('');
                    var editCustomerForm = $('#form-edit-customer');
                    // editCustomerForm.addClass('fade-in');
                    // editCustomerForm.addClass('show');
                    // editCustomerForm.removeClass('fade');
                    editCustomerForm.addClass('fade');
                    editCustomerForm.removeClass('fade-in');
                    editCustomerForm.removeClass('show');
                    var currShippingAddressCode = document.forms['form-edit-customer'].elements['shipping-checkout'].value;
                    selectShipping(currShippingAddressCode);

                    //saveEditCustomerForm();
                }

            },



            /* Control UI show add address form*/
            showAddress: function () {
                if (!this.isShowCustomerId()) return;
                CartModel.giftBtnClicked(true);
                // if (CartModel.CheckoutModel().shippingAddress().is_gift == "1") {
                if (CartModel.giftAddres()) {
                    editCustomerModel.setShippingPreviewData(CartModel.giftAddres());
                    editShippingPreview();
                    return;
                }
                document.getElementById('form-customer-add-address-checkout').reset();


                //  if(this.isGift()){
                //     this.isGift(false);
                //     return;
                //  } else{
                //      this.isGift(true);
                //  }

                var editCustomerForm = $('#form-edit-customer');
                var addAddressForm = $('#form-customer-add-address-checkout');
                new RegionUpdater('country_id', 'region', 'region_id',
                    JSON.parse(window.webposConfig.regionJson), undefined, 'zip');
                document.forms['form-customer-add-address-checkout'].elements['is_gift'].value = "1";
                editCustomerForm.addClass('fade');
                editCustomerForm.removeClass('fade-in');
                editCustomerForm.removeClass('show');


                addAddressForm.addClass('fade-in');
                addAddressForm.addClass('show');
                addAddressForm.removeClass('fade');

                newAddress.addressTitle(Helper.__('Add Gift Address'));
                newAddress.resetAddressForm();
                document.forms['form-customer-add-address-checkout'].elements['is_gift'].value = "1";
                // newAddress.addressTitle(Helper.__('Add Address'));
                // newAddress.resetAddressForm();
                // newAddress.firstName(currentCustomer.data().firstname);
                // newAddress.lastName(currentCustomer.data().lastname);


            },
            afterRender: function () {
                // $('#chk_isGift').iosCheckbox();

            },
            resetinMobile: function () {
                if ($(window).width() < 367) {
                    $("#product-list-wrapper").hide();
                    $("#webpos-notification").hide();
                    $(".show-menu-button").hide();
                    $("#webpos_cart").show();
                }
            },

        });
    });