<?php

namespace humhub\modules\letsMeet\assets;

use humhub\modules\ui\view\components\View;
use Yii;
use yii\web\AssetBundle;

class LetsMeetAsset extends AssetBundle
{
    public $jsOptions = ['position' => \yii\web\View::POS_END];
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
