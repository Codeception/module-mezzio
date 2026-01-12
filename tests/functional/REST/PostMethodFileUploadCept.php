<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('upload file');

$I->sendPOST('/rest', [], ['dump' => [
        'name' => 'dump.sql',
        'type' => 'text/plain',
        'size' => 57,
        'tmp_name' => codecept_data_dir('dump.sql'),
        'error' => 0,
    ],
]);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['files' => [
    'dump' => [
        'name' => 'dump.sql',
        'size' => 57,
    ]
]]);
