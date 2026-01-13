<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('make PUT request with query string');
$I->sendPUT('/rest?param=value');

$expectedResponse = array(
    'requestMethod' => 'PUT',
    'queryParams' => array(
        'param' => 'value'
    ),
);
$I->seeResponseContainsJson($expectedResponse);
