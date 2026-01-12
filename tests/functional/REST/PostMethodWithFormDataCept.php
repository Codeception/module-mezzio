<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('make POST request with form data');
$I->sendPOST('/rest', array('foo' => 'bar'));

$expectedResponse = array(
    'requestMethod' => 'POST',
    'formParams' => array(
        'foo' => 'bar'
    ),
);
$I->seeResponseContainsJson($expectedResponse);
