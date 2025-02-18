<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

return [
    [
        'id' => 101,
        'class' => 'humhub\modules\content\activities\ContentCreated',
        'module' => 'content',
        'object_model' => 'humhub\modules\letsMeet\models\Meeting',
        'object_id' => 1,
    ],
    [
        'id' => 102,
        'class' => 'humhub\modules\comment\activities\NewComment',
        'module' => 'comment',
        'object_model' => 'humhub\modules\comment\models\Comment',
        'object_id' => 201,
    ],
];
