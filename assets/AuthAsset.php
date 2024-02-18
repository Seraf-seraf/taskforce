<?php
/**
 * @link      https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Landing application asset bundle.
 */
class AuthAsset extends AssetBundle
{

    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        'css/normalize.css',
        'css/landing.css',
    ];

    public $js = [
        'js/landing.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];

}