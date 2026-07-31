# Project Instructions

## Project Overview

This repository contains a PHP library for accessing OpenWeather APIs. It uses
Composer with PSR-4 autoloading under the
`ProgrammatorDev\OpenWeatherMap` namespace and is built on
`programmatordev/php-api-sdk`.

## Sources Of Truth

- Use the official OpenWeather documentation for endpoint paths, parameters,
  response fields, availability, and subscription constraints.
- Use the installed PHP API SDK documentation and source for its supported
  authoring patterns.
- Read existing resources, entities, tests, and documentation before changing
  related behavior.
- Do not infer API availability from OpenWeather documentation sidebars; verify
  it against the current official API catalog or pricing information.

## Code Changes

- Prefer focused changes that follow the architecture and naming conventions
  documented for the active target version. Do not preserve legacy patterns
  when the active public contract intentionally replaces them.
- Reuse shared entities, helpers, resource behavior, test utilities, and
  constants when they fit.
- Keep endpoint construction in resource classes and response mapping in typed
  entity or response classes.
- Keep request-local fluent options immutable so they do not affect later
  resource calls.
- Treat official documentation and representative response fixtures as
  complementary schema evidence; neither source is exhaustive on its own.
- Tolerate missing, explicitly `null`, conditional, and unknown response fields.
  Reject known non-null fields with invalid types through descriptive hydration
  errors rather than silent coercion.
- Represent returned timestamps as nullable UTC `DateTimeImmutable` values and
  keep location timezone identifiers or offsets as separate metadata.
- Make destructive operations explicit in method naming and documentation.
- Do not expose API keys through exceptions, logs, fixtures, or committed
  example files.

## Dependencies And Tooling

- Run project PHP and Composer commands through DDEV.
- Respect the PHP versions declared by `composer.json` and CI.
- Remove a dependency only when its remaining usages have been eliminated.
- Do not introduce a formatter, static analyzer, or new test framework without
  explicit approval.

## Testing

- Use PHPUnit and the existing PSR-18 mock-client approach.
- Do not make live OpenWeather requests in the automated test suite.
- Build automated response tests from sanitized real API captures, supplemented
  by synthetic edge-case fixtures. Keep credentials and private or
  account-specific data out of committed fixtures.
- Test endpoint method, URL, path parameters, query parameters, headers, body,
  response mapping, error mapping, and immutable resource-chain behavior.
- Add focused entity tests for present, missing, explicitly `null`,
  conditionally present, unknown, invalidly typed, and nested fields.
- Cover empty-body responses for successful write and delete operations.
- Run the full test suite before handing off an implementation batch when
  practical.

## Documentation

- Update public documentation alongside implemented API areas.
- Keep method signatures, examples, supported endpoints, and response entities
  aligned with the implementation.
- Clearly distinguish standard free-plan APIs from APIs requiring separate or
  paid subscriptions.
- Document potentially billable or destructive behavior prominently.
