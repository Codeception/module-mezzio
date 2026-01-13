<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('upload file');

$I->sendPOST('/rest', [], ['dump' => [
    [
        'name' => 'dump1.sql',
        'type' => 'text/plain',
        'size' => 57,
        'tmp_name' => codecept_data_dir('dump.sql'),
        'error' => 0,
    ],
    [
        'name' => 'dump2.sql',
        'type' => 'text/sql',
        'size' => 60,
        'tmp_name' => codecept_data_dir('dump.sql'),
        'error' => 0,
    ],
    ]
]);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['files' => [
    'dump' => [
        [
            'name' => 'dump1.sql',
            'size' => 57,
        ],
        [
            'name' => 'dump2.sql',
            'size' => 60,
        ]
    ]
]]);
