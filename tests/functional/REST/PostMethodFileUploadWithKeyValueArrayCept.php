<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('upload file using simple key-value array');

$uploadFile = codecept_data_dir('dump.sql');

$I->sendPOST('/rest', [], ['dump' => $uploadFile]);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['files' => [
    'dump' => [
        'name' => 'dump.sql',
        'tmp_name' => $uploadFile,
        'type' => 'text/plain',
        'size' => 57,
        'error' => 0,
    ]
]]);
