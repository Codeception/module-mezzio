<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('make PUT request with raw body');
$I->sendPUT('/rest', 'raw request body');

$expectedResponse = array(
    'requestMethod' => 'PUT',
    'formParams' => array(),
    'rawBody' => 'raw request body',
);
$I->seeResponseContainsJson($expectedResponse);
