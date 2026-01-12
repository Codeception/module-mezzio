<?php

class SessionCest
{
    public function firstTimeUsingSession(FunctionalTester $I)
    {
        $I->amOnPage('/session/get');
        $I->see('Nobody');

        $I->amOnPage('/session/set');
        $I->see('Name set');

        $I->amOnPage('/session/get');
        $I->see('Somebody');
    }

    public function secondTimeUsingSessionMustNotBeAffectedByFirstTime(FunctionalTester $I)
    {
        $I->amOnPage('/session/get');
        $I->see('Nobody');
    }
}
