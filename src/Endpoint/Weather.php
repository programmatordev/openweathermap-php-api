<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

class Weather extends AbstractEndpoint
{
    private string $urlCurrentWeather = 'https://api.openweathermap.org/data/2.5/weather';

    private string $urlWeatherForecast = 'https://api.openweathermap.org/data/2.5/forecast';


}