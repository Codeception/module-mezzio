<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('make GET request with query string');
$I->sendGET('/rest?param=value');
$I->seeResponseIsJson();

$expectedResponse = array(
    'requestMethod' => 'GET',
    'queryParams' => array(
        'param' => 'value'
    ),
);
$I->seeResponseContainsJson($expectedResponse);
