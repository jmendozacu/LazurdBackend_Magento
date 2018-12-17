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
        'model/checkout/checkout',
        'model/checkout/cart',
        'model/checkout/cart/discountpopup',
        'model/checkout/cart/totals',
        'helper/general',
        'eventManager',
        'action/checkout/cart/show-cart-discount-popup',
        'action/checkout/cart/hide-cart-discount-popup',
        'model/resource-model/magento-rest/abstract',
        'model/staff/current-staff' //islam

    ],
    function ($,ko, Component, CheckoutModel, CartModel, DiscountModel, Totals, Helper, Event, ShowCartDiscountPopup, HideCartDiscountPopup,restAbstract,currentStaff) {
        "use strict";
        
        DiscountModel.reset();
        return Component.extend({
            defaults: {
                template: 'ui/checkout/cart/discountpopup'
            },
            isOnCheckout: CartModel.isOnCheckoutPage,
            isOnlineCheckout: ko.pureComputed(function(){
                return (Helper.isOnlineCheckout())?true:false;
            }),
            stage: ko.observable(),
            discountTitle: ko.observable(),
            cancelPromotionButtonTitle: ko.observable(),
            applyButtonTitle: ko.observable(),
            numberpadCache: ko.observableArray([]),
            stopCache: ko.observable(),
            discountType: ko.pureComputed(function(){
                if(!DiscountModel.cartDiscountType()){
                    DiscountModel.cartDiscountType(DiscountModel.DISCOUNT_TYPES.FIXED);
                }
                return DiscountModel.cartDiscountType();
            }),
            calculatedDiscountAmount: ko.pureComputed(function(){
                return Totals.discountAmount();
            }),
            discountName: ko.pureComputed(function(){
                if(!DiscountModel.cartDiscountName()){
                    DiscountModel.cartDiscountName("");
                }
                return DiscountModel.cartDiscountName();
            }),
             /*Edit by 24122017 Islam ELgarhy*/
            discountCause: ko.pureComputed(function(){
                if(!DiscountModel.cartDiscountCause()){
                    DiscountModel.cartDiscountCause("");
                }
                return DiscountModel.cartDiscountCause();
            }),
             /*Edit by 24122017 Islam ELgarhy*/
            couponCode: ko.pureComputed(function(){
                if(!DiscountModel.couponCode()){
                    DiscountModel.couponCode("");
                }
                return DiscountModel.couponCode();
            }),
            promotionDiscount: ko.pureComputed(function(){
                if(!DiscountModel.promotionDiscountAmount()){
                    DiscountModel.promotionDiscountAmount("");
                }
                return Helper.convertAndFormatPrice(DiscountModel.promotionDiscountAmount());
            }),
            hasPromotionDiscount: ko.pureComputed(function(){
                return ((!Helper.isOnlineCheckout() && DiscountModel.promotionDiscountAmount()) || DiscountModel.appliedPromotion())?true:false;
            }),
            loading: ko.pureComputed(function(){
                return (DiscountModel.loading())?true:false;
            }),
            initialize: function () {
                this._super();
                this.initDefaultData();

                var self = this;
                this.isDiscountStage = ko.pureComputed(function(){
                    return (self.stage() == DiscountModel.STAGES.DISCOUNT)?true:false;
                });
                this.isPromotionStage = ko.pureComputed(function(){
                    return (self.stage() == DiscountModel.STAGES.PROMOTION)?true:false;
                });
                this.discountAmount = ko.pureComputed(function(){
                    if(!DiscountModel.cartDiscountAmount()){
                        DiscountModel.cartDiscountAmount("");
                    }
                    var amount = (DiscountModel.cartDiscountAmount())?DiscountModel.cartDiscountAmount():0;
                    if(self.discountType() == DiscountModel.DISCOUNT_TYPES.PERCENT){
                        amount = DiscountModel.cartDiscountPercent();
                    }else{
                        var priceFormat = window.webposConfig.priceFormat;
                        var precision = (priceFormat && priceFormat.precision)?priceFormat.precision:2;
                        amount = parseFloat(amount).toFixed(precision);
                    }
                    return amount;
                });
                this.isDiscountFixed = ko.pureComputed(function(){
                    return (self.discountType() == DiscountModel.DISCOUNT_TYPES.FIXED)?true:false;
                });
                this.isDiscountPercent = ko.pureComputed(function(){
                    return (self.discountType() == DiscountModel.DISCOUNT_TYPES.PERCENT)?true:false;
                });
                this.stopCache(false);
                this.disableDiscountAmountInput = true;
                this.disablePromotionAmountInput = true;
                this.popupEl = '#webpos_cart_discountpopup';
                this.overlayEl = '.wrap-backover';
                Event.observer('show_cart_discount_popup', function(){
                    if(currentStaff.getStaffResourceAccessOriginal().length != 0)
                    {
                        currentStaff.setStaffResourceAccess(currentStaff.getStaffResourceAccessOriginal());
                    }
                    /*Edit by 24122017 Islam ELgarhy*/
                    if(currentStaff.getstaffIdOriginal() != "")
                    {
                        currentStaff.setStaffId(currentStaff.getstaffIdOriginal());
                    }
                    /*Edit by 24122017 Islam ELgarhy*/
                    self.show();
                });
                Event.observer('cart_empty_after', function(){
                    self.resetData();
                });
                Event.observer('remove_cart_discount', function(){
                    if(Helper.isOnlineCheckout()){

                    }else{
                        self.resetData();
                        self.initDefaultData();
                    }
                });
                Event.observer('staff_resource_Access_to_original', function(){
                    if(currentStaff.getStaffResourceAccessOriginal().length != 0)
                    {
                        currentStaff.setStaffResourceAccess(currentStaff.getStaffResourceAccessOriginal());
                    }
                     /*Edit by 24122017 Islam ELgarhy*/
                    if(currentStaff.getstaffIdOriginal() != "")
                    {
                        currentStaff.setStaffId(currentStaff.getstaffIdOriginal());
                    }
                     /*Edit by 24122017 Islam ELgarhy*/
                });
                // Ryan not Copy To server _ I.Elgarhy
                //Edit by ryan to apply discount for edited orrder
               Event.observer('apply_from_edit', function(){
                    if(window.webposConfig["OrderIsEdit"] && window.webposConfig["editedORderDiscount"] != '0')
                    {
                        var discountValue = window.webposConfig["editedORderDiscount"];
                        DiscountModel.cartDiscountAmount(discountValue); 
                        DiscountModel.cartBaseDiscountAmount(discountValue); 
                        self.apply();                   
                        self.hide();
                    }
                });
            },
            initDefaultData: function(){
                var self = this;
                var title = Helper.__("Discount - Maximum ")+DiscountModel.maximumPercent()+"%";
                self.stage(DiscountModel.STAGES.DISCOUNT);
                self.discountTitle(title);
                self.cancelPromotionButtonTitle(Helper.__("Remove"));
                self.applyButtonTitle(Helper.__("Apply"));
            },
            resetData: function(){
                DiscountModel.reset();
                this.stopCache(false);
                this.numberpadCache([]);
            },
            showDiscount: function(){
                this.stage(DiscountModel.STAGES.DISCOUNT);
            },
            showPromotion: function(){
                this.stage(DiscountModel.STAGES.PROMOTION);
            },
            discountFixed: function(){
                this.numberpadCache([]);
                this.stopCache(false);
                DiscountModel.cartDiscountType(DiscountModel.DISCOUNT_TYPES.FIXED);
                this.showDiscountAmount();
            },
            discountPercent: function(){
                this.numberpadCache([]);
                this.stopCache(false);
                DiscountModel.cartDiscountType(DiscountModel.DISCOUNT_TYPES.PERCENT);
                DiscountModel.cartDiscountPercent(0);
                this.showDiscountAmount();
            },
            numberpadClick: function(data,event){
                var value = event.target.getAttribute("value");
                if(this.numberpadCache().length <= 0 && (value =="00" || value == "0")){
                    return;
                }
                if(value != "backspace" && typeof value != "undefined" && this.stopCache() == false){
                    if(value == "00"){
                        this.numberpadCache().push(0);
                        this.numberpadCache().push(0);
                    }else{
                        this.numberpadCache().push(value);
                    }
                }else{
                    if(value == "backspace" && this.numberpadCache().length > 0){
                        this.numberpadCache().pop();
                    }
                }
                this.showDiscountAmount();
            },
            showDiscountAmount: function(){
                if(this.numberpadCache().length > 0){
                    var discountAmount = "";
                    $.each(this.numberpadCache(), function(){
                        discountAmount += ""+this;
                    });
                    discountAmount = parseFloat(discountAmount)/100;
                    var grandTotal = Totals.getMaxDiscountAbleAmount();
                    var maximumPercent = DiscountModel.maximumPercent();
                    if(this.isDiscountFixed() == true){
                        var percent = Helper.toBasePrice(discountAmount)/grandTotal*100;
                        if(percent > maximumPercent){
                            discountAmount = maximumPercent * grandTotal / 100;
                            this.stopCache(true);
                            DiscountModel.cartDiscountAmount(Helper.convertPrice(discountAmount));
                            DiscountModel.cartBaseDiscountAmount(discountAmount);
                        }else{
                            this.stopCache(false);
                            DiscountModel.cartDiscountAmount(discountAmount);
                            DiscountModel.cartBaseDiscountAmount(Helper.toBasePrice(discountAmount));
                        }
                    }else{
                        if(discountAmount > maximumPercent){
                            discountAmount = parseFloat(maximumPercent);
                            this.stopCache(true);
                        }else{
                            this.stopCache(false);
                        }
                        DiscountModel.cartDiscountPercent(discountAmount.toFixed(2));
                        DiscountModel.cartDiscountAmount(discountAmount);
                        DiscountModel.cartBaseDiscountAmount(discountAmount);
                    }
                }else{
                    DiscountModel.cartDiscountAmount("0.00");
                    DiscountModel.cartBaseDiscountAmount(0);
                    DiscountModel.cartDiscountPercent(0);
                }
                if(DiscountModel.appliedPromotion() || DiscountModel.appliedDiscount()){
                    DiscountModel.process(Totals.baseDiscountAmount());
                }
            },
            apply: function(){
                debugger;
                if(this.isPromotionStage() == true){
                    if(Helper.isOnlineCheckout()){
                        CheckoutModel.applyCouponOnline();
                    }else{
                        DiscountModel.appliedPromotion(true);
                        DiscountModel.appliedDiscount(false);
                        DiscountModel.cartDiscountType(DiscountModel.DISCOUNT_TYPES.FIXED);
                        DiscountModel.cartBaseDiscountAmount(DiscountModel.promotionDiscountAmount());
                        DiscountModel.cartDiscountAmount(Helper.convertPrice(DiscountModel.promotionDiscountAmount()));
                    }
                }else{
                    if(Helper.isOnlineCheckout()){
                        CheckoutModel.applyCartDiscountOnline();
                    }else{
                        DiscountModel.appliedPromotion(false);
                        DiscountModel.appliedDiscount(true);
                    }
                }
                if(!Helper.isOnlineCheckout()){
                    if(DiscountModel.cartBaseDiscountAmount() <= 0){
                        DiscountModel.reset();
                    }
                    Totals.updateDiscountTotal();
                    DiscountModel.process(Totals.baseDiscountAmount());
                }
                this.hide();
                Helper.dispatchEvent('reset_payments_data', '');
            },
            hide: function(){
                HideCartDiscountPopup();
            },
            show: function(){
                if(DiscountModel.appliedPromotion() == true){
                    this.showPromotion();
                }else{
                    this.showDiscount();
                }
                DiscountModel.appliedPromotion(false);
                DiscountModel.appliedDiscount(false);
                Totals.updateDiscountTotal();
                DiscountModel.process(0);

                if(
                    Helper.isHavePermission("Magestore_Webpos::apply_discount_per_cart") &&
                    !Helper.isHavePermission("Magestore_Webpos::apply_coupon")
                ){
                    this.showDiscount();
                }
                if(
                    !Helper.isHavePermission("Magestore_Webpos::apply_discount_per_cart") &&
                    Helper.isHavePermission("Magestore_Webpos::apply_coupon")
                ){
                    this.showPromotion();
                }

                ShowCartDiscountPopup(this.isOnCheckout());
            },
            cancelPromotion: function(){
                
                if(Helper.isOnlineCheckout()){
                    if(DiscountModel.appliedPromotion() == true){
                        CheckoutModel.cancelCouponOnline();
                        HideCartDiscountPopup();
                    }
                }else{
                    DiscountModel.reset();
                    DiscountModel.couponCode("");
                    if(DiscountModel.appliedPromotion() == true){
                        DiscountModel.appliedPromotion(false);
                        DiscountModel.promotionDiscountAmount(0);
                        DiscountModel.cartDiscountAmount(0);
                        DiscountModel.cartBaseDiscountAmount(0);
                        DiscountModel.appliedDiscount(false);
                        DiscountModel.appliedPromotion(false);
                    }
                    Totals.updateDiscountTotal();
                }
            },
            setCoupon: function(data,event){
                DiscountModel.couponCode(event.target.value);
            },
            setDiscountName: function(data,event){
                DiscountModel.cartDiscountName(event.target.value)
            },
             /*Edit by 24122017 Islam ELgarhy*/
            setDiscountCause: function(data,event){
                DiscountModel.cartDiscountCause(event.target.value)
            },
             /*Edit by 24122017 Islam ELgarhy*/
            checkPromotion: function(data,event){
                if(this.isPromotionStage() == true && this.loading() == false){
                    CheckoutModel.checkPromotion();
                }
            },
            getCurrencySymbol: function(){
                return window.webposConfig.currentCurrencySymbol;
            },
            canUseDiscount: function(){
                return (Helper.isHavePermission("Magestore_Webpos::all_discount") || Helper.isHavePermission("Magestore_Webpos::apply_discount_per_cart"));
            },
            canUseCoupon: function(){
                var canUseCoupon = (Helper.isHavePermission("Magestore_Webpos::all_discount") || Helper.isHavePermission("Magestore_Webpos::apply_coupon"));
                return canUseCoupon;
            },
            activeDiscount: function(){
                    var _self = this;
                    var apiUrl = '/webpos/staff/activediscount';
                    var deferred = $.Deferred();
                    var staff = {};
                    staff.username = $('#discount_username').val();
                    staff.password = $('#discount_userpassword').val();       
                    
                    restAbstract().setPush(true).setLog(false).callRestApi(
                        apiUrl,
                        'post',
                        {},
                        {
                            'staff': staff,
                            'store': Helper.getLocalConfig("currstoreId")
                        },
                        deferred
                    );
                    deferred.done(function (data) {
                        if (data != false) {
                            
                            var originalResource = $.extend(true, [], window.webposConfig.staffResourceAccess);
                            currentStaff.setStaffResourceAccessOriginal(originalResource);
                            currentStaff.setStaffResourceAccess(data.staffResourceAccess);
                              /*Edit by 24122017 Islam ELgarhy*/
                            var originalStaffId = $.extend(true, [], window.webposConfig.staffId);
                            currentStaff.setstaffIdOriginal(originalStaffId);
                            currentStaff.setStaffId(data.Staff);
                              /*Edit by 24122017 Islam ELgarhy*/
                            if((_self.isPromotionStage() && !_self.canUseCoupon()) ||(_self.isDiscountStage() && !_self.canUseDiscount()))
                            {
                                $.toaster(
                                    {
                                        priority: 'danger',
                                        title: Helper.__("Warning"),
                                        message: Helper.__("Your Activation information is wrong!")
                                    }
                                );
                            }
                            
                        }
                        else
                        {
                            $.toaster(
                                {
                                    priority: 'danger',
                                    title: Helper.__("Warning"),
                                    message: Helper.__("Your Activation information is wrong!")
                                }
                            );
                        }
                    });
                    deferred.fail(function (data) {
                        $.toaster(
                            {
                                priority: 'danger',
                                title: Helper.__("Warning"),
                                message: Helper.__("Your Activation information is wrong!")
                            }
                        );
                    })
               return false;
            },
            canUseCouponCode: ko.pureComputed(function(){
                var canUseCoupon = (Helper.isHavePermission("Magestore_Webpos::all_discount") || Helper.isHavePermission("Magestore_Webpos::apply_coupon"));
                return canUseCoupon && DiscountModel.modifierCanUseCoupon();
            }),
            enterCoupon: function(data,event){
                if(event.keyCode == 13){
                    if(Helper.isOnlineCheckout()){
                        CheckoutModel.applyCouponOnline();
                    }else{
                        this.checkPromotion();
                    }
                    event.target.blur();
                }
            }
        });
    }
);