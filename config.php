<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\user\models\User;
use humhub\commands\IntegrityController;
use humhub\modules\content\widgets\WallEntryControls;

/** @noinspection MissedFieldInspection */
return [
    'id' => 'lets-meet',
    'class' => 'humhub\modules\letsMeet\Module',
    'namespace' => 'humhub\modules\letsMeet',
    'events' => [
        ['class' => WallEntryControls::class, 'event' => WallEntryControls::EVENT_INIT, 'callback' => ['humhub\modules\letsMeet\Events', 'onWallEntryControlsInit']],
        ['class' => User::class, 'event' => User::EVENT_BEFORE_DELETE, 'callback' => ['humhub\modules\letsMeet\Events', 'onUserDelete']],
    ],
];
