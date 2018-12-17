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
        'action/customer/change/select-customer-checkout',
        'action/customer/change/show-create-customer-form',
        'action/customer/change/use-guest',
        'model/customer/customer-factory',
        'model/checkout/cart',
        'ui/components/base/list/collection-list',
        'helper/general',
        // 'action/checkout/select-customer-checkout',
        'model/sales/order-factory'
    ],
    function ($,
              ko,
              selectCustomerCheckout,
              showCreateCustomerForm,
              useGuest,
              CustomerFactory,
              CartModel,
              colGrid,
              Helper,
              OrderFactory
    ) {
        "use strict";

        return colGrid.extend({
            items: ko.observableArray([]),
            columns: ko.observableArray([]),
            order_customer: ko.observableArray([]),
            totalCustomer: 0,
            numberOfPage: 1,
            isShowHeader: false,
            isSearchable: true,
            isShowCreateForm: ko.observable(false),


            /* Template for koJS*/
            defaults: {
                template: 'ui/checkout/customer/change-customer'
            },

            /* Automatically run when init JS*/
            initialize: function () {
                this._super();
                this.linstenEventToRender();

            },

            /* Prepare customer collection*/
            _prepareCollection: function () {
                if (Helper.isOnlineCheckout()) {
                    this.collection = CustomerFactory.get().setMode('online').getCollection();
                } else {
                    if (this.collection == null) {
                        this.collection = CustomerFactory.get().setMode('offline').getCollection();
                    }
                }
                debugger;
                this.pageSize = 20;
                this.collection.setPageSize(this.pageSize);
                this.collection.setCurPage(this.curPage);
                this.collection.setOrder('firstname', 'ASC');
                if (this.searchKey) {
                    this.collection.addFieldToFilter(
                        [
                            ['email', "%" + this.searchKey + "%", 'like'],
                            ['telephone', "%" + this.searchKey + "%", 'like'],
                            ['firstname', "%" + this.searchKey + "%", 'like']
                        ]
                    );
                }
            },

            /* Prepare Items for list*/
            _prepareItems: function () {
                var self = this;
                if (this.refresh) {
                    this.curPage = 1;
                }

                if (this.curPage <= this.numberOfPage) {
                    var deferred = self.getCollection().load();
                    $('#customer-overlay').show();
                    deferred.done(function (data) {
                        self.totalCustomer = data.total_count;
                        self.numberOfPage = self.totalCustomer/20 + 1;
                        $('#customer-overlay').hide();
                        self.finishLoading();
                        // console.log(data.items);
                        self.setItems(data.items);
                    });
                }

            },


            /* Select customer to checkout */
            selectCustomer: function (object) {
                
                var self = this;
                // console.log(object);
                var orderCustomer;
                CartModel.isGift(false);
                CartModel.giftBtnClicked(false);
                CartModel.giftAddres('');

                if (Helper.isOnlineCheckout()) {
                    orderCustomer = OrderFactory.get().setMode('online').getCollection();
                } else {
                    orderCustomer = OrderFactory.get().setMode('offline').getCollection();
                }

                orderCustomer.addFieldToFilter('customer_id', object.id, 'eq');
                orderCustomer.setPageSize(2).setCurPage(1).setOrder('created_at', 'DESC');
                
                var deferred = $.Deferred();
                orderCustomer.load(deferred);
                deferred.done(function (response) {
                    self.order_customer(response.items);
                });
                
                selectCustomerCheckout(object);
            },

            formatPriceWithoutSymbol(amount){
                return Helper.formatPriceWithoutSymbol(amount);
            },
            getFullDatetime(datetime){
                var helperDatetime = Helper.getDatetimeHelper();
                return helperDatetime.getFullDate(datetime);
            },

            showCreateCustomerForm: function () {
                showCreateCustomerForm();
            },

            useGuestCustomer: function () {
                useGuest();
            },

            /**
             * Only render list when show popup
             */
            linstenEventToRender: function(){
                var self = this;
                Helper.observerEvent('checkout_customer_list_show_after', function(){
                    self._render();
                })
            },

            webposBackoverClicked: function () {
                $("#popup-edit-product").hide();
                $("#popup-change-customer").hide();
                $("#webpos_cart_discountpopup").hide();
                $("#popup-custom-sale").hide();
                $(".wrap-backover").hide();
                $("#popup-product-detail").hide();
                $("#popup-custom-sale").addClass("fade");
                $("#popup-custom-sale").removeClass("show");
                $("#popup-custom-sale").removeClass("fade-in");
                $('.notification-bell').show();
                if($('#checkout_container').hasClass('showMenu')){
                    $('#c-button--push-left').show();
                    $('#c-button--push-left').removeClass('hide');
                }else{
                    $('#c-button--push-left').hide();
                    $('#c-button--push-left').addClass('hide');
                }
            }
        });
    }
);
