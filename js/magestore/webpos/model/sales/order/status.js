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
    'ko'
    ], function (ko) {
    'use strict';

    return {
        getStatusObject: function(){
            return [

                // OrderStatus&State&Status Islam Elgarhy
                {statusClass: 'pending', statusTitle: 'Need Approval', statusLabel: 'Need Approval'},
                {statusClass: 'approved', statusTitle: 'Approved', statusLabel: 'Approved'},
                {statusClass: 'canceled', statusTitle: 'Canceled', statusLabel: 'canceled'},
                {statusClass: 'ready', statusTitle: 'Ready', statusLabel: 'Ready'},
                {statusClass: 'correction', statusTitle: 'Correction', statusLabel: 'Correction'},
                {statusClass: 'dispatched', statusTitle: 'Dispatched', statusLabel: 'Dispatched'},
                {statusClass: 'waitting', statusTitle: 'Waitting', statusLabel: 'Waitting'},
                {statusClass: 'delivered', statusTitle: 'Delivered', statusLabel: 'Delivered'},
                {statusClass: 'returned', statusTitle: 'Returned', statusLabel: 'Returned'},
                {statusClass: 'closed', statusTitle: 'Closed', statusLabel: 'Closed'},
                {statusClass: 'notsync', statusTitle: 'Not Sync', statusLabel: 'Not Sync'}
         
              
               
            
              
               


         
                /*
                {statusClass: 'pending', statusTitle: 'Pending', statusLabel: 'Pending'},
                {statusClass: 'pending_payment', statusTitle: 'Pending Payment', statusLabel: 'Pending Payment'},
                {statusClass: 'processing', statusTitle: 'Processing', statusLabel: 'Processing'},
                {statusClass: 'complete', statusTitle: 'Complete', statusLabel: 'Complete'},
                {statusClass: 'canceled', statusTitle: 'Canceled', statusLabel: 'Cancelled'},
                {statusClass: 'closed', statusTitle: 'Closed', statusLabel: 'Closed'},
                {statusClass: 'notsync', statusTitle: 'Not Sync', statusLabel: 'Not Sync'},
                {statusClass: 'need_approval', statusTitle: 'Need Approval', statusLabel: 'Need Approval'},
                {statusClass: 'waitting', statusTitle: 'Waitting', statusLabel: 'Waitting'},
                {statusClass: 'waitting_need_approve', statusTitle: 'Waitting/Need Approve', statusLabel: 'Waitting/Need Approve'},
                {statusClass: 'correction', statusTitle: 'Correction', statusLabel: 'Correction'}
                */
                 // OrderStatus&State&Status Islam Elgarhy
        
            ]
        },

        getStatusObjectView: function(){
            return [
                {statusClass: 'pending', statusTitle: 'Need Approval', statusLabel: 'Need Approval'},
                {statusClass: 'approved', statusTitle: 'Approved', statusLabel: 'Approved'},
                {statusClass: 'canceled', statusTitle: 'Canceled', statusLabel: 'canceled'},
                {statusClass: 'ready', statusTitle: 'Ready', statusLabel: 'Ready'},
                {statusClass: 'correction', statusTitle: 'Correction', statusLabel: 'Correction'},
                {statusClass: 'dispatched', statusTitle: 'Dispatched', statusLabel: 'Dispatched'},
                {statusClass: 'waitting', statusTitle: 'Waitting', statusLabel: 'Waitting'},
                {statusClass: 'delivered', statusTitle: 'Delivered', statusLabel: 'Delivered'},
                {statusClass: 'returned', statusTitle: 'Returned', statusLabel: 'Returned'},
                {statusClass: 'closed', statusTitle: 'Closed', statusLabel: 'Closed'},
                {statusClass: 'notsync', statusTitle: 'Not Sync', statusLabel: 'Not Sync'},
                {statusClass: 'holded', statusTitle: 'On Hold', statusLabel: 'On Hold'},
                {statusClass: 'onhold', statusTitle: 'On Hold', statusLabel: 'On Hold'}


                // OrderStatus&State&Status Islam Elgarhy
                /*
                {statusClass: 'pending', statusTitle: 'Pending', statusLabel: 'Pending'},
                {statusClass: 'pending_payment', statusTitle: 'Pending Payment', statusLabel: 'Pending Payment'},
                {statusClass: 'processing', statusTitle: 'Processing', statusLabel: 'Processing'},
                {statusClass: 'complete', statusTitle: 'Complete', statusLabel: 'Complete'},
                {statusClass: 'canceled', statusTitle: 'Canceled', statusLabel: 'Cancelled'},
                {statusClass: 'need_approval', statusTitle: 'Need Approval', statusLabel: 'Need Approval'},
                {statusClass: 'waitting', statusTitle: 'Waitting', statusLabel: 'Waitting'},
                {statusClass: 'waitting_need_approve', statusTitle: 'Waitting/Need Approve', statusLabel: 'Waitting/Need Approve'},
                {statusClass: 'closed', statusTitle: 'Closed', statusLabel: 'Closed'},
                {statusClass: 'notsync', statusTitle: 'Not Sync', statusLabel: 'Not Sync'},
                {statusClass: 'holded', statusTitle: 'On Hold', statusLabel: 'On Hold'},
                {statusClass: 'onhold', statusTitle: 'On Hold', statusLabel: 'On Hold'},
                {statusClass: 'correction', statusTitle: 'Correction', statusLabel: 'Correction'}
                */
                // OrderStatus&State&Status Islam Elgarhy
            ];
        },

        getStatusArray: function(){
            return ['pending','processing','complete','canceled','closed','notsync', 'holded'];
        }
    }
});
