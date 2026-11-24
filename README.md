# Yireo_CleanDatabaseTables

<!-- badges.specs.start -->
![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://img.shields.io/packagist/v/yireo/module-clean-database-tables)
<!-- badges.specs.end -->


> This Magento 2 module allows you to remove database tables via a simple CLI command

### Installation
Install this package via composer:
```bash
composer require yireo/module-clean-database-tables
```

Next, enable this module:
```bash
bin/magento module:enable Yireo_CleanDatabaseTables
```

### Usage
List all tables of the current database:
```bash
bin/magento db:table:list
```

Remove a table:
```bash
bin/magento db:table:remove foobar
```

The table name needs to match a table of the current database exactly (including the table prefix). Names of tables in
other databases (like `otherdb.foobar`) are refused. Before removing the table, you are asked for confirmation. Tables
that are declared in the `db_schema.xml` file of an enabled module are refused as well. Use the `--force` (`-f`) option
to skip the confirmation and to allow removing declared tables (also required when running with `--no-interaction`):
```bash
bin/magento db:table:remove foobar --force
```

Every removal is logged to the Magento system log.

## Current status

<!-- badges.test.start -->
![Static Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_CleanDatabaseTables/static-tests.yml?label=static-tests)
![Unit Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_CleanDatabaseTables/unit-tests.yml?label=unit-tests)
![Integration Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_CleanDatabaseTables/integration-tests.yml?label=integration-tests)
![Playwright](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_CleanDatabaseTables/playwright.yml?label=playwright)
![DI Compilation](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_CleanDatabaseTables/compile.yml?label=compile)
<!-- badges.test.end -->

