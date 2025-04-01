# DocuDoodle - AI Documentation Generator

DocuDoodle is an AI-powered documentation generator for Laravel applications. It analyzes your codebase and creates comprehensive documentation using AI.

## Installation

DocuDoodle has been installed in this project using Composer:

```bash
composer require genericmilk/docudoodle
```

The configuration file has been published to `config/docudoodle.php`.

## Configuration

DocuDoodle requires an OpenAI API key to function. The following environment variables have been added to the `.env` file:

```
OPENAI_API_KEY=  # Add your OpenAI API key here
DOCUDOODLE_API_PROVIDER=openai
DOCUDOODLE_MODEL=gpt-4o-mini
```

You must add your OpenAI API key to the `.env` file before using DocuDoodle.

### Alternative AI Providers

DocuDoodle supports multiple AI providers:

1. **OpenAI (default)**: Set `DOCUDOODLE_API_PROVIDER=openai` and provide `OPENAI_API_KEY`
2. **Claude**: Set `DOCUDOODLE_API_PROVIDER=claude` and provide `CLAUDE_API_KEY`
3. **Google Gemini**: Set `DOCUDOODLE_API_PROVIDER=gemini` and provide `GEMINI_API_KEY`
4. **Ollama** (local): Set `DOCUDOODLE_API_PROVIDER=ollama` and configure `OLLAMA_HOST` and `OLLAMA_PORT`

## Usage

### Generating Documentation

To generate documentation for your project, run:

```bash
php artisan docudoodle:generate
```

This command analyzes your codebase and generates Markdown documentation files. By default, DocuDoodle:

- Processes PHP, YAML, and YML files
- Skips vendor/, node_modules/, tests/, and cache/ directories
- Outputs documentation to the directory specified in the configuration

### Command Options

The generate command supports several options:

```bash
php artisan docudoodle:generate [--source=app] [--output=docs] [--extensions=php,yaml] [--skip=vendor,node_modules]
```

- `--source`: Specify the source directory to analyze (default: app)
- `--output`: Specify the output directory for documentation (default: docs)
- `--extensions`: Comma-separated list of file extensions to process
- `--skip`: Comma-separated list of directories to skip

## Benefits

- **Time-saving**: Automatically generates documentation without manual effort
- **Comprehensive**: Creates detailed documentation with insights about code structure and functionality
- **Incremental updates**: Skips existing documentation files, allowing for quick top-up runs after adding new features
- **Markdown support**: Outputs documentation in Markdown format, which displays well in GitHub and other source control providers

## Example Workflow

A typical workflow for using DocuDoodle in this project:

1. Add your OpenAI API key to the `.env` file
2. Run `php artisan docudoodle:generate` to generate initial documentation
3. After adding new features or making significant changes, run the command again to update documentation
4. Commit the generated documentation files to the repository

## Best Practices

- Run DocuDoodle after completing a feature or making significant changes
- Review the generated documentation for accuracy and completeness
- Commit the generated documentation files along with your code changes
- Consider using DocuDoodle as part of your development workflow to keep documentation up-to-date

## Limitations

- DocuDoodle relies on AI to generate documentation, so the quality may vary
- Complex code structures might not be fully captured
- Generated documentation should be reviewed for accuracy before sharing

## Support

For more information about DocuDoodle, visit:
- [GitHub Repository](https://github.com/genericmilk/docudoodle)
- [Examples of Generated Documentation](https://github.com/genericmilk/docudoodle/tree/main/examples) 