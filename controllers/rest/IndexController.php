<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\controllers\rest;

use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\ContentContainer;
use humhub\modules\rest\components\BaseContentController;
use Yii;

class IndexController extends BaseContentController
{
    public static $moduleId = 'lets-meet';

    public function getContentActiveRecordClass()
    {

    }

    public function returnContentDefinition(ContentActiveRecord $contentRecord)
    {
    }
}
