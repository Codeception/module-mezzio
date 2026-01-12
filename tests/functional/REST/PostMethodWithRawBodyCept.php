<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('make POST request with raw body');
$I->sendPOST('/rest', 'raw request body');

$expectedResponse = array(
    'requestMethod' => 'POST',
    'formParams' => array(),
    'rawBody' => 'raw request body',
);
$I->seeResponseContainsJson($expectedResponse);
