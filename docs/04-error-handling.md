# Error Handling

## API Errors

To handle API response errors, multiple exceptions are provided. You can see all available in the following example:

```php
use ProgrammatorDev\OpenWeatherMap\Exception\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnexpectedErrorException;

try {
    // ...
    
    $weather = $api->oneCall()->getWeather($latitude, $longitude);
}
// bad request to the API
catch (BadRequestException $exception) {
    echo $exception->getCode(); // 400
    echo $exception->getMessage();
}
// invalid API key or trying to request an endpoint with no granted access
catch (UnauthorizedException $exception) {
    echo $exception->getCode(); // 401
    echo $exception->getMessage();
}
// resource not found,
// for example, when trying to get a location with a zip code that does not exist
catch (NotFoundException $exception) {
    echo $exception->getCode(); // 404
    echo $exception->getMessage();
}
// API key requests quota exceeded
catch (TooManyRequestsException $exception) {
    echo $exception->getCode(); // 429
    echo $exception->getMessage();
}
// any other error, probably an internal error
catch (UnexpectedErrorException $exception) {
    echo $exception->getCode(); // 5xx
    echo $exception->getMessage();
}
```

To catch all API errors with a single exception, `ApiErrorException` is available:

```php
use ProgrammatorDev\OpenWeatherMap\Exception\ApiErrorException;

try {
    // ...

    $weather = $api->oneCall()->getWeather($latitude, $longitude);
}
// catches all API response errors
catch (ApiErrorException $exception) {
    echo $exception->getCode();
    echo $exception->getMessage();
}
```