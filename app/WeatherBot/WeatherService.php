<?php

namespace App\WeatherBot;

use Cache;
use App\Models\Weather as Weather;
use App\WeatherImportService;

class WeatherService
{

    // // https://raw.githubusercontent.com/ram-nadella/airport-codes/master/airports.json
    public function getAirportFromFeed()
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

    public function cleanKey($key)
    {
        $pieces = explode(' ', $key);
        $key    = array_pop($pieces);

        if (substr($key, -1) == '!' or substr($key, -1) == '?') {
            $key = substr($key, 0, -1);
        }

        return $key;
    }

    public function getFromDatabase($key)
    {
        $key = $this->cleanKey($key);

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
                if (strlen($key) === 3) {
                    $response = Weather::where('key', $key)->first();
                } else {
                    $response = Weather::where('city', $key)->first();

                    if (is_null($response)) {
                        return false;
                    }
                }
            }

            Cache::forever($cacheKey, $response->toArray());
        endif;

        return $response;
    }

    public function getForecast($key = null)
    {
        $cacheKey = 'weather.forecast.' . $key;
        $api_key  = config('services.forecast')['key'];

        $location = $this->getFromDatabase($key);

        if (is_array($location)) {
            $latitude  = $location['latitude'];
            $longitude = $location['longitude'];

            $weatherApi = 'https://api.forecast.io/forecast/' . $api_key . '/' . $latitude . ',' . $longitude . '?units=ca';

            if (Cache::has($cacheKey)):
                $weather = Cache::get($cacheKey);
            else:
                $weather = file_get_contents($weatherApi);
                $weather = json_decode($weather, true);

                Cache::put($cacheKey, $weather, now()->addMinutes(15));
            endif;

            return ['weather' => $weather, 'city' => $location];
        }

        return false;
    }

    public function makeMessage($var)
    {
        $weather = new \App\WeatherBot\WeatherService;

//        $code = substr($var, -3);

        $pieces = explode(' ', $var);
        $code   = array_pop($pieces);

        $forecast = $weather->getForecast($var);

        if ( ! $forecast) {
            return 'Sorry, There was an issue.';
        }

        $emoji = [
            'clear-day'           => '☀️',
            'clear-night'         => '🌌',
            'partly-cloudy-day'   => '🌤',
            'partly-cloudy-night' => '',
            'cloudy'              => '☁️',
            'rain'                => '🌧',
            'sleet'               => '🌨',
            'snow'                => '❄️',
            'wind'                => '💨️',
            'fog'                 => '🌫',
        ];

        $icon    = $emoji[$forecast['weather']['currently']['icon']];
        $in      = $forecast['city']['city'];
        $now     = $forecast['weather']['currently']['summary'];
        $nowTemp = $forecast['weather']['currently']['temperature'];
        $later   = $forecast['weather']['daily']['summary'];

//        return "Right now it's " . $nowTemp . "c and " . $now . " " . $icon . " in " . $in . '. Later this week: ' . $later;
        return "Right now it's " . $nowTemp . "c and " . $now . " " . $icon . " in " . $in . ".";
    }
}
