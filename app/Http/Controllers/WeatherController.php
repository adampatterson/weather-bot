<?php

namespace App\Http\Controllers;

class WeatherController extends Controller
{

    public function import(\App\Weather\WeatherService $weather)
    {
        dd($weather->get());;
    }
}