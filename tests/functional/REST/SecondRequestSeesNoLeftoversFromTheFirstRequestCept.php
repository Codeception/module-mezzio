<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('ensure that request object is reset between requests');

//$I->haveHttpHeader('X-Auth-Token', 'verySecureToken');
$I->sendPOST('/rest?queryParam=1', ['foo' => 'bar']);

$expectedResponse = array(
    'requestMethod' => 'POST',
    'requestUri' => '/rest?queryParam=1',
    'queryParams' => ['queryParam' => '1'],
    'formParams' => ['foo' => 'bar'],
//    'X-Auth-Token' => 'verySecureToken',

);
$I->seeResponseContainsJson($expectedResponse);

//$I->haveHttpHeader('X-Auth-Token', null );
$I->sendGET('/rest');
$I->seeResponseIsJson();
$expectedResponse = array(
    'requestMethod' => 'GET',
    'requestUri' => '/rest',
    'queryParams' => [],
    'formParams' => [],
//    'X-Auth-Token' => null,

);

$I->seeResponseContainsJson($expectedResponse);
