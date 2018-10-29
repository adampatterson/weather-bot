<?php

use Carbon\Carbon;
use Numeral\Numeral;
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});

$botman->hears('Whats {var}', function ($bot, $var) {
    $weatherService = new \App\WeatherBot\WeatherService;

    $code = substr($var, -3);

    $message = $weatherService->makeMessage($code);

    $bot->reply($message);
});

$botman->hears('/weather {var}', function ($bot, $var) {
    $weatherService = new \App\WeatherBot\WeatherService;

    $code = substr($var, -3);

    $message = $weatherService->makeMessage($code);

    $bot->reply($message);
});