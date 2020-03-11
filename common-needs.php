<?php defined('CORE_FOLDER') OR exit('You can not get in here!');

    $ticket_situations = [
        'process' => '<div class="listingstatus"><span class="process">'.__("website/account_tickets/status-process").'</span></div>',
        'waiting' => '<div class="listingstatus"><span class="wait">'.__("website/account_tickets/status-waiting").'</span></div>',
        'replied' => '<div class="listingstatus"><span class="active">'.__("website/account_tickets/status-replied").'</span></div>',
        'solved' => '<div class="listingstatus"><span>'.__("website/account_tickets/status-solved").'</span></div>',
    ];

    $product_situations = [
        'waiting' => '<div class="listingstatus"><span class="wait">'.__("website/account_products/status-waiting").'</span></div>',
        'inprocess' => '<div class="listingstatus"><span class="process">'.__("website/account_products/status-inprocess").'</span></div>',
        'cancelled' => '<div class="listingstatus"><span>'.__("website/account_products/status-cancelled").'</span></div>',
        'suspended' => '<div class="listingstatus"><span>'.__("website/account_products/status-suspended").'</span></div>',
        'active' => '<div class="listingstatus"><span class="active">'.__("website/account_products/status-active").'</span></div>',
        'inactive' => '<span> '.__("website/account_products/status-inactive").'</span>',
    ];

    $origin_situations = [
        'active' => '<div class="listingstatus"><span class="active">'.__("website/account_products/origin-status-active").'</span></div>',
        'inactive' => '<div class="listingstatus"><span>'.__("website/account_products/origin-status-inactive").'</span></div>',
        'waiting' => '<div class="listingstatus"><span class="process">'.__("website/account_products/origin-status-waiting").'</span></div>',
    ];

    $origin_prereg_situations = [
        'active' => '<div class="listingstatus"><span class="active">'.__("website/account_products/origin-status-active").'</span></div>',
        'inactive' => '<div class="listingstatus"><span>'.__("website/account_products/origin-status-inactive").'</span></div>',
        'waiting' => '<div class="listingstatus"><span class="process">'.__("website/account_products/origin-status-waiting").'</span></div>',
    ];

    $invoice_situations = [
        'refund' => '<div class="listingstatus"><span>'.__("website/account_invoices/status-refund").'</span></div>',
        'waiting' => '<div class="listingstatus"><span class="process">'.__("website/account_invoices/status-waiting").'</span></div>',
        'unpaid' => '<div class="listingstatus"><span class="wait">'.__("website/account_invoices/status-unpaid").'</span></div>',
        'cancelled' => '<span>'.__("website/account_invoices/status-cancelled").'</span>',
        'paid' => '<div class="listingstatus"><span class="active">'.__("website/account_invoices/status-paid").'</span></div>',
    ];

    return [
        'ticket'  => $ticket_situations,
        'product' => $product_situations,
        'origin' => $origin_situations,
        'invoice' => $invoice_situations,
    ];