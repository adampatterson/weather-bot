<?php

use Carbon\Carbon;
use Numeral\Numeral;
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('Whats {var}', function ($bot, $var) {
    $weatherService = new \App\WeatherBot\WeatherService;

    $message = $weatherService->makeMessage($var);

    $bot->reply($message);
});

$botman->hears('/weather {var}', function ($bot, $var) {
    $weatherService = new \App\WeatherBot\WeatherService;

    $message = $weatherService->makeMessage($var);

    $bot->reply($message);
});