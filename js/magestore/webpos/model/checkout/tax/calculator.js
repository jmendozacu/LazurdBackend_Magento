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
        'dataManager',
        'eventManager',
        'model/appConfig'
    ],
    function ($, ko, DataManager, Event, AppConfig) {
        "use strict";
        var TaxCalculator = {
            rules: ko.observableArray([]),
            rates: ko.observableArray([]),
            groups: ko.observableArray([]),
            initialize: function () {
                var self = this;
                self.initData();
                Event.observer(AppConfig.EVENT.DATA_MANAGER_SET_DATA_AFTER, function(event, data){
                    self.initData();
                });
                return self;
            },
            initData: function () {
                var self = this;
                if (self.rules().length <= 0) {
                    self.getRules();
                }
                if (self.rates().length <= 0) {
                    self.getRates();
                }
                if (self.groups().length <= 0) {
                    self.getGroups();
                }
            },
            reInitData: function () {
                var self = this;
                self.getRules();
                self.getRates();
                self.getGroups();
            },
            getRules: function () {
                var self = this;
                var taxRules = DataManager.getData('tax_rules');
                if (taxRules && taxRules.length > 0) {
                    self.rules(taxRules);
                }
            },
            getRates: function () {
                var self = this;
                var taxRates = DataManager.getData('tax_rates');
                if (taxRates && taxRates.length > 0) {
                    self.rates(taxRates);
                }
            },
            getGroups: function () {
                var self = this;
                var groups = DataManager.getData('customer_group');
                if (groups && groups.length > 0) {
                    self.groups(groups);
                }
            },
            getProductTaxRate: function (productTaxClassId, customerGroup, billingAddress) {
                var self = this;
                var customerTaxClassId = self.getCustomerTaxClassId(customerGroup);
                var rateIds = self.getRuleRates(productTaxClassId, customerTaxClassId);
                var rate = self.getRate(rateIds, billingAddress);
                return rate;
            },
            getCustomerTaxClassId: function (customerGroup) {
                var customerTaxClassId = "";
                if (customerGroup && this.groups() && this.groups().length > 0) {
                    $.each(this.groups(), function () {
                        if (this.id == customerGroup) {
                            customerTaxClassId = this.tax_class_id;
                        }
                    });
                }
                return customerTaxClassId;
            },
            getRuleRates: function (productTaxClassId, customerTaxClassId) {
                var self = this;
                var rateIds = "";
                var highestPriority = "";
                if (customerTaxClassId != "") {
                    if (this.rules() && this.rules().length > 0) {
                        $.each(this.rules(), function () {
                            if (
                                $.inArray(customerTaxClassId, this.customer_tc_ids) !== -1
                                && $.inArray(productTaxClassId, this.product_tc_ids) !== -1
                            ) {
                                if (highestPriority === "" || (highestPriority !== "" && highestPriority > this.priority)) {
                                    highestPriority = this.priority;
                                    rateIds = this.rates_ids;
                                }
                            }
                        });
                    }
                } else {
                    if (this.rules() && this.rules().length > 0) {
                        rateIds = [];
                        $.each(this.rules(), function () {
                            if (
                                $.inArray(productTaxClassId, this.product_tc_ids) !== -1
                            ) {
                                if (highestPriority === "" || (highestPriority !== "" && highestPriority > this.priority)) {
                                    highestPriority = this.priority;
                                    rateIds = this.rates_ids;
                                }
                            }
                        });
                    }
                }
                return rateIds;
            },
            getRate: function (rateIds, address) {
                var self = this;
                var rate = 0;
                if (rateIds != "") {
                    if (rateIds && rateIds.length > 0) {
                        $.each(rateIds, function () {
                            var rateId = this;
                            if (self.rates() && self.rates().length > 0) {
                                $.each(self.rates(), function () {
                                    if (this.id == rateId) {
                                        if (
                                            (this.country == "*" || this.country == address.country_id) &&
                                            (this.region_id == "*" || this.region_id == "0" || this.region_id == address.region_id) &&
                                            (this.postcode == "*" || this.postcode == address.postcode)
                                        ) {
                                            rate = this.rate;
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
                return rate;
            }
        };
        return TaxCalculator.initialize();
    }
);