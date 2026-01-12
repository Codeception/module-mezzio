<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('make POST request');

$I->sendPOST('/rest');
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('requestMethod' => 'POST'));
