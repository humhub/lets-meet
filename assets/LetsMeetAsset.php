<?php

namespace humhub\modules\letsMeet\assets;

use yii\web\AssetBundle;
use yii\web\View;

class LetsMeetAsset extends AssetBundle
{
    public $jsOptions = ['position' => View::POS_END];
    public $sourcePath = '@lets-meet/resources';
    public $css = [];
    public $js = [
        'js/humhub.lets-meet.js',
    ];

    /**
     * @param View $view
     * @return AssetBundle
     */
    public static function register($view)
    {
        $view->registerJsConfig('letsMeet', [
            'text' => [
                // module text translations
            ],
        ]);
        return parent::register($view);
    }
}
