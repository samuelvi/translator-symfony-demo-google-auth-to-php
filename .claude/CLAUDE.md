# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Symfony 7 demo application showcasing the Spreadsheet Translator library ecosystem. It demonstrates how to:
- Connect to Google Drive with OAuth 2.0 authentication
- Read secured Google Sheets spreadsheets
- Generate translation files per locale in PHP format

The project serves as a reference implementation for the `samuelvi/spreadsheet-translator-*` library family.

## Common Commands

### Running the Translator
```bash
# Basic usage - translate a specific sheet
bin/console atico:demo:translator --sheet-name=common --book-name=frontend

# With debugging (inside Docker)
XDEBUG_SESSION=PHPSTORM bin/console atico:demo:translator --sheet-name=common --book-name=frontend --env=dev
```

### Docker Development
```bash
# Start containers
make up

# Run console commands in Docker
make console atico:demo:translator --sheet-name=common --book-name=frontend

# Access container shell
make shell

# Stop containers
make down
```

### Code Quality
```bash
# Run Rector for automated refactoring and PHP 8.4 compliance
vendor/bin/rector process

# Dry run to preview changes
vendor/bin/rector process --dry-run
```

### Standard Symfony Commands
```bash
# Install dependencies
composer install

# Clear cache
bin/console cache:clear

# Debug configuration
bin/console debug:config atico_spreadsheet_translator
```

## Architecture

### Core Components

**TranslatorCommand** (`src/App/Command/TranslatorCommand.php`)
- Main console command for the translation process
- Accepts `--sheet-name` and `--book-name` options to specify which sheet and configuration to process
- Delegates actual processing to `SpreadsheetTranslator` service (injected from the bundle)
- After processing, demonstrates translation lookup using Symfony's `TranslatorInterface`

**Kernel** (`src/App/Kernel.php`)
- Uses Symfony's `MicroKernelTrait` for minimal configuration
- Auto-wires services and auto-discovers commands

### Bundle Integration

The application integrates `SpreadsheetTranslatorBundle` which provides:
- `SpreadsheetTranslator` service that orchestrates the entire translation workflow
- Configuration structure defined in `config/packages/atico_spreadsheet_translator.yaml`
- Provider, exporter, and reader services auto-configured based on YAML settings

### Configuration Structure

The `atico_spreadsheet_translator.yaml` configuration supports multiple "books" (e.g., `frontend`, `backend`). Each book defines:

- **provider**: Google Drive OAuth settings
  - `application_name`: Application identifier
  - `name`: Must be `google_drive_auth` for OAuth provider
  - `source_resource`: Google Sheets URL
  - `credentials_path`: Path to OAuth client credentials (`private/credentials.json`)
  - `client_secret_path`: Path to OAuth token storage (`private/token.json`)

- **exporter**: Output format configuration
  - `format`: `php` for PHP array format
  - `prefix`: Prefix for generated files (e.g., `demo_`)
  - `destination_folder`: Where to write translation files

- **shared**: Common settings
  - `default_locale`: Fallback locale
  - `name_separator`: Character used to separate translation keys

### Translation Workflow

1. **Authentication**: First run triggers OAuth flow, saves token to `private/token.json`
2. **Download**: Spreadsheet is downloaded from Google Drive using authenticated API
3. **Read**: XLSX reader parses the spreadsheet structure (columns = locales, rows = keys)
4. **Export**: PHP exporter generates files like `translations/demo_common.en_GB.php`

Each translation file contains a nested array structure:
```php
<?php
return [
    'homepage' => [
        'title' => 'Secured Spreadsheet translator',
        'subtitle' => 'Translator of web pages from secured spreadsheet',
    ],
];
```

### Library Ecosystem

This demo depends on modular libraries that can be mixed:
- `spreadsheet-translator-core`: Core translation logic
- `spreadsheet-translator-symfony-bundle`: Symfony integration
- `spreadsheet-translator-provider-googledriveauth`: Google OAuth provider
- `spreadsheet-translator-reader-xlsx`: Excel file reader
- `spreadsheet-translator-exporter-php`: PHP format exporter

Other providers (OneDrive), readers (CSV), and exporters (YML, XLIFF) exist in separate packages.

## Google OAuth Setup

Authentication requires:
1. Google Cloud project with Sheets API and Drive API enabled
2. OAuth 2.0 Desktop credentials downloaded as `private/credentials.json`
3. First run generates `private/token.json` after user grants permissions

The OAuth redirect URL is `http://localhost` - users must extract the verification code from the browser URL after authentication.

## Rector Configuration

The project uses Rector (`rector.php`) for automated refactoring with:
- PHP 8.4 compliance rules (`LevelSetList::UP_TO_PHP_84`)
- Symfony 7.0 rules and code quality standards
- Doctrine annotations to attributes conversion
- Automatic import optimization

Run `vendor/bin/rector process` to apply automated improvements.

## Project Requirements

- PHP >= 8.4
- Symfony ^7.0
- Composer for dependency management
- Docker (optional but recommended for development)
