# Laravel SEO Analyzer - Technical Documentation


## Table of Contents
1. [Introduction](#introduction)
2. [System Requirements](#system-requirements)
3. [Installation](#installation)
4. [Project Structure](#project-structure)
5. [Configuration](#configuration)
6. [Usage](#usage)
7. [Key Components](#key-components)
8. [Database Schema](#database-schema)
9. [Extending the Analyzer](#extending-the-analyzer)
10. [Troubleshooting](#troubleshooting)


## Introduction

The SEO Analyzer is a Laravel-based tool designed to analyze websites for various SEO factors. It uses Puppeteer via the Spatie/Browsershot package to scrape web pages and perform SEO checks. The analysis results are stored in a database and the process is managed using Laravel's job queue system for efficient, asynchronous processing

## System Requirements

- PHP >= 7.3
- Composer
- Node.js and npm
- Laravel 8.x
- MySQL or another Laravel-supported database

## Installation

1. Clone the repository:
```shell
 git clone https://github.com/oshkoshbagoshh/laravel-seo-analzer.git
 
 # move to the directory 
 cd laravel-seo-analyzer
  ```
2. Install PHP dependencies 
```shell
composer install 
```

3. Install Node.js dependencies
```shell
npm install 
```

4. Copy the `env.example` file to `.env` and configure your database settings
```shell
cp .env.example .env
```
5. Generate an application key:
```shell
php artisan key:generate
```
6. Run database migrations:
```shell
php artisan migrate
```

---
## Project Structure 
The key files and directories for this project are:

- `app/Jobs/AnalyzeUrl.php`: Contains the main logic for analyzing a single URL.
- `app/Console/Commands/AnalyzeSeo.php`: Artisan command to start the SEO analysis.
- `app/Models/SeoResult.php`: Eloquent model for SEO results.
- `database/migrations/xxxx_xx_xx_create_seo_results_table.php`: Database migration for SEO results.

## Configuration

1. Set up your queue driver in `.env`: `QUEUE_CONNECTION=database`
2. You may need to configure Puppeteer's path in `config/browsershot.php` if it's not automatically detected:
```php
'node_binary_path' => env('NODE_BINARY_PATH', '/usr/local/bin/node'),
'npm_binary_path' => env('NPM_BINARY_PATH', '/usr/local/bin/npm'),
```
Usage
To start an SEO analysis, use the Artisan command:
```
php artisan seo:analyze https://example.com https://another-example.com
```

Process the analysis jobs:
```
php artisan queue:work
```
---


## Key Components

### AnalyzeUrl Job
- This job (app/Jobs/AnalyzeUrl.php) is responsible for analyzing a single URL. It performs the following tasks:

- Loads the webpage using Browsershot.
- Runs various SEO checks (ARIA landmarks, alt tags, etc.).
- Calculates a total SEO score.
- Saves or updates the results in the database.
- Extracts and dispatches jobs for subpages.

### AnalyzeSeo Command
- The seo:analyze Artisan command (app/Console/Commands/AnalyzeSeo.php) is the entry point for starting an analysis. It accepts multiple URLs as arguments and dispatches an AnalyzeUrl job for each.

### SeoResult Model
- The SeoResult model (app/Models/SeoResult.php) represents the SEO analysis results for a single URL. It corresponds to the seo_results table in the database.

### Database Schema
The seo_results table has the following structure:

| Column Name | Type | Description |
|-------------|------|-------------|
| id | bigint(20) unsigned | Auto-incrementing primary key |
| url | varchar(255) | The analyzed URL (unique) |
| aria_landmarks | float | Score for ARIA landmarks |
| img_alt_tags | float | Score for image alt tags |
| color_contrast | float | Score for color contrast |
| semantic_html | float | Score for semantic HTML usage |
| broken_links | float | Score for broken links check |
| meta_tags | float | Score for meta tags |
| lazy_loading_images | float | Score for lazy-loaded images |
| favicon | float | Score for favicon presence |
| mobile_friendly | float | Score for mobile-friendliness |
| input_types | float | Score for correct input types |
| total_score | float | Overall SEO score |
| created_at | timestamp | Record creation timestamp |
| updated_at | timestamp | Record last update timestamp |


## Extending the Analyzer
- To add new SEO checks:

- Add a new method in the AnalyzeUrl job class.
- Update the handle method to include the new check.
- Modify the calculateTotalScore method to include the new factor in the overall score.
- Update the database migration and SeoResult model to include the new field.

## Troubleshooting
- If Puppeteer fails to run, ensure Node.js and npm are correctly installed and accessible.
- For database connection issues, check your .env file for correct database credentials.
- If jobs are not processing, ensure the queue worker is running (php artisan queue:work).
