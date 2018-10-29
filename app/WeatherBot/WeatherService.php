<?php

namespace App\Weather;

use Cache;
use App\WeatherImportService;

class WeatherService
{

    public function getFromFeed()
    {
        $cacheKey = 'weather.iata';

        if (Cache::has($cacheKey)):
            $response = Cache::get($cacheKey);
        else:
            $response = file_get_contents('https://raw.githubusercontent.com/ram-nadella/airport-codes/master/airports.json');
            $response = json_decode($response);
            Cache::forever($cacheKey, $response);
        endif;

        return $response;
    }

    public function import()
    {
        $weather = new Weather;

        // Soft Delete everything


        // Import new content from the JSON feed.
        $this->getFromFeed();


        return;
    }
}
