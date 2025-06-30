# FUTEMON - Footballer Profile Application

## About This Project

FUTEMON is a modern web application built with Laravel 12 that allows users to browse, search, and view detailed profiles of football players from around the world. The application integrates with the Sportmonks API to import and display comprehensive player data, including personal details, nationalities, positions, and more.

### Key Features

- **Player Directory**: Browse through an extensive database of football players
- **Advanced Search**: Search for players by name, nationality, position, and more
- **Responsive Design**: Fully responsive UI built with Tailwind CSS
- **Real-time Interaction**: Livewire-powered components for a seamless user experience
- **Background Processing**: Efficient data import via queue jobs
- **Performance Optimized**: Utilizes caching, indexing, and other performance enhancements
- **Docker Ready**: Containerized development and production environments
- **CI/CD Pipeline**: Automated testing and deployment with GitHub Actions

## Technology Stack

- **Backend**: Laravel 12.19.3, PHP 8.2+
- **Frontend**: Livewire 3.x, Tailwind CSS 4.0
- **Database**: PostgreSQL
- **Queue**: Database-driven queues
- **Cache**: Redis
- **API**: Sportmonks Football API
- **Containerization**: Docker & Docker Compose
- **CI/CD**: GitHub Actions

## Installation

### Prerequisites

#### Traditional Setup
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- PostgreSQL
- Redis
- Sportmonks API Token

#### Docker Setup (Recommended)
- Docker & Docker Compose
- Sportmonks API Token

### Docker Setup Instructions

1. **Clone the repository**

   ```bash
   git clone https://github.com/ps-carvalho/futemon
   cd futemon
   ```

2. **Create environment file**

   ```bash
   cp .env.example .env
   ```

3. **Configure your environment file**

   Update the following values in your `.env` file:

   ```env
   # Application Configuration
   APP_NAME="Futemon"

   # Database Configuration (Docker setup)
   DB_CONNECTION=pgsql
   DB_HOST=postgres
   DB_PORT=5432
   DB_DATABASE=futemon
   DB_USERNAME=username
   DB_PASSWORD=password

   # Redis Configuration (Docker setup)
   REDIS_HOST=redis
   REDIS_PASSWORD=null
   REDIS_PORT=6379

   # Queue Configuration
   QUEUE_CONNECTION=redis

   # Cache Configuration
   CACHE_DRIVER=redis

   # Sportmonks API Configuration
   SPORTMONKS_API_URL=https://api.sportmonks.com/v3/football
   SPORTMONKS_API_TOKEN=your_api_token_here
   SPORTMONKS_RATE_LIMIT=100
   SPORTMONKS_TIMEOUT=30

   # Cache TTL Configuration
   CACHE_PLAYERS_TTL=3600
   CACHE_SEARCH_TTL=1800
   CACHE_NATIONALITIES_TTL=86400
   ```

4. **Start the Docker environment**

   ```bash
   docker compose up -d
   ```

5. **Install PHP dependencies**

   ```bash
   docker compose exec app composer install
   ```

6. **Generate application key**

   ```bash
   docker compose exec app php artisan key:generate
   ```

7. **Run database migrations**

   ```bash
   docker compose exec app php artisan migrate
   ```

8. **Install Node.js dependencies and build assets**

   ```bash
   docker compose exec app npm install
   docker compose exec app npm run build
   ```

9. **Import initial player data**

   ```bash
   docker compose exec app php artisan players:import
   ```

### Traditional Setup Instructions

1. **Clone the repository**

   ```bash
   git clone https://github.com/ps-carvalho/futemon
   cd futemon
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Install Node.js dependencies**

   ```bash
   npm install
   ```

4. **Create environment file**

   ```bash
   cp .env.example .env
   ```

5. **Configure your environment file**

   Update the following values in your `.env` file:

   ```env
   # Application Configuration
   APP_NAME="Futemon"

   # Database Configuration
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=futemon
   DB_USERNAME=username
   DB_PASSWORD=password

   # Queue Configuration
   QUEUE_CONNECTION=database

   # Sportmonks API Configuration
   SPORTMONKS_API_URL=https://api.sportmonks.com/v3/football
   SPORTMONKS_API_TOKEN=your_api_token_here
   SPORTMONKS_RATE_LIMIT=100
   SPORTMONKS_TIMEOUT=30
   SPORTSMONKS_MAX_RETRIES=3
   SPORTSMONKS_BASE_DELAY_MS=1000
   SPORTSMONKS_BACKOFF_MULTIPLIER=2.0

   # Cache Configuration
   CACHE_PLAYERS_TTL=3600
   CACHE_SEARCH_TTL=1800
   CACHE_NATIONALITIES_TTL=86400
   ```

6. **Generate application key**

   ```bash
   php artisan key:generate
   ```

7. **Run database migrations**

   ```bash
   php artisan migrate
   ```

8. **Build frontend assets**

   ```bash
   npm run build
   ```

9. **Import initial player data**

   ```bash
   php artisan players:import
   ```

## Running the Application

### Docker Development Environment

```bash
# Start all services
docker compose up -d

# Watch for frontend changes
docker compose exec app npm run dev
```

Visit `http://localhost:8000` in your browser to access the application.

### Traditional Development Server

```bash
# Start the Laravel development server
php artisan serve

# In a separate terminal, compile assets with hot reloading
npm run dev
```

Visit `http://localhost:8000` in your browser to access the application.

### Production Server

For production deployments, use the Docker setup with appropriate environment configurations or configure your web server (Nginx, Apache) to point to the `public` directory and ensure proper file permissions.

## Queue Worker

### With Docker

The Docker setup already includes a dedicated queue worker container that automatically processes jobs.

```bash
# View queue worker logs
docker compose logs queue-worker
```

### Traditional Setup

To process background jobs (such as player data imports), you need to run a queue worker:

```bash
php artisan queue:work
```

For production environments, consider using a process manager like Supervisor to keep the queue worker running.

## Testing

### With Docker

```bash
# Run all tests
docker compose exec app php artisan test

# Run with detailed output
docker compose exec app php artisan test --testdox

# Run with coverage report
docker compose exec app php artisan test --coverage
```

### Traditional Setup

```bash
# Run all tests
php artisan test

# Run with detailed output
php artisan test --testdox

# Run with coverage report
php artisan test --coverage
```

## Scheduled Tasks

### With Docker

The Docker setup includes a dedicated scheduler container that automatically runs Laravel's scheduler.

```bash
# View scheduler logs
docker compose logs scheduler
```

### Traditional Setup

Set up a cron job on your server to run Laravel's scheduler:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

This will automatically run the following scheduled tasks:
- Daily player data update at 3:00 AM
- Weekly cleanup of old import logs

## Architecture Overview

The application follows several architectural patterns to ensure maintainability and scalability:

- **Service Layer Pattern**: Business logic is separated from controllers
- **Repository Pattern**: Data access is abstracted through repositories
- **Command Pattern**: Data imports use Artisan commands
- **Observer Pattern**: Model events for data integrity
- **Single Responsibility**: Each class has one clear purpose

### Server Requirements

For Docker deployment:
- Docker Engine 25+
- Docker Compose 2.17+
- At least 2GB RAM
- 20GB available disk space

For traditional deployment:
- PHP 8.2+
- PostgreSQL 13+
- Redis 7+
- Composer
- Node.js 20+
- NPM 9+
- Web server (Nginx or Apache)

## Acknowledgments

- [Laravel](https://laravel.com) - The web framework used
- [Livewire](https://livewire.laravel.com) - For dynamic UI components
- [Tailwind CSS](https://tailwindcss.com) - For styling the application
- [Sportmonks](https://sportmonks.com) - For providing the football data API
