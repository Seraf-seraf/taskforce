<?php

namespace app\assets;
use yii\web\AssetBundle;

class YandexMapAsset extends AssetBundle
{
    public function init()
    {
        parent::init();

        $apiKey = getenv('YANDEX_API_KEY');
        $this->js[] = "https://api-maps.yandex.ru/2.1/?apikey=$apiKey&lang=ru_RU&suggest_apikey=09950492-b7fc-47e3-8102-ff67f08b52e4";
    }
}