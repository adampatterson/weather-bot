<?php

namespace App\Http\Controllers;

class WeatherController extends Controller
{

    public function import(\App\WeatherBot\WeatherImportService $weatherImportService)
    {
        return $weatherImportService->import();
    }

    public function get(\App\WeatherBot\WeatherService $weatherService, $key = null)
    {
        return $weatherService->getFromDatabase($key);
    }
}