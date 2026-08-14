# Upgrading To 4.0

Version 4 is a complete rewrite. It is not backward compatible with earlier
releases. Existing integrations should adopt it as a new implementation, even
where the usage looks familiar.

## Upgrade Expectations

- Previous resources, entities, methods, namespaces, and configuration are not
  part of the current public contract.
- Compatibility aliases, transitional APIs, and an old-to-new API mapping are
  not provided.
- Integrations should be rebuilt against the current [README](README.md) and
  [API guides](README.md#apis).
- Application tests should be reviewed and updated before adopting the new
  release.

## Current Baseline

- PHP 8.1 or later is required.
- The client is built on
  [`programmatordev/php-api-sdk` 3](https://github.com/programmatordev/php-api-sdk).
- Current weather, forecasts, air pollution, geocoding, maps, stations, and One
  Call 4.0 are supported.
- Metric units and English are the defaults. They can be configured for the
  client or changed for one request chain where supported.
- Response entities accept missing, `null`, conditional, and unknown fields.
  Known non-null fields with invalid types produce hydration errors.
- OpenWeather API failures use a documented exception hierarchy; transport,
  decoding, and hydration failures remain distinguishable. See
  [Error Handling](docs/errors.md).

The current documentation defines the supported behavior for this release.
