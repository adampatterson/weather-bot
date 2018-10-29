<?php

namespace App\WeatherBot;

use Cache;
use App\Models\Weather as Weather;
use App\WeatherBot\WeatherService as WeatherService;

class WeatherImportService
{

    public function import()
    {
        // Soft Delete everything

        // Import new content from the JSON feed.
        $feed = (new WeatherService)->getFromFeed();

        foreach ($feed as $key => $value) {
            $record = [
                'key'       => $key,
                'name'      => $value->name,
                'city'      => $value->city,
                'country'   => $value->country,
                'iata'      => $value->iata,
                'icao'      => $value->icao,
                'latitude'  => $value->latitude,
                'longitude' => $value->longitude,
                'altitude'  => $value->altitude,
                'timezone'  => $value->timezone,
                'dst'       => $value->dst
            ];

            $weather = Weather::updateOrCreate($record, ['key' => $key]);
        }

        return $feed;
    }
}