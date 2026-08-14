# Response Fixtures

Automated tests use committed response fixtures derived from representative
real OpenWeather responses. These are usually JSON, but binary APIs retain
their original response format. Tests must never make live OpenWeather
requests.

## Naming

Store fixtures by product, endpoint, and scenario:

```text
tests/Fixtures/<product>/<endpoint>/<scenario>.json
tests/Fixtures/<product>/<endpoint>/<scenario>.meta.json
```

Name top-level product folders after the corresponding public resource, using
kebab case where required, such as `maps`, `stations`, `air-pollution`, and
`one-call`. When a resource and endpoint are the same concept, operation names
may be used directly as scenarios instead of adding a redundant folder.

For example:

```text
tests/Fixtures/geocoding/direct/success.json
tests/Fixtures/geocoding/direct/success.meta.json
```

Use stable endpoint and scenario names such as `success`, `empty`,
`missing-optional-fields`, or `invalid-request`. Do not include a captured
location name in a filename because the returned name may change or be absent.
Use the actual body format as the fixture extension, such as `.png` for a map
tile, even when the response advertises an incorrect content type. Use
`.empty` for a successful response with a zero-byte body.

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
    "httpStatus": 200,
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

For binary fixtures, also record the response content type, body format, byte
length, and SHA-256 hash. Record stable format metadata such as image dimensions
when it is useful for validation.

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

- Never record API keys or authentication headers in request metadata. Because
  they are excluded at the capture boundary, do not list them as sanitization.
- Remove API keys from response URLs and pagination links, and record those
  response changes in `sanitization`.
- Replace identifiers, names, and coordinates belonging to persistent or
  user-owned stations.
- Generated identifiers and deliberately public metadata for a temporary
  fixture station may remain unchanged after its deletion is verified.
- Public test locations and coordinates may remain unchanged.
- Do not change ordinary weather or geocoding values merely to make assertions
  easier.

Capture responses manually outside PHPUnit and CI. Once committed, treat a
fixture as immutable; add a new scenario or capture rather than silently
rewriting its provenance.
