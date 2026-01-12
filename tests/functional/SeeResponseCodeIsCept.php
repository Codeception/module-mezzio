<?php

use tests\_support\FunctionalTester;

$I = new FunctionalTester($scenario);
$I->wantTo('see different response code');

$I->amOnPage('/error');
$I->seeResponseCodeIs(404);
