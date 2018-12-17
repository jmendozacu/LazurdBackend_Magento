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
    'action/notification/add-notification',
    'helper/full-screen-loader',
    'model/resource-model/magento-rest/abstract'
], function (ko, $, Component, addNotification, loader, restAbstract) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'ui/PosReports/sales/content'
        },
        elementName: 'myStaff_list',
        elementName2: 'status_list',
        elementName3: 'shipping_list',
        optionsArray: ko.observableArray([]),
        optionsArray2: ko.observableArray([]),
        optionsArray3: ko.observableArray([]),
        toperiodDefault: ko.observableArray(),
        initialize: function () {
            var self = this;
            this._super();
            var deferred = $.Deferred();
            var apiUrl = '/webpos/staff/find';

            restAbstract().setPush(true).setLog(false).callRestApi(
                apiUrl,
                'post', {},
                {},
                deferred);
            self.optionsArray.push({ value: -1, text: "All" });
            deferred.done(function (data) {
                $.each(data, function (i, x) {
                    self.optionsArray.push({ value: x.user_id, text: x.display_name });
                });
            });
            deferred.fail(function (data) {
            });
            /*
            self.optionsArray.push({value: -1, text: "Please select a staff"});
            var deferred = staffFactory.get().setMode('online').getCollection().load();
            deferred.done(function (data) {
                debugger;
                $.each(data.items,function(i,x){
                    self.optionsArray.push({value: x.user_id, text: x.display_name});
                });
            });
            */
            self.optionsArray2([
                { value: 'any', text: 'Any' },
                { value: 'pending', text: 'Need Approval' },
                { value: 'approved', text: 'Approved' },
                { value: 'canceled', text: 'Canceled' },
                { value: 'ready', text: 'Ready' },
                { value: 'correction', text: 'Correction' },
                { value: 'dispatched', text: 'Dispatched' },
                { value: 'waitting', text: 'Waitting' },
                { value: 'delivered', text: 'Delivered' },
                { value: 'returned', text: 'Returned' },


            ]);

            self.optionsArray3([
                { value: 'any', text: 'Any' },
                { value: 'flatrate_flatrate', text: 'Delivery' },
                { value: 'webpos_shipping_storepickup', text: 'Pickup from Ardiya' },
                { value: 'webpos_pickup_gate_mall_gatemallpickup', text: 'Pickup from Gate Mall' },
                { value: 'webpos_pickup_alraya_alrayapickup', text: 'Pickup from Alraya' },

            ]);

            self.toperiodDefault = self.convertDate(new Date());
        },
        showSalesReport: function (data, event) {
            debugger;
            var _self = this;
            var apiUrl = '/webpos/staff/getsales';
            var deferred = $.Deferred();
            var fromDate = $('input[name="from-period"]').val();
            var toDate = $('input[name="to-period"]').val();
            var orderStatus = $('select[name="' + data.elementName2 + '"]').val();
            var staffId = $('select[name="' + data.elementName + '"]').val();
            var shippingMethod = $('select[name="' + data.elementName3 + '"]').val();

            restAbstract().setPush(true).setLog(false).callRestApi(
                apiUrl,
                'post',
                {},
                {
                    'fromDate': fromDate,
                    'toDate': toDate,
                    'orderStatus': orderStatus,
                    'staffId': staffId,
                    'shippingMethod':shippingMethod
                },
                deferred
            );
            deferred.done(function (data) {

                var SalesArr = data;
                var tblhtml = "";
                var tblTotal = 0;
                if (SalesArr.length != 0) {
                    for (var i = 0; i < SalesArr.length; i++) {
                        var evenodd = "";
                        if (i % 2 == 0) {
                            evenodd = "even";
                        }
                        var customerFName = SalesArr[i].customer_firstname == null ? '' : SalesArr[i].customer_firstname;
                        var customerMName = SalesArr[i].customer_middlename == null ? '' : SalesArr[i].customer_middlename;
                        var customerLName = SalesArr[i].customer_lastname == null ? '' : SalesArr[i].customer_lastname;
                        var customerFullName = customerFName + ' ' + customerMName + ' ' + customerLName;
                        var orderStatus = SalesArr[i].order_status == 'pending' ? 'Need Approval' : SalesArr[i].order_status;
                        orderStatus = orderStatus == null ? '' : ((orderStatus)[0].toUpperCase() + (orderStatus).substr(1));
                        tblhtml += "<tr class='" + evenodd + "'><td>"
                            + (SalesArr[i].webpos_staff_name == null ? '' : SalesArr[i].webpos_staff_name) + "</td><td>"
                            + (SalesArr[i].increment_id == null ? '' : SalesArr[i].increment_id) + "</td><td>"
                            + (SalesArr[i].base_total_invoiced == null ? '' : SalesArr[i].base_total_invoiced) + "</td><td>"
                            + (SalesArr[i].created_at == null ? '' : SalesArr[i].created_at) + "</td><td>"
                            + (SalesArr[i].shipping_delivery_date == null ? '' : SalesArr[i].shipping_delivery_date) + "</td><td>"
                            + (SalesArr[i].discount_cause == null ? '' : SalesArr[i].discount_cause) + "</td><td>"
                            + (SalesArr[i].display_name == null ? '' : SalesArr[i].display_name) + "</td><td>"
                            + (SalesArr[i].reason_text == null ? '' : SalesArr[i].reason_text) + "</td><td>"
                            + (customerFullName == null ? '' : customerFullName) + "</td><td>"
                            + (orderStatus == null ? '' : orderStatus) + "</td><td>"
                            + (SalesArr[i].shipping_description == null ? '' : SalesArr[i].shipping_description) + "</td></tr>";

                        tblTotal += (SalesArr[i].base_total_invoiced == null ? 0 : parseFloat(SalesArr[i].base_total_invoiced));
                    }

                    tblhtml += "<tr><td></td><td><strong>Total</strong></td><td><strong>" + tblTotal.toFixed(4) + "</strong></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";

                    //tblhtmlfooter += "<tfoot><tr class='totals'>Total&nbsp;</th><th class=''>&nbsp;</th><th>100</th> </tr></tfoot>"
                }
                else {
                    tblhtml = "<tr class='even'><td class='empty-text a-center' colspan='11'>No records found.</td></tr>";

                }

                //$("#tbl_sales_reportfooter").html(tblhtmlfooter);
                $("#tbl_sales_reportBody").html(tblhtml);
            });
            deferred.fail(function (data) { });
            return true;
        },
        validateSalesReportForm: function () {
            return true;
            //NEW 2018
            /*var form = '#staff-settings-form';
            if ($('#password').val()) {
                $('#current-password').addClass('required-entry');
            } else {
                $('#current-password').removeClass('required-entry');
            }
            return $(form).validation() && $(form).validation('isValid');
            */
        },
        convertDate: function (date) {
            var yyyy = date.getFullYear().toString();
            var mm = (date.getMonth() + 1).toString();
            var dd = date.getDate().toString();

            var mmChars = mm.split('');
            var ddChars = dd.split('');
            //yyyy-MM-dd
            //nnnnnnn2018
            return yyyy + '-' + (mmChars[1] ? mm : "0" + mmChars[0]) + '-' + (ddChars[1] ? dd : "0" + ddChars[0]);
        },
        printReport: function (data, event) {
            debugger;
            var createdDate = new Date();
            var fromDate = $('input[name="from-period"]').val();
            var toDate = $('input[name="to-period"]').val();
            var orderStatus = $('select[name="' + data.elementName2 + '"]').val();
            var staffId = $('select[name="' + data.elementName + '"]').val();
            var divToPrint = document.getElementById('tbl_sales_report').outerHTML;
            var logoPath = window.webposConfig["webpos/general/webpos_logo"];
            var staffName = window.webposConfig["staffName"];
            var headerToPrint = "<div><h4 align='center'><img style='height:60px;'src='" + logoPath + "' class='logo' alt='Logo' /> </br>Sales Report(Order List By Staff)</br>**** *****</h4><h5>Period From:  " + fromDate + "     To:   " + toDate + "</br> Order Status: " + orderStatus + "</br> Staff: " + (staffId == -1 ? "all" : staffId) + "</br> DateTime: " + createdDate + "</br> UserName: " + staffName + "</h5></div>";
            var newWin = window.open('', 'Print-Window');
            newWin.document.open();
            newWin.document.write('<html> <style> th, td {border: 0.1px solid gray;font-family: Roboto, "Segoe UI", Tahoma, sans-serif;font-size: 81.25%;}</style><body onload="window.print()">' + headerToPrint + divToPrint + '<div><h4 align="center">---------- ********** ---------</h4></div></body></html>');
            newWin.document.close();
            setTimeout(function () { newWin.close(); }, 10);

        }
    });
});