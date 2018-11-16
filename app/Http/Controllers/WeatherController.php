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
        $forecast = $weatherService->getForecast($key);

        return (is_array($forecast)) ? $forecast : [false];
    }

    public function message(\App\WeatherBot\WeatherService $weatherService, $var = null)
    {
        return $weatherService->makeMessage($var);
    }

    public function test(\App\WeatherBot\WeatherService $weatherService)
    {
        return $weatherService->makeMessage(\Request::get('message'));
    }
}