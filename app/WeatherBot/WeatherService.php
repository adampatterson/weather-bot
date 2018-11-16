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

    /**
     * While it is possible to look up the weather based on airport codes like YEG
     * or a City name like Edmonton. There are some cities like New York or San Fransisco
     * that cause problems.
     *
     * @param $string
     *
     * @return mixed
     */
    public function cleanKey($string)
    {
        $cleanKey = str_replace(['?', '!', '.'], "", $string);

        $pieces = explode(' ', $cleanKey);

        if (count($pieces) > 2) {
            $dirtyCity = array_slice($pieces, -2);

            $key['first']  = $dirtyCity[1];
            $key['second'] = $dirtyCity[0];
        } else {
            $key['first']  = array_pop($pieces);
            $key['second'] = null;
        }

        return $key;
    }

    public function getFromDatabase($key)
    {
        if (is_null($key)) {
            $cacheKey = 'weather.iata.db.all';

            return Weather::all()->toArray();
        } else {
            $cacheKey = 'weather.iata.db.' . str_slug($key, '-');
        }

        if (Cache::has($cacheKey)):
            $response = Cache::get($cacheKey);
        else:

            if (strlen($key) === 3) {
                $response = Weather::where('key', $key)->first();
            } else {
                $response = Weather::where('city', $key)->first();

                if (is_null($response)) {
                    return false;
                }
            }

            Cache::forever($cacheKey, $response);
        endif;

        return $response->toArray();
    }

    public function getForecast($key = null)
    {
        $cacheKey = 'weather.forecast.' . str_slug($key, '_');
        $api_key  = config('services.forecast')['key'];

        $key = $this->cleanKey($key);

        $location = $this->getFromDatabase($key['first']);

        if ($location === false) {
            $location = $this->getFromDatabase($key['second'] . ' ' . $key['first']);
        }

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
//        $code = substr($var, -3);

//        $pieces = explode(' ', $var);
//        $code   = array_pop($pieces);

        $forecast = $this->getForecast($var);

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
