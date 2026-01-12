<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('make POST request with query string');
$I->sendPOST('/rest?param=value');

$expectedResponse = array(
    'requestMethod' => 'POST',
    'queryParams' => array(
        'param' => 'value'
    ),
);
$I->seeResponseContainsJson($expectedResponse);
