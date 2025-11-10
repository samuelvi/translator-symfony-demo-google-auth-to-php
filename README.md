Spreadsheet Translator Symfony Demo Application - Use Case
======================================================================================

Introduction
------------

Lightweight Symfony Demo Application for the Spreadsheet Translator functionality.
This demo provides a command that connects to Google Drive with authentication, reads a spreadsheet file, and generates translation files per locale in PHP format.


Requirements
------------

* PHP >= 8.4
* Symfony ^7.0
* Composer


Installation
------------

```bash
composer create-project atico/translator-symfony-demo-google-auth-to-php
```

This will install the demo application on your computer.


Google OAuth Setup
------------------

This application uses Google OAuth 2.0 to access secured Google Sheets. Follow these steps to configure authentication:

### 1. Create Google Cloud Project and Enable APIs

1. Go to [Google Cloud Console](https://console.cloud.google.com)
2. Create a new project or select an existing one
3. Navigate to **"APIs & Services" > "Library"**
4. Search and enable **"Google Sheets API"**
5. Search and enable **"Google Drive API"**

### 2. Create OAuth 2.0 Credentials

1. Go to **"APIs & Services" > "Credentials"**
2. Click **"Create Credentials" > "OAuth client ID"**
3. If prompted, configure the OAuth consent screen:
   - Add your email and application name
   - User Type: External (or Internal if using Google Workspace)
4. Select application type: **"Desktop application"**
5. Give it a name (e.g., "Spreadsheet Translator")
6. Click **"Create"**
7. Download the JSON file
8. Rename it to `credentials.json`
9. Place it in the `private/` directory

### 3. Add Test Users (Optional but Recommended)

To avoid verification warnings during development:

1. Go to **"APIs & Services" > "OAuth consent screen"**
2. Scroll to **"Test users"** section
3. Click **"+ ADD USERS"**
4. Add your Google email address
5. Save changes

### 4. First-Time Authentication

Run the translator command:

```bash
bin/console atico:demo:translator --sheet-name=common --book-name=frontend
```

The system will prompt you to authenticate:

1. **Copy the URL** displayed in the terminal
2. **Open it in your browser**
3. **Sign in** with your Google account
4. You may see a warning: *"Google hasn't verified this app"*
   - Click **"Advanced"** or **"Advanced settings"**
   - Click **"Go to Spreadsheet Translator (unsafe)"**
   - This is safe - it's your own application
5. **Accept the permissions** requested
6. Google will redirect to `localhost` and show a connection error - **this is expected**
7. **Look at the browser URL bar** - it contains the verification code:
   ```
   http://localhost/?code=4/0AeanS0Z...LONG_CODE...&scope=https://...
   ```
8. **Extract the verification code from the URL:**
   - The code is the value that comes **after** `code=` and **before** `&`
   - For example, if the URL is:
     ```
     http://localhost/?code=4/0AeanS0Zabcdefghijklmnopqrstuvwxyz1234567890&scope=https://...
     ```
   - The code to copy is:
     ```
     4/0AeanS0Zabcdefghijklmnopqrstuvwxyz1234567890
     ```
9. **Paste the code into the terminal** where it says "Enter verification code:"

The system will save your credentials in `private/token.json` and you won't need to authenticate again unless you delete this file.

### 5. Configuration

Edit `config/packages/atico_spreadsheet_translator.yaml` to configure your spreadsheets:

```yaml
atico_spreadsheet_translator:
  frontend:
    provider:
      application_name: 'Your App Name'
      name: 'google_drive_auth'
      source_resource: 'YOUR_GOOGLE_SHEETS_URL'
      credentials_path: '%kernel.project_dir%/private/credentials.json'
      client_secret_path: '%kernel.project_dir%/private/token.json'
```

Replace `YOUR_GOOGLE_SHEETS_URL` with your Google Sheets URL.

**Note:** Make sure the Google account you authenticate with has access to the spreadsheet you want to translate.


Running the demo
----------------

Execute the following command in your terminal:

```bash
bin/console atico:demo:translator --sheet-name=common --book-name=frontend
```

This command will generate translation files that will be stored in the `translations/` folder.

### Generated files structure:

```
translations/
│  demo_common.en_GB.php
│  demo_common.es_ES.php
│  demo_common.fr_FR.php
```

### Example output

`demo_common.en_GB.php` will contain:

```php
<?php
return [
    'homepage' => [
        'title' => 'Secured Spreadsheet translator',
        'subtitle' => 'Translator of web pages from secured spreadsheet',
    ],
];
```


Dependencies
------------

This project uses the Spreadsheet Translator library ecosystem. Check `composer.json` for the complete list of dependencies.

Main components:
- **spreadsheet-translator-core**: Core functionality
- **spreadsheet-translator-symfony-bundle**: Symfony integration
- **spreadsheet-translator-provider-googledriveauth**: Google Drive authentication
- **spreadsheet-translator-reader-xlsx**: Excel file reader
- **spreadsheet-translator-exporter-php**: PHP format exporter


Testing
-------

The project includes comprehensive unit tests using PHPUnit 11:

```bash
# Run all tests
bin/phpunit
# or
make test

# Run tests with coverage report (generates coverage/index.html)
bin/phpunit --coverage-html coverage
# or
make test-coverage

# Run specific test file
bin/phpunit tests/Command/TranslatorCommandTest.php
```

### Test Structure

- `tests/Command/TranslatorCommandTest.php` - Unit tests for the translator command
- `tests/KernelTest.php` - Integration tests for the Symfony kernel
- `tests/bootstrap.php` - PHPUnit bootstrap file
- `phpunit.xml.dist` - PHPUnit configuration

### Code Quality

The project uses Rector for automated code refactoring and modernization to PHP 8.4 and Symfony 7:

```bash
# Apply Rector changes
bin/rector process
# or
make rector

# Preview Rector changes without applying them
bin/rector process --dry-run
# or
make rector-dry
```

Development
-----------

### Makefile Commands

The project includes a comprehensive Makefile for convenient development. Run `make help` to see all available commands:

**Local Development (without Docker):**
```bash
# Install dependencies
make install

# Run tests
make test

# Run tests with coverage report
make test-coverage

# Run Rector to modernize code
make rector

# Preview Rector changes without applying them
make rector-dry

# Run all code quality checks
make lint

# Run the translator command
make translate

# Clear Symfony cache
make cache-clear
```

**Docker Development:**
```bash
# Show all available commands
make help

# Start the application
make up

# Run tests inside Docker
make docker-test

# Run the translator command inside Docker
make docker-translate

# Run Rector inside Docker
make docker-rector

# Access the container shell
make shell

# Stop the application
make down
```


Related Projects
----------------

### Symfony Bundle:
- [Spreadsheet Translator Symfony Bundle](https://github.com/samuelvi/spreadsheet-translator-symfony-bundle)

### Symfony Demos:
- [Symfony Demo - Local File to PHP](https://github.com/samuelvi/translator-symfony-demo-local-file-to-php) - Takes a local file and creates translation files per locale in PHP format
- [Symfony Demo - Google to YML](https://github.com/samuelvi/translator-symfony-demo-google-drive-provider-yml-exporter) - Takes a non secured Google Drive spreadsheet and creates translation files per locale in YML format
- [Symfony Demo - OneDrive to XLIFF](https://github.com/samuelvi/translator-symfony-demo-onedrive-to-xliff) - Takes a Microsoft OneDrive spreadsheet and creates translation files per locale in XLIFF format


Contributing
------------

We welcome contributions to this project, including pull requests and issues (and discussions on existing issues).

If you'd like to contribute code but aren't sure what, the issues list is a good place to start. If you're a first-time code contributor, you may find Github's guide to [forking projects](https://docs.github.com/get-started/quickstart/fork-a-repo) helpful.

All contributors (whether contributing code, involved in issue discussions, or involved in any other way) must abide by our code of conduct.


License
-------

Spreadsheet Translator Symfony Bundle is licensed under the MIT License. See the LICENSE file for full details.

