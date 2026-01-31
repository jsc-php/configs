# Configs

A simple and flexible PHP library for parsing and writing configuration files. Supports JSON, YAML, and INI formats with
ease.

## Features

- **Multi-format Support**: Seamlessly handle `.json`, `.yaml` / `.yml`, and `.ini` files.
- **Magic Access**: Access and modify configuration data using PHP magic properties.
- **Auto-save**: Automatically persists changes back to the file when the object is destroyed (can be disabled).
- **Format Conversion**: Easily convert configuration files between supported formats.
- **Type Safety**: Built for PHP 8.5+ with modern syntax.

## Installation

Install via Composer:

```bash
composer require jsc-php/configs
```

*Note: Requires the `ext-yaml` PHP extension for YAML support.*

## Usage

### Basic Example

```php
use JscPhp\Configs\Config;

// Load a config file (format is detected by extension)
$config = new Config('config.json');

// Get values
$dbHost = $config->database['host'];

// Set values
$config->debug = true;

// Changes are automatically saved when $config goes out of scope
```

### Options

You can disable autosave in the constructor:

```php
$config = new Config('config.yaml', ['autosave' => false]);

$config->theme = 'dark';

// Manual save required if autosave is false
$config->save();
```

### Converting Formats

Convert an existing configuration to a different format:

```php
use JscPhp\Configs\Config;
use JscPhp\Configs\Types\Type;

$config = new Config('settings.ini');

// Save as JSON
$config->saveAs(Type::Json); // Creates settings.json (or overwrites if same path)
```

### Deleting Keys

```php
$config->delete('temporary_key');
```

## Supported Formats

- **JSON**: Uses native `json_encode` and `json_decode`.
- **YAML**: Uses `yaml_emit` and `yaml_parse` (requires `ext-yaml`).
- **INI**: Uses `parse_ini_file` with typed scanning and a custom writer supporting sections and multi-value keys.

## License

This project is licensed under the GPL-3.0 License.

## Authors

- **James Cavaliere** - [james.cavaliere@gmail.com](mailto:james.cavaliere@gmail.com)
