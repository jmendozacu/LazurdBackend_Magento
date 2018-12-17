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
        'model/checkout/cart/items',
        'model/checkout/cart/totals',
        'eventManager',
        'dataManager',
        'model/resource-model/magento-rest/checkout/cart',
        'model/checkout/tax/calculator',
        'helper/pole',
        'helper/general'
    ],
    function ($, ko, Items, Totals, Event, DataManager, CartResource, TaxCalculator, poleHelper, generalHelper) {
        "use strict";
        var CartModel = {
            loading: ko.observable(),
            currentPage: ko.observable(),
            customerId: ko.observable(''),
            customerGroup: ko.observable(''),
            isGift: ko.observable(),
            dob: ko.observable(''),
            phone: ko.observable(''),
            customerData: ko.observable({}),
            CheckoutModel: ko.observable(),
            billingAddress: ko.observable(),
            shippingAddress: ko.observable(),
            giftAddres: ko.observable(),
            hasErrors: ko.observable(false),
            errorMessages: ko.observable(),
            /*save cart 2 lan*/
            checkButtonClick: ko.observable(1),
            /*save cart 2 lan*/
            isGift: ko.observable(false),
            /**
             * Flag to alert add gift address button clicking
             */
            giftBtnClicked: ko.observable(false),
            GUEST_CUSTOMER_NAME: "Guest",
            BACK_CART_BUTTON_CODE: "back_to_cart",
            CHECKOUT_BUTTON_CODE: "checkout",
            HOLD_BUTTON_CODE: "hold",
            PAGE: {
                CART: "cart",
                CHECKOUT: "checkout"
            },
            KEY: {
                QUOTE_INIT: 'quote_init',
                ITEMS: 'items',
                SHIPPING: 'shipping',
                PAYMENT: 'payment',
                TOTALS: 'totals',
                QUOTE_ID: "quote_id",
                TILL_ID: "till_id",
                CURRENCY_ID: "currency_id",
                CUSTOMER_ID: "customer_id",
                CUSTOMER_DATA: "customer_data",
                BILLING_ADDRESS: "billing_address",
                SHIPPING_ADDRESS: "shipping_address",
                STORE_ID: "store_id",
                STORE: "store",
            },
            DATA: {
                STATUS: {
                    SUCCESS: '1',
                    ERROR: '0'
                }
            },
            initialize: function () {
                var self = this;
                self.initObserver();
                return self;
            },
            initObserver: function () {
                var self = this;
                self.isOnCheckoutPage = ko.pureComputed(function () {
                    return (self.currentPage() == self.PAGE.CHECKOUT) ? true : false;
                });
                Event.observer('init_quote_online_after', function (event, response) {
                    if (response && response.data) {
                        self.saveQuoteData(response.data);
                    }
                });
            },
            emptyCart: function () {
                var self = this;
                Items.items.removeAll();
                self.removeCustomer();
                Totals.shippingData("");
                Totals.shippingFee(0);
                Totals.updateShippingAmount(0);
                Totals.updateDiscountTotal();
                Event.dispatch('cart_empty_after', '');
                self.resetQuoteInitData();
                poleHelper('', 'Total: ' +
                    generalHelper.convertAndFormatPrice(Totals.grandTotal()));
                self.giftAddres('');
                // Edit by 17032018 Islam ELgarhy
                localStorage.removeItem("OrderIsEdit");
                localStorage.removeItem("OrderIsEditId");
                window.webposConfig["OrderIsEdit"] = false;
                window.webposConfig["OrderIsEditId"] = null;
                // Ryan not Copy To server _ I.Elgarhy
                window.webposConfig["editedORderDiscount"] =  null;
                // Edit by 17032018 Islam ELgarhy*/
            },
            // Islam Elgarhy For Edit Order 7/2018
            emptyCartItems: function () {
                var self = this;
                Items.items.removeAll();
                Totals.shippingData("");
                Totals.shippingFee(0);
                Totals.updateShippingAmount(0);
                Totals.updateDiscountTotal();
                //Event.dispatch('cart_empty_after', '');
                self.resetQuoteInitData();
                poleHelper('', 'Total: ' +
                    generalHelper.convertAndFormatPrice(Totals.grandTotal()));
                self.giftAddres('');
            },
            // Islam Elgarhy For Edit Order 7/2018
            addCustomer: function (data) {
                this.customerData(data);
                this.customerId(data.id);
                this.customerGroup(data.group_id);
                this.dob(data.dob);
                this.phone(data.phone);
                this.collectTierPrice();
                poleHelper('', 'Total: ' +
                    generalHelper.convertAndFormatPrice(Totals.grandTotal()));
            },
            removeCustomer: function () {
                var self = this;
                self.customerId("");
                self.customerGroup("");
                self.customerData({});
                self.dob('');
                self.phone('');
                self.collectTierPrice();
                self.isGift(false);
                self.giftBtnClicked(false);

                Event.dispatch('cart_remove_customer_after', {
                    guest_customer_name: self.GUEST_CUSTOMER_NAME
                });
                poleHelper('', 'Total: ' +
                    generalHelper.convertAndFormatPrice(Totals.grandTotal()));
            },
            removeItem: function (itemId) {
                Items.removeItem(itemId);
                if (Items.items().length == 0) {
                    Totals.updateShippingAmount(0);
                }
                Event.dispatch('collect_totals', '');
                Event.dispatch('cart_item_remove_after', Items.items());
                poleHelper('', 'Total: ' +
                    generalHelper.convertAndFormatPrice(Totals.grandTotal()));
            },
            addProduct: function (data) {
                var self = this;
                if (self.loading()) {
                    return false;
                }
                var validate = true;
  // Islam  OO 2018
/*
                if (typeof data.options == 'undefined') {

                    data.options = new Object();

                    var rand = Math.floor((Math.random() * 100) + 1); //rand between 1 to 100
                    data.hasOption = 1;
                    data.has_options = 1;

                    data.options.code = rand;
                    data.options.value = rand;

                    data.options_label = '';

                    if (typeof data.productObject == 'undefined') {
                        data.productObject = new Object();
                    }

                    data.productObject.has_options = 1;
                    data.productObject.options_container = "container1";

                    data.productObject.selected_options = new Object();
                    data.productObject.selected_options.id = rand;
                    data.productObject.selected_options.value = rand;

                    data.productObject.options_label = new Object();
                    data.productObject.options_label.id = rand;
                    data.productObject.options_label.value = rand;

                    data.productObject.custom_options = new Object();
                    data.productObject.custom_options.option_id = rand;
                    data.productObject.custom_options.product_id = data.product_id;
                    data.productObject.custom_options.type = "field";
                    data.productObject.custom_options.is_require = false;
                    data.productObject.custom_options.sku = null;
                    data.productObject.custom_options.max_characters = 0;
                    data.productObject.custom_options.file_extension = null;
                    data.productObject.custom_options.image_size_x = null;
                    data.productObject.custom_options.image_size_y = null;
                    data.productObject.custom_options.sort_order = 0;
                    data.productObject.custom_options.default_title = "Test";
                    data.productObject.custom_options.store_title = "Test";
                    data.productObject.custom_options.title = "Test";
                    data.productObject.custom_options.default_price = "0.0000";
                    data.productObject.custom_options.default_price_type = "fixed";
                    data.productObject.custom_options.store_price = "0.0000";
                    data.productObject.custom_options.store_price_type = "fixed";
                    data.productObject.custom_options.price = "0.0000";
                    data.productObject.custom_options.price_type = "fixed";
                }

                //alert(JSON.stringify(data));
                //alert(data.qty + ' cart');
*/
// Islam  OO 2018
                var item = Items.getAddedItem(data);

                if (item !== false) {
                    var dataToValidate = item.getData();
                    if (dataToValidate.product_id != "customsale" && data.product_type != "bundle") {
                        dataToValidate.qty += data.qty;
                        dataToValidate.customer_group = self.customerGroup();
                        // validate = ObjectManager.get('model/catalog/product').validateQtyInCart(dataToValidate);
                    }
                    //alert(dataToValidate.qty);
                } else {
                    if (data.product_id != "customsale" && data.product_type != "bundle") {
                        data.customer_group = self.customerGroup();
                        if (data.minimum_qty && data.qty < data.minimum_qty) {
                            data.qty = data.minimum_qty;
                        }
                        if (data.maximum_qty && data.maximum_qty > 0 && data.qty > data.maximum_qty) {
                            data.qty = data.maximum_qty;
                        }
                        // validate = ObjectManager.get('model/catalog/product').validateQtyInCart(data);
                    }
                }
                if (validate) {
                    data = self.collectTaxRate(data);
                    Items.addItem(data);
                    Event.dispatch('collect_totals', '');
                    self.collectTierPrice();
                }

                poleHelper(data.sku + ' +' + generalHelper.convertAndFormatPrice(parseFloat(data.unit_price) * parseFloat(data.qty)), 'Total: ' +
                    generalHelper.convertAndFormatPrice(Totals.grandTotal()));
            },
            updateItem: function (itemId, key, value) {
                var validate = true;
                var item = Items.getItem(itemId);
                if (item) {
                    if (key == "qty") {
                        var data = item.getData();
                        data.qty = value;
                        if (data.product_id != "customsale" && data.product_type != "bundle") {
                            data.customer_group = this.customerGroup();
                            // validate = ObjectManager.get('model/catalog/product').validateQtyInCart(data);
                        }
                        if (data.product_id == "customsale") {
                            value = (value > 0) ? value : 1;
                        }

                        poleHelper(data.sku + ' - ' + 'Qty: ' + data.qty, '');
                    }
                    if (validate) {
                        Items.setItemData(itemId, key, value);
                        Event.dispatch('collect_totals', '');
                    }

                }

            },
            getItemData: function (itemId, key) {
                return Items.getItemData(itemId, key);
            },
            getItemsInfo: function () {
                var itemsInfo = [];
                if (Items.items().length > 0) {
                    //alert(JSON.stringify(Items.items()));
                    ko.utils.arrayForEach(Items.items(), function (item) {
                        //alert(JSON.stringify(item.getInfoBuyRequest()));
                        itemsInfo.push(item.getInfoBuyRequest());
                    });
                }
                return itemsInfo;
            },
            getItemsDataForOrder: function () {
                var itemsData = [];
                if (Items.items().length > 0) {
                    ko.utils.arrayForEach(Items.items(), function (item) {
                        itemsData.push(item.getDataForOrder());
                    });
                }
                return itemsData;
            },
            getItemsInitData: function () {
                var itemsData = [];
                if (Items.items().length > 0) {
                    ko.utils.arrayForEach(Items.items(), function (item) {
                        itemsData.push(item.getData());
                    });
                }
                return itemsData;
            },
            isVirtual: function () {
                var isVirtual = true;
                if (Items.items().length > 0) {
                    var notVirtualItem = ko.utils.arrayFilter(Items.items(), function (item) {
                        return item.is_virtual() == false;
                    });
                    isVirtual = (notVirtualItem.length > 0) ? false : true;
                }
                return isVirtual;
            },
            totalItems: function () {
                return Items.totalItems();
            },
            totalShipableItems: function () {
                return Items.totalShipableItems();
            },
            collectTaxRate: function (data) {
                var self = this;
                var calculateTaxBaseOn = window.webposConfig["tax/calculation/based_on"];
                var address = (calculateTaxBaseOn == 'shipping') ? self.CheckoutModel().shippingAddress() : self.CheckoutModel().billingAddress();

                data.tax_rate = TaxCalculator.getProductTaxRate(data.tax_class_id, self.customerGroup(), address);
                return data;
            },
            reCollectTaxRate: function () {
                var self = this;
                if (Items.items().length > 0) {
                    var calculateTaxBaseOn = window.webposConfig["tax/calculation/based_on"];
                    var address = (calculateTaxBaseOn == 'shipping') ? self.CheckoutModel().shippingAddress() : self.CheckoutModel().billingAddress();
                    ko.utils.arrayForEach(Items.items(), function (item) {
                        var taxrate = TaxCalculator.getProductTaxRate(item.tax_class_id(), self.customerGroup(), address);
                        self.updateItem(item.item_id(), 'tax_rate', taxrate);
                    });
                }
            },
            collectTierPrice: function () {
                var self = this;
                if (Items.items().length > 0) {
                    var hasTierPriceItems = ko.utils.arrayFilter(Items.items(), function (item) {
                        return (item.tier_prices()) ? true : false;
                    });
                    ko.utils.arrayForEach(hasTierPriceItems, function (item) {
                        var tier_prices = item.tier_prices();
                        var itemQty = item.qty();
                        var tier_price = false;
                        if (tier_prices) {
                            var validTierPrice = ko.utils.arrayFirst(tier_prices, function (data) {
                                return (
                                    ((self.customerGroup() == data.cust_group) || data.all_groups == '1') &&
                                    (itemQty >= data.price_qty)
                                );
                            });
                            if (validTierPrice) {
                                tier_price = validTierPrice.price;
                            }
                        }
                        self.updateItem(item.item_id(), 'tier_price', tier_price);
                    });
                }
            },
            validateItemsQty: function () {
                var self = this;
                var error = [];
                if (Items.items().length > 0) {
                    ko.utils.arrayForEach(Items.items(), function (item) {
                        var data = item.getData();
                        if (data.product_id != "customsale" && data.product_type != "bundle") {
                            data.customer_group = self.customerGroup();
                            // var validate = ObjectManager.get('model/catalog/product').checkStockItemsInCart(data);
                            // if(validate !== true){
                            //     error.push(validate);
                            // }
                        }
                    });
                }
                return (error.length > 0) ? error : true;
            },
            getItemChildsQty: function () {
                var qtys = [];
                if (Items.items().length > 0) {
                    ko.utils.arrayForEach(Items.items(), function (item) {
                        var data = item.getData();
                        if (data.product_id != "customsale") {
                            if (data.product_type == "bundle") {
                                if (data.bundle_childs_qty) {
                                    ko.utils.arrayForEach(data.bundle_childs_qty, function (option) {
                                        qtys.push({
                                            id: option.code,
                                            qty: option.value
                                        });
                                    });
                                }
                            } else {
                                if (data.child_id) {
                                    qtys.push({
                                        id: data.child_id,
                                        qty: data.qty
                                    });
                                } else {
                                    qtys.push({
                                        id: data.product_id,
                                        qty: data.qty
                                    });
                                }
                            }
                        }
                    });
                }
                return qtys;
            },
            getQtyInCart: function (productId) {
                var qty = 0;
                if (productId && Items.items().length > 0) {
                    ko.utils.arrayForEach(Items.items(), function (item) {
                        if (item.getData('product_id') == productId) {
                            qty += item.getData('qty');
                        }
                    });
                }
                return qty;
            },
            hasStorecredit: function () {
                if (Items.items().length > 0) {
                    var storecreditItem = ko.utils.arrayFirst(Items.items(), function (item) {
                        return (item.product_type() == "customercredit");
                    });
                    if (storecreditItem) {
                        return true;
                    }
                }
                return false;
            },
            canCheckoutStorecredit: function () {
                var hasStorecredit = this.hasStorecredit();
                if (hasStorecredit && this.customerId() == '') {
                    return false;
                }
                return true;
            },
            getQuoteCustomerParams: function () {
                var self = this;
                return {
                    customer_id: self.customerId(),
                    billing_address: self.billingAddress(),
                    shipping_address: self.shippingAddress()
                };
            },
            resetQuoteInitData: function () {
                var self = this;
                var data = {
                    quote_id: '',
                    customer_id: self.customerId()
                };
                self.saveQuoteData(data);
            },
            getCustomerInitParams: function () {
                var self = this;
                return {
                    customer_id: DataManager.getData(self.KEY.CUSTOMER_ID),
                    billing_address: DataManager.getData(self.KEY.BILLING_ADDRESS),
                    shipping_address: DataManager.getData(self.KEY.SHIPPING_ADDRESS),
                    data: DataManager.getData(self.KEY.CUSTOMER_DATA)
                };
            },
            getQuoteInitParams: function () {
                var self = this;
                return {
                    quote_id: DataManager.getData(self.KEY.QUOTE_ID),
                    store_id: DataManager.getData(self.KEY.STORE_ID),
                    customer_id: DataManager.getData(self.KEY.CUSTOMER_ID),
                    currency_id: DataManager.getData(self.KEY.CURRENCY_ID),
                    till_id: DataManager.getData(self.KEY.TILL_ID)
                };
            },
            /**
             * Save cart only - not distch events
             * @returns {*}
             */
            saveCartOnline: function () {
                var self = this;
                var params = self.getQuoteInitParams();
                params.items = self.getItemsInfo();
                /*Edit by 24122017 Islam ELgarhy*/
                if (self.CheckoutModel().cartDiscountCause() != "" && self.CheckoutModel().cartDiscountCause() != undefined) {
                    for (var i = 0; i < params.items.length; i++) {
                        params.items[i].discount_cause = self.CheckoutModel().cartDiscountCause();
                        params.items[i].discount_by = self.CheckoutModel().cartDiscountBy();
                        //alert(params.items[i].discount_cause);
                    }
                }
                /*Edit by 24122017 Islam ELgarhy*/
                params.customer = self.getQuoteCustomerParams();
                params.section = self.KEY.QUOTE_INIT;
                self.loading(true);
                var apiRequest = $.Deferred();
                CartResource().setPush(true).setLog(false).saveCart(params, apiRequest);
                apiRequest.done(function (response) {
                    if (response.status == self.DATA.STATUS.ERROR && response.messages) {
                        if (generalHelper.getBrowserConfig("webpos/general/ignore_checkout") == '0') {
                            self.hasErrors(true);
                        }
                        self.errorMessages(response.messages);
                    } else {
                        self.hasErrors(false);
                        self.errorMessages('');
                    }
                }).always(function () {
                    self.loading(false);

                });
                return apiRequest;
            },
            /**
             * Save cart and dispatch events
             * @param saveBeforeRemove
             * @returns {*}
             */
            saveCartBeforeCheckoutOnline: function (saveBeforeRemove) {
             
                // Edit by 17032018 Islam ELgarhy 
                if (window.webposConfig["OrderIsEdit"] == true) {
                    localStorage.setItem("OrderIsEdit", window.webposConfig["OrderIsEdit"]);
                    localStorage.setItem("OrderIsEditId", window.webposConfig["OrderIsEditId"]);
                }
                // Edit by 17032018 Islam ELgarhy 
                var self = this;
                var params = self.getQuoteInitParams();
                params.items = self.getItemsInfo();

                /*Edit by 24122017 Islam ELgarhy*/
                //if(self.CheckoutModel().getDiscountCausePopup() != "" && self.CheckoutModel().getDiscountCausePopup() != undefined)
                //{
                // for(var i= 0 ;i<params.items.length;i++)
                //  {
                //      params.items[i].discount_cause = self.CheckoutModel().getDiscountCausePopup();
                //      params.items[i].discount_by = self.CheckoutModel().cartDiscountBy();
                //  }
                //alert(params.items[i].discount_cause);
                //}

                /*Edit by 24122017 Islam ELgarhy*/
                params.customer = self.getQuoteCustomerParams();
                if (saveBeforeRemove == true) {
                    params.section = self.KEY.QUOTE_INIT;
                }
                self.loading(true);
                var apiRequest = $.Deferred();
                CartResource().setPush(true).setLog(false).saveCartBeforeCheckout(params, apiRequest);

                apiRequest.done(function (response) {
                    if (response.status == self.DATA.STATUS.ERROR && response.messages && saveBeforeRemove != true) {
                        if (generalHelper.getBrowserConfig("webpos/general/ignore_checkout") == '0') {
                            self.hasErrors(true);
                        }
                        self.errorMessages(response.messages);
                    } else {
                        self.hasErrors(false);
                        self.errorMessages('');
                    }
                }).always(function () {
                    self.loading(false);
                    poleHelper('', 'Total: ' +
                        generalHelper.convertAndFormatPrice(Totals.grandTotal()));
                });
                return apiRequest;
            },
            /**
             * Call API to empty cart - remove quote
             * @returns {*}
             */
            removeCartOnline: function () {
                var self = this;
                var params = self.getQuoteInitParams();
                self.loading(true);
                var apiRequest = $.Deferred();
                CartResource().setPush(true).setLog(false).removeCart(params, apiRequest);

                apiRequest.done(
                    function (response) {
                        if (response.status == self.DATA.STATUS.SUCCESS) {
                            self.emptyCart();
                        }
                    }
                ).always(function () {
                    self.loading(false);
                    poleHelper('', 'Total: ' +
                        generalHelper.convertAndFormatPrice(Totals.grandTotal()));
                });
                return apiRequest;
            },
            /**
             * Call API to remove cart item online
             * @param itemId
             * @returns {*}
             */
            removeItemOnline: function (itemId) {
                var self = this;
                if (Items.items().length == 1) {
                    return self.removeCartOnline();
                }

                var params = self.getQuoteInitParams();
                params.item_id = itemId;

                self.loading(true);
                var apiRequest = $.Deferred();
                CartResource().setPush(true).setLog(false).removeItem(params, apiRequest);

                apiRequest.done(
                    function (response) {
                        if (response.status == self.DATA.STATUS.SUCCESS) {
                            self.removeItem(itemId);
                        }
                    }
                ).always(function () {
                    self.loading(false);
                    poleHelper('', 'Total: ' +
                        generalHelper.convertAndFormatPrice(Totals.grandTotal()));
                });
                return apiRequest;
            },
            /**
             * Check if cart has been saved online or not
             * @returns {boolean}
             */
            hasOnlineQuote: function () {
                var self = this;
                return (DataManager.getData(self.KEY.QUOTE_ID)) ? true : false;
            },
            /**
             * Save quote init data to data manager
             * @param quoteData
             */
            saveQuoteData: function (quoteData) {
                if (quoteData) {
                    $.each(quoteData, function (key, value) {
                        DataManager.setData(key, value);
                    })
                }
            }
        };
        return CartModel.initialize();
    }
);