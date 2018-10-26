<?php

use Carbon\Carbon;
use Numeral\Numeral;
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});

$botman->hears('Weather', function ($bot) {
    $bot->reply("23c and ☀️");
});

$botman->hears('/weather {var}', function ($bot, $var) {
    $cacheKey = 'weather.iata';

    if (Cache::has($cacheKey)):
        $response = Cache::get($cacheKey);
    else:
        $response = file_get_contents('https://raw.githubusercontent.com/ram-nadella/airport-codes/master/airports.json');
        $response = json_decode($response);
        Cache::forever($cacheKey, $response);
    endif;

    $bot->reply("23c and ☀️ in " . $var);
});

$botman->hears('/lookup {var}', function ($bot, $var) {
    $response = file_get_contents('https://www.goauto.ca/inventory/search/json?stock_number=' . $var);
    $response = json_decode($response);

    $vehicle = $response->results[0];
    $price   = $vehicle->list_price;

    $bodyStyle = $vehicle->body_type_name;

    $bodyStyle = ($bodyStyle == 'Sedan') ? '🚘' : '🚙';

    $bot->reply('This ' . $bodyStyle . ' is ' . Numeral::number($price)->format('$0,0.00'));
});


$botman->hears('Lets Chat', BotManController::class . '@startConversation');

$botman->hears('Hello BotMan!', function ($bot) {
    $bot->reply('Hello!');
    $bot->ask('Whats your name?', function ($answer, $bot) {
        $bot->say('Welcome ' . $answer->getText());
    });
});