<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('make DELETE request');

$I->sendDELETE('/rest');
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('requestMethod' => 'DELETE'));
