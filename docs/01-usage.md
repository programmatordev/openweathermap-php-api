# Using OpenWeatherMap PHP API

- [Requirements](#requirements)
- [API Key](#api-key)
- [Installation](#installation)
- [Basic Usage](#basic-usage)

## Requirements

- PHP 8.1 or higher.

## API Key

A key is required to be able to make requests to the API.
You must sign up for an [OpenWeatherMap account](https://openweathermap.org/appid#signup) to get one.

## Installation

You can install the library via [Composer](https://getcomposer.org/):

```bash
composer require programmatordev/openweathermap-php-api
```

To use the library, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once 'vendor/autoload.php';
```

## Basic Usage

Simple usage looks like:

```php
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

// Initialize
$openWeatherMap = new OpenWeatherMap(
    new Config([
        'applicationKey' => 'yourappkey'
    ])
);

// Get current weather by coordinate (latitude, longitude)
$currentWeather = $openWeatherMap->weather->getCurrent(50, 50);
// Show current temperature
echo $currentWeather->getTemperature();
```