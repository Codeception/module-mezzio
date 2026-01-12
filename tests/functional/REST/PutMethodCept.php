<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('make PUT request');

$I->sendPUT('/rest');
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('requestMethod' => 'PUT'));
