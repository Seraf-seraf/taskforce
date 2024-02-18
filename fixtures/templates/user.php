<?php

return [
    'name' => $faker->name,
    'email' => $faker->email,
    'password' => Yii::$app->getSecurity()->generatePasswordHash('qwerty'),
    'city_id' => rand(1, 1000),
    'isPerformer' => rand(0, 1)
];