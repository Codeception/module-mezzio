<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('make DELETE request with query string');
$I->sendDELETE('/rest?param=value');

$expectedResponse = array(
    'requestMethod' => 'DELETE',
    'queryParams' => array(
        'param' => 'value'
    ),
);
$I->seeResponseContainsJson($expectedResponse);
