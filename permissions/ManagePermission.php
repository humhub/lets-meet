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
class ManagePermission extends BasePermission
{
    protected $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
        User::USERGROUP_SELF,
    ];

    protected $fixedGroups = [
        Space::USERGROUP_GUEST,
        Space::USERGROUP_USER,
        User::USERGROUP_SELF,
        User::USERGROUP_FRIEND,
        User::USERGROUP_USER,
        User::USERGROUP_GUEST,
    ];

    protected $moduleId = 'lets-meet';

    public function getTitle()
    {
        return Yii::t('LetsMeetModule.permission', 'Manage Let\'s Meet');
    }

    public function getDescription()
    {
        return Yii::t('LetsMeetModule.permission', 'Can manage Let\'s Meet');
    }
}
