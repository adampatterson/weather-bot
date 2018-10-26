<?php

namespace App\Weather;

use Cache;

class WeatherService
{

    public function get()
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
        return $this->get();
    }
}
