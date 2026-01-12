<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('pass request uri');
$I->sendGET('/rest?param=value&param2=value2');
$I->seeResponseIsJson();
$expectedResponse = array(
    'requestUri' => '/rest?param=value&param2=value2',
);
$I->seeResponseContainsJson($expectedResponse);
