<?php

namespace App\WeatherBot;

use Cache;
use App\Models\Weather as Weather;
use App\WeatherImportService;

class WeatherService
{

    // // https://raw.githubusercontent.com/ram-nadella/airport-codes/master/airports.json
    public function getFromFeed()
    {
        $cacheKey = 'weather.iata.json';

        if (Cache::has($cacheKey)):
            $response = Cache::get($cacheKey);
        else:
            $response = file_get_contents('https://raw.githubusercontent.com/ram-nadella/airport-codes/master/airports.json');
            $response = json_decode($response);
            Cache::forever($cacheKey, $response);
        endif;

        return $response;
    }

    public function getFromDatabase($key)
    {
        if (is_null($key)) {
            $cacheKey = 'weather.iata.db.all';
        } else {
            $cacheKey = 'weather.iata.db.' . $key;
        }

        if (Cache::has($cacheKey)):
            $response = Cache::get($cacheKey);
        else:
            if (is_null($key)) {
                $response = Weather::all()->toArray();
            } else {
                $response = Weather::where('key', $key)->get()->toArray();
            }
            Cache::forever($cacheKey, $response);
        endif;

        return $response;
    }
}
