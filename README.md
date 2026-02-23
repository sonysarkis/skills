# Skills Package

Laravel package to fetch, cache, and display quotes from DummyJSON.

## Installation Guide
### Requirements

- PHP 8.2+
- Composer
- Node.js + npm (for frontend assets)


### Install as a local package in a Laravel app
1. Add this package as a path repository in your Laravel app `composer.json`:
```json
"repositories": [
	{
		"type": "path",
		"url": "../skills"
	}
]
```

2. Require the package:
```bash
composer require sonysarkis/skills *@dev
```

3. Publish package resources:
```bash
php artisan vendor:publish --tag=quotes-config
php artisan vendor:publish --tag=quotes-assets
```
4. Open the UI:
   http://localhost:8000/quotes-ui

## Rate Limiting Strategy

The package enforces rate limits within the QuoteService using a cache-based counter strategy. Relying on configurable values (request_limit and time_window) defined in config/quotes.php, the service tracks API hits via the quotes_api_hits cache key, setting the TTL on the first request and incrementing it on subsequent calls. If the hit count reaches or exceeds the allowed limit, the service immediately throws a custom RateLimitExceededException without relying on sleep or wait functions. Instead of blocking the service, the responsibility of pausing and retrying the request is gracefully offloaded to the quotes:batch-import console command, which handles the recovery after catching the exception

## Docker Environment

### Run the full environment

From the package root:

```bash
docker compose up
```

This process automatically:

1. Creates a fresh Laravel app inside the container.
2. Adds this package as a local path repository.
3. Installs the package.
4. Publishes config and frontend assets.
5. Starts Laravel on port `8080`.

Open:

```text
http://localhost:8080/quotes-ui
```

### Stop and clean containers

```bash
docker-compose down -v
```

OR

```bash
docker compose down --volumes --remove-orphans
```

## Testing

From the package root, run:

```bash
vendor/bin/pest
```



## Development process

To tackle this project, I opted for an incremental approach, ensuring each functional block was secure before moving on to the next. I started by laying the foundations of the package architecture (Service Providers, Facades, and configuration) and then established communication with DummyJSON using Saloon. With the connection ready, I incorporated the cache-based rate limiting system and its custom exception, which was key to being able to schedule the quotes:batch-import command with the ability to catch errors and automatically retry requests. Once the data ingestion was robust, I implemented caching using a sorted array along with the binary search algorithm for ID-based queries. Finally, I developed the interface in Vue.js compiled with Vite, packaged the entire solution in a self-configuring Docker environment, and reinforced the business logic with unit and integration tests in Pest
