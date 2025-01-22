<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\permissions;

use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use Yii;

/**
 * CreatePost Permission
 */
class CreateLetsMeet extends BasePermission
{
    public $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
        Space::USERGROUP_MEMBER,
        User::USERGROUP_SELF,
    ];

    protected $fixedGroups = [
//        Space::USERGROUP_OWNER,
    ];

    protected $moduleId = 'lets-meet';

    public function getTitle()
    {
        return Yii::t('LetsMeetModule.base', 'Lets meet');
    }

    public function getDescription()
    {
        return Yii::t('LetsMeetModule.base', 'Lets meet description');
    }
}
