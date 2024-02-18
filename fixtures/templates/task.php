<?php

use app\models\User;
use yii\db\Expression;

$faker = Faker\Factory::create('ru_RU');

return [
    'client_id' => User::find()->select('id')->orderBy(new Expression('rand()'))->scalar(),
    'taskStatus_id' => 1,
    'name'        => $faker->word(),
    'category_id' => rand(1, 8),
    'location' => $faker->city,
    'budget' => rand(0, 1111),
    'file' => null,
    'finished' => null,
    'description' => $faker->realText(120),
    'deadline'    => $faker->dateTimeBetween('now', '+1 month')->format(
        'Y-m-d H:i:s'
    ),
    'dateCreate' => $faker->dateTimeBetween('-1 month')->format('Y-m-d H:i:s')
];