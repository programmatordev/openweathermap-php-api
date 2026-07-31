# Response Fixtures

Automated tests use committed JSON fixtures derived from representative real
OpenWeather responses. Tests must never make live OpenWeather requests.

## Naming

Store fixtures by product, endpoint, and scenario:

```text
tests/Fixtures/<product>/<endpoint>/<scenario>.json
tests/Fixtures/<product>/<endpoint>/<scenario>.meta.json
```

For example:

```text
tests/Fixtures/geocoding/direct/success.json
tests/Fixtures/geocoding/direct/success.meta.json
```

Use stable endpoint and scenario names such as `success`, `empty`,
`missing-optional-fields`, or `invalid-request`. Do not include a captured
location name in a filename because the returned name may change or be absent.

## Metadata

Every response fixture must have a sidecar with the same basename. A captured
response sidecar follows this shape:

```json
{
    "provenance": "captured",
    "product": "Geocoding API",
    "endpoint": "Direct geocoding",
    "apiVersion": "1.0",
    "capturedAt": "2026-07-31T12:00:00Z",
    "request": {
        "method": "GET",
        "path": "/geo/1.0/direct",
        "query": {
            "q": "Lisbon,PT",
            "limit": 5
        }
    },
    "sanitization": []
}
```

Record non-secret request parameters, including coordinates when applicable.
Never include an API key. Synthetic fixtures use `"provenance": "synthetic"`
and describe why they were created in a `notes` field.

## Sanitization

Keep captured payloads as close to the real response as possible. Record every
redaction or replacement in `sanitization`, including its JSON path and the
action performed:

```json
{
    "path": "$.station.id",
    "action": "replaced private identifier with station-example"
}
```

- Remove API keys from URLs and pagination links.
- Replace private station identifiers, names, and coordinates.
- Public test locations and coordinates may remain unchanged.
- Do not change ordinary weather or geocoding values merely to make assertions
  easier.

Capture responses manually outside PHPUnit and CI. Once committed, treat a
fixture as immutable; add a new scenario or capture rather than silently
rewriting its provenance.
