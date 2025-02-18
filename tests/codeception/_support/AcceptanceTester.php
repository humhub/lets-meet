<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace letsMeet;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \AcceptanceTester
{
    use _generated\AcceptanceTesterActions;

    /**
     * Define custom actions here
     */

    public function clickEditFromContextMenu()
    {
        $this->waitForElementVisible('.wall_humhubmodulesletsMeetmodelsMeeting_1');
        $this->click('.preferences .dropdown-toggle', '.wall_humhubmodulesletsMeetmodelsMeeting_1');
        $this->waitForText('Edit');
        $this->click('Edit', '.wall_humhubmodulesletsMeetmodelsMeeting_1');
        $this->waitForText('Edit Let\'s Meet');
        $this->see('Edit Let\'s Meet');
    }

    public function clickNext()
    {
        $this->click('Next', '#globalModal');
    }

    public function clickSave()
    {
        $this->click('Save', '#globalModal');
    }

    public function clickConfirm()
    {
        $this->click('Confirm', '#globalModalConfirm');
    }
}
