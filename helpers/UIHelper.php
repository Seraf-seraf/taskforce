<?php

namespace app\helpers;

/**
 * Вспомогательный класс для работы с интерфесом сайта
 */
class UIHelper
{

    /**
     * Плюрализация для русских слов.
     *
     * @param $n     int целое число
     * @param $forms array принимает массив с 3 формами слова: в единственном
     *               числе и 2 формы множественного числа
     *
     * @return string
     */
    public static function pluralize(string $n, array $forms): string
    {
        return $n % 10 == 1 && $n % 100 != 11 ? $forms[0] : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? $forms[1] : $forms[2]);
    }

    /**
     * Создание контейнера со звездочками рейтинга
     *
     * @param  float  $rating
     * @param  string  $size  small or big
     *
     * @return string
     */
    public static function getStarsByRate(
        float $rating,
        string $size = 'small'
    ): string {
        $stars = "<div class='stars-rating $size'>";

        for ($i = 0; $i < 5; $i++) {
            if ($i < $rating) {
                $stars .= "<span class='fill-star'></span>";
            } else {
                $stars .= '<span></span>';
            }
        }

        $stars .= "</div>";

        return $stars;
    }

}