<?php

namespace humhub\modules\letsMeet\assets;

use yii\web\AssetBundle;
use yii\web\View;
class WallEntryAsset extends AssetBundle
{
    public $sourcePath = '@lets-meet/resources';
    public $css = [
        'css/lets-meet-wall-entry.css'
    ];
    public $js = [
        'js/humhub.lets-meet-wall-entry.js',
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

    public $publishOptions = [
        'forceCopy' => true,
    ];
}
