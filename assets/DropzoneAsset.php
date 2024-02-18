<?php

namespace app\assets;

use yii\web\AssetBundle;

class DropzoneAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'dropzone/dropzone.css'
    ];
    public $js = [
        'dropzone/dropzone.js',
    ];
    public $depends = [
    ];
}