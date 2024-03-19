<?php

namespace app\helpers;

use Yandex\Geo\Api;
use Yandex\Geo\Exception;

class YandexMapHelper
{
    private Api $apiClient;

    public function __construct($key)
    {
        $this->apiClient = new Api();
        $this->apiClient->setToken($key);
    }

    public function getCoords($city, $location): bool|array
    {
        $api = $this->apiClient;

        try {
            $api->setQuery($city . ',' . $location);
            $api
                ->setLimit(1)
                ->load();

            $response = $api->getResponse();
            $result = $response->getList();

            if ($result) {
                $object = $result[0];

                if ($object->getLocalityName() != $city) {
                    return false;
                }

                return [$object->getLatitude(), $object->getLongitude()];
            }
            
        } catch (Exception $error) {
            error_log($error->getMessage());
        }

        return false;
    }
}
