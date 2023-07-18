# Error Handling

- [API Errors](#api-errors)
- [Validation Errors](#validation-errors)

## API Errors

To handle API response errors, multiple exceptions are provided. You can see all available in the following example:

```php
use ProgrammatorDev\OpenWeatherMap\Exception\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnexpectedErrorException;

try {
    $location = $openWeatherMap->getGeocoding()->getByZipCode('1000-001', 'pt');
    
    $weather = $openWeatherMap->getOneCall()->getWeather(
        $location->getCoordinate()->getLatitude(),
        $location->getCoordinate()->getLongitude()
    );
}
// Bad requests to the API
// If this library is making a good job validating input data, this should not happen
catch (BadRequestException $exception) {
    echo $exception->getCode(); // 400
    echo $exception->getMessage();
}
// Invalid API key or trying to request an endpoint with no granted access
catch (UnauthorizedException $exception) {
    echo $exception->getCode(); // 401
    echo $exception->getMessage();
}
// Resource not found
// For example, when trying to get a location with a zip/post code that does not exist
catch (NotFoundException $exception) {
    echo $exception->getCode(); // 404
    echo $exception->getMessage();
}
// API key requests quota exceeded
catch (TooManyRequestsException $exception) {
    echo $exception->getCode(); // 429
    echo $exception->getMessage();
}
// Any other error, probably an internal error
catch (UnexpectedErrorException $exception) {
    echo $exception->getCode(); // 5xx
    echo $exception->getMessage();
}
```

To catch all API errors with a single exception, `ApiErrorException` is available:

```php
use ProgrammatorDev\OpenWeatherMap\Exception\ApiErrorException;

try {
    $location = $openWeatherMap->getGeocoding()->getByZipCode('1000-001', 'pt');
    
    $weather = $openWeatherMap->getOneCall()->getWeather(
        $location->getCoordinate()->getLatitude(),
        $location->getCoordinate()->getLongitude()
    );
}
// Catches all API response errors
catch (ApiErrorException $exception) {
    echo $exception->getCode();
    echo $exception->getMessage();
}
```

## Validation Errors

To catch invalid input data (like an out of range coordinate, blank location name, etc.), 
the `ValidationException` is available:

```php
use ProgrammatorDev\OpenWeatherMap\Exception\ValidationException;

try {
    // An invalid latitude value is given
    $weather = $openWeatherMap->getWeather()->getCurrent(999, 50);
}
catch (ValidationException $exception) {
    // Should print:
    // The "latitude" value "999" is invalid. Must be between "-90" and "90".
    echo $exception->getMessage();
}
```