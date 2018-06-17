
# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.0.1 - 2018-06-17

### Added

- Added Processor/ folder.
- Added Env processor to read env values.
- Added PhpReader class.

### Deprecated

- Load method last parameter (return object) deprecated.
- Deprecated Redis, Memcached and File cache handlers. We use `Zend\ConfigAggregator` cache interface.

### Removed

- Cache/
- Cache/RedisHandler
- Cache/MemcachedHandler
- Cache/FileHandler

### Fixed

- Nothing.

## 1.0.0 - 2018-03-06

First beta, and first relase as `obullo/config`.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.