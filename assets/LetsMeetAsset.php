<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\assets;

use Yii;
use yii\web\AssetBundle;


class LetsMeetAsset extends AssetBundle
{
    public $sourcePath = '@lets-meet/resources';
    public $css = [
        'css/lets-meet.css',
        'css/lets-meet-wall-entry.css',
    ];
    public $js = [
        'js/humhub.lets-meet.js',
    ];

    public static function register($view)
    {
        $view->registerJsConfig('letsMeet', [
            'text' => [
                'error' => [
                    'unsuccessful' => Yii::t('LetsMeetModule.base', 'Something went wrong.'),
                ],
            ],
        ]);

        return parent::register($view);
    }

    public $publishOptions = [
        'forceCopy' => YII_ENV_DEV,
    ];
}
