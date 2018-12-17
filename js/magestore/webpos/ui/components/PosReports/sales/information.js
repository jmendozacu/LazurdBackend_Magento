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

      //NEW 2018
define([
    'ko',
    'jquery',
    'posComponent',
    'action/notification/add-notification',
    'helper/full-screen-loader',
    'model/resource-model/magento-rest/abstract'
], function (ko, $, Component, addNotification, loader,restAbstract) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'ui/PosReports/sales/information',
            elementName: 'myStaff_list',
            optionsArray: ko.observableArray([]),
            elementName2: 'status_list',
            elementName3: 'shipping_list',
            optionsArray2: ko.observableArray([]),
            optionsArray3: ko.observableArray([]),
            toperiodDefault:ko.observableArray()
        },
        initialize: function () {
            this._super();
            var self = this;
            if(self.optionsArray().length == 0){
                var deferred = $.Deferred();
                var apiUrl = '/webpos/staff/find';
                
                restAbstract().setPush(true).setLog(false).callRestApi(
                    apiUrl,
                    'post',{},
                    {},
                    deferred);
                self.optionsArray.push({value: -1, text: "All"});
                deferred.done(function (data) {
                   
                    $.each(data,function(i,x){
                        self.optionsArray.push({value: x.user_id, text: x.display_name});
                    });
                });
                deferred.fail(function (data) {
                });
                
                /*self.optionsArray.push({value: -1, text: "Please select a staff"});
                var deferred = staffFactory.get().setMode('online').getCollection().load();
                deferred.done(function (data) {
                    debugger;
                $.each(data.items,function(i,x){
                    self.optionsArray.push({value: x.user_id, text: x.display_name});
                });
                
            });
*/
            }
            if(self.optionsArray2().length == 0){
         
                self.optionsArray2([
                    {value: 'any', text: 'Any'},
					{value: 'pending', text: 'Need Approval'},
					{value: 'approved', text: 'Approved'},
					{value: 'canceled', text: 'Canceled'},
					{value: 'ready', text: 'Ready'},
                    {value: 'correction', text: 'Correction'},
                    {value: 'dispatched', text: 'Dispatched'},
                    {value: 'waitting', text: 'Waitting'},
                    {value: 'delivered', text: 'Delivered'},
                    {value: 'returned', text: 'Returned'},
    
                ]);
                
    
            }
            if(self.optionsArray3().length == 0){
         
                self.optionsArray3([
                    { value: 'any', text: 'Any' },
                    { value: 'flatrate_flatrate', text: 'Delivery' },
                    { value: 'webpos_shipping_storepickup', text: 'Pickup from Ardiya' },
                    { value: 'webpos_pickup_gate_mall_gatemallpickup', text: 'Pickup from Gate Mall' },
                    { value: 'webpos_pickup_alraya_alrayapickup', text: 'Pickup from Alraya' },
    
                ]);
    
            }

              //nnnnnnn2018
            self.toperiodDefault = self.convertDate(new Date());
           
        },
        convertDate: function (date) {
            var yyyy = date.getFullYear().toString();
            var mm = (date.getMonth()+1).toString();
            var dd  = date.getDate().toString();
          
            var mmChars = mm.split('');
            var ddChars = dd.split('');
          
            //yyyy-MM-dd
            //nnnnnnn2018
            return yyyy +'-' + (mmChars[1]?mm:"0"+mmChars[0]) + '-' + (ddChars[1]?dd:"0"+ddChars[0]) ;
        },
    });
});