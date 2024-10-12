<?php

return [
    'storage' => [
        'root_folder' => env('STORAGE_FOLDER', '/test')
    ],
    'disbursement' => [
        'server' => [
            'url' => env('DISBURSEMENT_SERVER_URL', 'https://fibi.disburse.cash/api/disburse'),
            'token' => env('DISBURSEMENT_SERVER_TOKEN')
        ],
        'bank' => [
            'code' => env('DISBURSEMENT_BANK_CODE'),
            'via' => env('DISBURSEMENT_BANK_VIA'),
        ],
    ],
];
