<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('make GET request with custom request header');
$I->haveHttpHeader('X-Auth-Token', 'verySecureToken');
$I->sendGET('/rest');

$expectedResponse = array(
    'requestMethod' => 'GET',
    'queryParams' => array(),
    'X-Auth-Token' => 'verySecureToken',
);
$I->seeResponseContainsJson($expectedResponse);