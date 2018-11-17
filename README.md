# WeatherBot

### How the Bot works**

This is a quick and dirty Slack Bot experiment using [BotMan](https://botman.io/) and the [DarkSky API](https://darksky.net).

Sending a string using an International Air Transport Association or IATA code will return that locations weather conditions using the [DarkSky API](https://darksky.net).

The string or phrase like `Whats the weather in yeg`, or `/weather in Edmonton` will return a formatted string.

"Right now it's -12.54c and Light Snow ❄️ in Edmonton."

### Requirements**
* Running Laravel 5.7
* Memcached

### Setting up the Bot

To get up and running quickly I recommend using [Ngrok](https://ngrok.com/) which will allow you to proxy your local connection externally.

For the weather you will need to create an API key over at [Dark Sky](https://darksky.net/dev).
 
Create a new Database, add your credentials to the `.env` file along with your Darksky API Key.  

```
  SLACK_BOT_TOKEN=token
  FORECAST_API=key
```

Next run `composer install`, `artisan migrate` to setup the Database.

Our data will come from a json file containing all of the airport codes which will give us the city names as well as the lat log locations that will be used for the weather API call.

Next run `artisan weather:import`.

### Setting up Slack
 
To use the bot with Slack you will have to register a new [Bot](https://api.slack.com/apps/new).
 
Name your App and choose your Development workspace.
  
**Slash Command**
Under Features add a Slash Command add `/weather` and for the quest URL use you Ngrok url (https://random.ngrok.io/botman)

**Event Subscriptions**
In order for Slack to send our bot any information we need to subscribe to some specific actions.

Add `message.channels, message.im`, again we need to add the bot URL (https://random.ngrok.io/botman)

**Bot Users**
Under `Bot Users` click **Add Bot User**.

**Authentication**
Under `OAuth & Permissions` then `Scopes` add the following:

**CONVERSATIONS**
* channels:history
* channels:read
* channels:write
* chat:write:bot
* groups:history
* im:history

**INTERACTIVITY**
* bot
* commands

Then `Install App to Workspace` authorize the bot with your workspace and add the **Bot User OAuth Access Token** to your `.env` file.
