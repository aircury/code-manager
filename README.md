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

## PHPStan shape gates

Custom PHPStan rules for the company's shape gates: file size, duplication and complexity.
Include them from your project's `phpstan.neon`:

```neon
includes:
    - vendor/aircury/code-manager/rules.neon
```

| Gate | Identifier | Reports |
|------|------------|---------|
| File length | `aircury.fileLength` | Files longer than `maxLines` physical lines. Reported on the first line over the limit. |
| Cyclomatic complexity | `aircury.cyclomaticComplexity` | Methods, functions, closures and arrow functions above `maxComplexity`. |
| Duplicate methods | `aircury.duplicateMethod` | Groups of identical method bodies, naming every copy. Reported once per group, at the first copy (by file, then line). |
| Cognitive complexity | `complexity.functionLike`, `complexity.classLike` | From [`tomasvotruba/cognitive-complexity`](https://github.com/TomasVotruba/cognitive-complexity), included by `rules.neon`. |

Cyclomatic complexity is 1 plus one for each `if`, `elseif`, `for`, `foreach`, `while`, `do`, `case` (not `default`),
`catch`, ternary, `??`, `&&`, `||`, `and`, `or` and non-default `match` arm.
Nested closures, arrow functions and anonymous classes are measured on their own and do not count toward the parent.

Duplicate detection compares method bodies after pretty-printing them, stripping comments and renaming variables
(positional names in order of first use; `$this` is kept), so bodies that differ only in naming or comments match.
It only sees the files in the current analysis, so run it on the whole project, not on changed files alone.

### Parameters

```neon
parameters:
    aircury:
        fileLength:
            maxLines: 800         # default
        cyclomaticComplexity:
            maxComplexity: 10     # default
        duplicateMethod:
            minStatements: 5      # default; bodies with fewer top-level statements are ignored
    cognitive_complexity:         # tomasvotruba/cognitive-complexity defaults
        function: 9
        class: 40
```

Each parameter is validated: a non-integer value fails configuration loading.

### Ignoring one finding

Ignore a finding only in a form that cannot widen: one identifier, one file, a stated count.
PHPStan reports an ignore that no longer matches as an error (`reportUnmatchedIgnoredErrors` is on by default; keep it on).

```neon
parameters:
    ignoreErrors:
        -
            identifier: aircury.fileLength
            path: src/Legacy/BigImporter.php
            count: 1
```

Do not use `message` patterns, directory paths, `paths:` lists, inline `@phpstan-ignore` comments or a baseline for these gates.

### Near-duplicate code

`aircury.duplicateMethod` only finds exact duplicates (modulo naming and comments).
Near-duplicates (token-window copy and paste) need a separate, version-pinned tool such as a maintained `phpcpd` fork
or `jscpd`, run in CI alongside PHPStan.

### Developing the rules

```bash
vendor/bin/phpunit
vendor/bin/phpstan analyse   # phpstan.neon: level max, gates enabled, no baseline
```
