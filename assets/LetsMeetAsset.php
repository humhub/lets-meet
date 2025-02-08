<?php

namespace humhub\modules\letsMeet\assets;

use Yii;
use yii\web\AssetBundle;
use yii\web\View;


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
        'forceCopy' => true,
    ];
}
