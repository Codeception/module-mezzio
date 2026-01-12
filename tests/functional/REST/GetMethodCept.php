<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('make GET request');
$I->sendGET('/rest');
$I->seeResponseIsJson();
$expectedResponse = array(
    'requestMethod' => 'GET',
);
$I->seeResponseContainsJson($expectedResponse);
