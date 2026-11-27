# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 27 November 2026
### Added
- CLI command `bin/magento db:table:list` to list all tables of the current database
- CLI command `bin/magento db:table:remove` to drop a database table
- `db:table:remove` only accepts tables of the current database and refuses cross-database names like `otherdb.foobar`
- `db:table:remove` asks for confirmation and refuses tables declared in the `db_schema.xml` of an enabled module, unless `--force` (`-f`) is given
- `db:table:remove` returns a failure for a non-existing table, and every removal is logged to the Magento system log
- Support for PHP 8.5

### Changed
- Require `magento/framework` `^103.0` instead of any version

### Removed
- Support for PHP 8.1
