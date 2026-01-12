<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('make GET request with query string');
$I->sendGET('/rest?param1=value1', ['param2' => 'value2']);
$I->seeResponseIsJson();

$expectedResponse = [
    'requestMethod' => 'GET',
    'requestUri' => '/rest?param1=value1&param2=value2',
    'queryParams' => [
        'param1' => 'value1',
        'param2' => 'value2',
    ],
];
$I->seeResponseContainsJson($expectedResponse);
