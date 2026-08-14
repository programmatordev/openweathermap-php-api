# Error Handling

The library separates OpenWeather API errors from invalid input, HTTP client
failures, decoding errors, and invalid response data.

## OpenWeather API Errors

Every mapped HTTP error extends `ApiException`, so applications can catch one
type for all unsuccessful OpenWeather responses.

```php
use ProgrammatorDev\OpenWeatherMap\Exception\ApiException;

try {
    $current = $api->weather()->current(
        latitude: 38.7223,
        longitude: -9.1393,
    );
} catch (ApiException $exception) {
    echo $exception->getMessage();
    echo $exception->statusCode();
    echo $exception->apiCode();

    $responseData = $exception->responseData();
}
```

`getMessage()` returns OpenWeather's non-empty error message when one is
available. Otherwise, it describes the HTTP status. `statusCode()` always
returns the HTTP response status, while `apiCode()` returns OpenWeather's
numeric `cod` or `code` value when the response provides one. `responseData()`
exposes the decoded error payload.

Known HTTP statuses use dedicated exceptions:

| HTTP status | Exception |
| ---: | --- |
| `400` | `BadRequestException` |
| `401` | `UnauthorizedException` |
| `404` | `NotFoundException` |
| `429` | `TooManyRequestsException` |
| Other `4xx` or `5xx` | `UnexpectedErrorException` |

Catch a specific exception before `ApiException` when the application needs
different behavior for that failure.

```php
use ProgrammatorDev\OpenWeatherMap\Exception\ApiException;
use ProgrammatorDev\OpenWeatherMap\Exception\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnauthorizedException;

try {
    $current = $api->weather()->current(38.7223, -9.1393);
} catch (TooManyRequestsException $exception) {
    // Defer or slow down further requests.
} catch (UnauthorizedException $exception) {
    // Check the API key and whether it can use this endpoint.
} catch (ApiException $exception) {
    // Handle any other unsuccessful OpenWeather response.
}
```

## Other Failures

Failures that occur before an OpenWeather error response is mapped do not
extend `ApiException`:

| Type | Meaning |
| --- | --- |
| `InvalidArgumentException` | A method argument or client option is invalid, so no request is sent. |
| `Psr\Http\Client\ClientExceptionInterface` | The PSR-18 client could not complete the HTTP request. |
| `JsonException` | A successful response expected to contain JSON could not be decoded. |
| `HydrationException` | A known response property contains an invalid type or value. |
| `UnexpectedValueException` | A non-JSON response, such as a map tile, has unexpected content or a wrong content type. |

Missing, explicitly `null`, conditional, and unknown response properties are
tolerated. `HydrationException` is reserved for known non-null properties whose
types or values do not match the response contract.
