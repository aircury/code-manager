# CodeManager

A powerful PHP tool to improve and manage your code quality through various commands and checks.

## Features

- PSR-4 namespace checking and validation
- Autoloader optimization
- Code quality tools integration
- Code formatting
- Static analysis

## Setup

First, add the repository to your composer.json:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/aircury/code-manager"
        }
    ]
}
```

Then install the package as dev dependency:

```bash
composer require aircury/code-manager --dev
```

## Commands

### code:format

Checks and fixes code style issues.

```bash
bin/code-manager code:format [options] [--] [<files>...]
```

This command:
- Validates code style compliance
- Automatically fixes code style issues
- Uses PHP CS Fixer rules
- Returns success (0) or failure (1) status code

Options:
- `--branch` or `-b` - Run the formatter in your branch new files relative to the specified branch
- `--read-only` or `-o` - Run the formatter in read only mode, showing the diff if the files had been formatted and the applied rules

Arguments:
- `files` - Specify files that you want to format (array)

### code:analyser

Performs static analysis on your code.

```bash
bin/code-manager code:analyser [options] [--] [<files>...]
```

This command:
- Runs PHPStan analysis
- Checks for potential bugs
- Validates type safety
- Returns success (0) or failure (1) status code

Options:
- `--branch` or `-b` - Run the formatter in your branch new files relative to the specified branch
- `--level` or `-l` - Specify level to run the analyser

Arguments:
- `files` - Specify files that you want to format (array)

### namespace:check

Checks if classes follow PSR-4 autoloading and namespace rules.

```bash
bin/code-manager namespace:check
```

This command:
- Validates PSR-4 compliance
- Optimizes the autoloader
- Checks namespace rules
- Returns success (0) or failure (1) status code

### yaml:format

Formats YAML files according to specified rules.

```bash
bin/code-manager yaml:format [options]
```

This command:
- Automatically formats YAML files
- Maintains consistent indentation
- Optimizes YAML structure

Options:
- `--indent` or `-i` - Number of spaces for indentation (default: 2)
- `--inline` or `-l` - Level where to switch to inline YAML (default: 2)

The command will automatically detect and format all YAML files (`.yaml` or `.yml`) that have been changed in your current branch.
