<?php

return [

    /*
    |--------------------------------------------------------------------------
    | VAT percent
    |--------------------------------------------------------------------------
    |
    | By specifying a VAT percent, vat_amount will automatically be
    | set on 'Transaction' when transfering funds to SalesAccount
    |
    | Example values: "0.10" for 10%
    |
    | */

    'vat_percent' => 0.00,

    'invoice_storage' => [
        'disk' => null,
        'path' => '/',
        'visibility' => 'public', // public or private
    ],
];
