<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('upload file using simple key-value array');

$uploadFile = codecept_data_dir('dump.sql');

$I->sendPOST('/rest', [], [
    'dump1' => $uploadFile,
    'nested' => [
        'dump2' => $uploadFile,
    ],
    'nested1' => [
        'nested2' => [
            'dump3' => $uploadFile,
        ]
    ],
]);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['files' => [
    'dump1' => [
        'name' => 'dump.sql',
        'tmp_name' => $uploadFile,
        'type' => 'text/plain',
        'size' => 57,
        'error' => 0,
    ],
    'nested' => [
        'dump2' => [
            'name' => 'dump.sql',
            'tmp_name' => $uploadFile,
            'type' => 'text/plain',
            'size' => 57,
            'error' => 0,
        ],
    ],
    'nested1' => [
        'nested2' => [
            'dump3' => [
                'name' => 'dump.sql',
                'tmp_name' => $uploadFile,
                'type' => 'text/plain',
                'size' => 57,
                'error' => 0,
            ],
        ],
    ],
]]);
