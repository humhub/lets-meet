<?php

namespace humhub\modules\letsMeet\assets;

use yii\web\AssetBundle;
use yii\web\View;


class LetsMeetAsset extends AssetBundle
{
    public $sourcePath = '@lets-meet/resources';
    public $css = [
        'css/lets-meet.css'
    ];
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
                'success' => [
                    'saved' => 'Successfully Saved',
                ],
                'error' => [
                    'unsuccessful' => 'Something went wrong.',
                ],
            ],
        ]);
        return parent::register($view);
    }

    public $publishOptions = [
        'forceCopy' => true,
    ];
}
