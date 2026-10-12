# Changelog

## 2.0.0 — Unreleased

### Breaking changes

- Require PHP `^8.2` and Laravel 12 or 13; Laravel 13 requires PHP 8.3+.
- Drop Laravel 8–11 and PHP 8.0–8.1 support.
- Remove unused `illuminate/database` and `ext-json` requirements.
- Add explicit return types to provider, command and facade methods; subclasses overriding these methods must use compatible signatures.

### Development

- Upgrade PHPUnit to 11/12 and Testbench to 10/11.
- Test Laravel 12 on PHP 8.2–8.5 and Laravel 13 on PHP 8.3–8.5 in GitHub Actions.
- Stop tracking the library's Composer lock file.

### Inherited fixes

- Include all 1.x facade, view, configuration and installation fixes without changing their behavior.

## 1.x — Unreleased

### Fixed

- Implement the missing `Theme` facade target and share view lookup with the helper.
- Preserve selected/default/application view fallback, including absent or empty theme selections, explicit namespaces and stringable view names.
- Load default and selected configuration together, filling missing associative settings without replacing existing values, lists or explicitly empty arrays.
- Keep merged settings compatible with Laravel's configuration cache.
- Preserve existing files during `theme:install`; add `--force` for deliberate replacement and reject symbolic link file destinations.
- Correct the Composer package type to `library` and declare directly used Illuminate dependencies.
- Correct the documented default view path and clarify the installation requirement for the starter view.

### Compatibility

- Keep the existing PHP `^8` and Laravel 8/9/10 requirements.
- Retain the existing published configuration, nested theme names, empty-selection lookup paths and provider extension hook.
- Keep existing runtime dependencies; removal of unused requirements is deferred to 2.x.
- Add PHPUnit 9/Testbench regression coverage and CI for the older framework versions.
