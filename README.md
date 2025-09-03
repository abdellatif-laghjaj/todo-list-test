# Todo List Application

A modern, full-stack todo list application built with Laravel (backend) and Vue.js (frontend), featuring real-time notifications, JWT authentication, and a responsive design with Tailwind CSS.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red?style=flat-square&logo=laravel)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green?style=flat-square&logo=vue.js)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue?style=flat-square&logo=php)
![License](https://img.shields.io/badge/License-MIT-yellow?style=flat-square)

## Table of Contents

-   [Features](#features)
-   [Tech Stack](#tech-stack)
-   [Prerequisites](#prerequisites)
-   [Installation](#installation)
-   [Environment Configuration](#environment-configuration)
-   [Database Setup](#database-setup)
-   [Running the Application](#running-the-application)
-   [API Documentation](#api-documentation)
-   [Project Structure](#project-structure)
-   [Development Workflow](#development-workflow)
-   [Testing](#testing)
-   [Contributing](#contributing)
-   [Troubleshooting](#troubleshooting)
-   [License](#license)

## Features

### 🎯 Core Features

-   **Task Management**: Create, read, update, and delete tasks
-   **Task Status Tracking**: Three status levels (pending, in_progress, completed)
-   **Priority System**: Set task priorities (low, medium, high)
-   **Due Date Management**: Set and track task deadlines
-   **User Authentication**: Secure JWT-based authentication system
-   **User Profiles**: Manage user accounts and profiles

### 🔔 Advanced Features

-   **Real-time Notifications**: Live updates using Laravel Echo and Pusher
-   **Task Statistics**: Dashboard with task completion analytics
-   **Responsive Design**: Mobile-first approach with Tailwind CSS
-   **Vue.js SPA**: Single Page Application with Vue Router
-   **State Management**: Centralized state management with Pinia
-   **Event Broadcasting**: Real-time task updates across sessions

### 🛡️ Security Features

-   JWT token-based authentication
-   API rate limiting
-   CSRF protection
-   Input validation and sanitization
-   Secure password hashing

## Tech Stack

### Backend

-   **Framework**: Laravel 12.x
-   **Language**: PHP 8.2+
-   **Database**: SQLite (configurable to MySQL/PostgreSQL)
-   **Authentication**: JWT (tymon/jwt-auth)
-   **Broadcasting**: Pusher
-   **Queue**: Database driver
-   **Cache**: Database driver

### Frontend

-   **Framework**: Vue.js 3.x
-   **Router**: Vue Router 4.x
-   **State Management**: Pinia
-   **HTTP Client**: Axios
-   **CSS Framework**: Tailwind CSS
-   **Icons**: Heroicons
-   **UI Components**: Headless UI
-   **Build Tool**: Vite

## Prerequisites

Before you begin, ensure you have the following installed on your system:

-   **PHP** >= 8.2
-   **Composer** >= 2.0
-   **Node.js** >= 18.x
-   **npm** or **yarn**
-   **Git**

### Optional

-   **SQLite** (included with PHP)
-   **MySQL** or **PostgreSQL** (if not using SQLite)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/abdellatif-laghjaj/todo-list-test.git
cd todo-list-test
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Setup

```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret
```

## Environment Configuration

Edit the `.env` file with your specific configuration:

### Basic Configuration

```env
APP_NAME="Todo List App"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

### Database Configuration (SQLite - Default)

```env
DB_CONNECTION=sqlite
# The database file will be created at database/database.sqlite
```

### Database Configuration (MySQL - Optional)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Broadcasting Configuration (Optional - for real-time features)

```env
BROADCAST_CONNECTION=pusher

PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_key
PUSHER_APP_SECRET=your_pusher_secret
PUSHER_APP_CLUSTER=your_pusher_cluster

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### Queue Configuration

```env
QUEUE_CONNECTION=database
```

## Database Setup

### 1. Create Database File (SQLite)

```bash
# Create the SQLite database file
touch database/database.sqlite
```

### 2. Run Migrations

```bash
php artisan migrate
```

### 3. Seed Database (Optional)

```bash
php artisan db:seed
```

## Running the Application

### Development Mode

The easiest way to run the application is using the built-in development script:

```bash
composer run dev
```

This command will start:

-   Laravel development server (http://localhost:8000)
-   Queue worker
-   Log monitoring
-   Vite development server for hot reloading

### Manual Setup

If you prefer to run services individually:

#### 1. Start the Laravel Server

```bash
php artisan serve
```

#### 2. Start the Queue Worker (New Terminal)

```bash
php artisan queue:work
```

#### 3. Start Vite Development Server (New Terminal)

```bash
npm run dev
```

#### 4. Monitor Logs (Optional - New Terminal)

```bash
php artisan pail
```

### Production Build

```bash
# Build frontend assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## API Documentation

### Authentication Endpoints

| Method | Endpoint             | Description         |
| ------ | -------------------- | ------------------- |
| POST   | `/api/auth/register` | Register a new user |
| POST   | `/api/auth/login`    | Login user          |
| POST   | `/api/auth/logout`   | Logout user         |
| POST   | `/api/auth/refresh`  | Refresh JWT token   |
| GET    | `/api/auth/profile`  | Get user profile    |

### Task Endpoints

| Method | Endpoint           | Description         |
| ------ | ------------------ | ------------------- |
| GET    | `/api/tasks`       | Get all user tasks  |
| POST   | `/api/tasks`       | Create a new task   |
| GET    | `/api/tasks/{id}`  | Get specific task   |
| PUT    | `/api/tasks/{id}`  | Update task         |
| DELETE | `/api/tasks/{id}`  | Delete task         |
| GET    | `/api/tasks-stats` | Get task statistics |

### Notification Endpoints

| Method | Endpoint                               | Description                    |
| ------ | -------------------------------------- | ------------------------------ |
| GET    | `/api/notifications`                   | Get all notifications          |
| PUT    | `/api/notifications/{id}/mark-as-read` | Mark notification as read      |
| PUT    | `/api/notifications/mark-all-as-read`  | Mark all notifications as read |
| DELETE | `/api/notifications/{id}`              | Delete notification            |
| DELETE | `/api/notifications`                   | Clear all notifications        |

## Project Structure

```
todo-list-test/
├── app/
│   ├── Events/                 # Laravel Events
│   ├── Http/Controllers/       # API Controllers
│   ├── Models/                 # Eloquent Models
│   ├── Repositories/           # Repository Pattern
│   └── Services/               # Business Logic Services
├── database/
│   ├── migrations/             # Database Migrations
│   └── seeders/                # Database Seeders
├── resources/
│   ├── js/
│   │   ├── components/         # Vue Components
│   │   ├── router/             # Vue Router Configuration
│   │   └── stores/             # Pinia Stores
│   └── views/                  # Blade Templates
├── routes/
│   ├── api.php                 # API Routes
│   └── web.php                 # Web Routes
└── tests/                      # PHPUnit Tests
```

## Development Workflow

### Adding New Features

1. **Create a new branch**

    ```bash
    git checkout -b feature/your-feature-name
    ```

2. **Backend Development**

    - Create migration: `php artisan make:migration create_feature_table`
    - Create model: `php artisan make:model FeatureName`
    - Create controller: `php artisan make:controller FeatureController --api`
    - Add routes in `routes/api.php`

3. **Frontend Development**

    - Create Vue components in `resources/js/components/`
    - Add routes in `resources/js/router/index.js`
    - Create Pinia stores in `resources/js/stores/`

4. **Testing**

    ```bash
    # Run PHP tests
    composer test

    # Run with coverage
    php artisan test --coverage
    ```

### Code Style

-   **PHP**: Follow PSR-12 coding standards
-   **JavaScript**: Use ESLint configuration
-   **Vue**: Follow Vue.js style guide

```bash
# Format PHP code
./vendor/bin/pint

# Format JavaScript/Vue code
npm run lint
```

## Testing

### Running Tests

```bash
# Run all tests
composer test

# Run specific test
php artisan test --filter=TaskTest

# Run with coverage
php artisan test --coverage
```

### Writing Tests

-   Place unit tests in `tests/Unit/`
-   Place feature tests in `tests/Feature/`
-   Follow Laravel testing conventions

## Contributing

We welcome contributions! Please follow these steps:

1. **Fork the repository**
2. **Create a feature branch**
    ```bash
    git checkout -b feature/amazing-feature
    ```
3. **Make your changes**
4. **Add tests for new functionality**
5. **Ensure all tests pass**
    ```bash
    composer test
    ```
6. **Commit your changes**
    ```bash
    git commit -m "Add amazing feature"
    ```
7. **Push to your branch**
    ```bash
    git push origin feature/amazing-feature
    ```
8. **Open a Pull Request**

### Contribution Guidelines

-   Write clear, descriptive commit messages
-   Add tests for new features
-   Update documentation as needed
-   Follow existing code style
-   Ensure CI/CD pipeline passes

## Troubleshooting

### Common Issues

#### 1. JWT Secret Not Set

```bash
Error: JWT secret not set
Solution: php artisan jwt:secret
```

#### 2. Database Connection Error

```bash
Error: Database file not found
Solution: touch database/database.sqlite
```

#### 3. Permission Errors

```bash
# Fix storage permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache

# Windows
# Ensure the web server has write access to storage/ and bootstrap/cache/
```

#### 4. Node Modules Issues

```bash
# Clear npm cache
npm cache clean --force

# Remove node_modules and reinstall
rm -rf node_modules package-lock.json
npm install
```

#### 5. Composer Issues

```bash
# Clear composer cache
composer clear-cache

# Remove vendor and reinstall
rm -rf vendor composer.lock
composer install
```

### Getting Help

If you encounter issues:

1. Check the [Laravel documentation](https://laravel.com/docs)
2. Check the [Vue.js documentation](https://vuejs.org/guide/)
3. Search existing [GitHub issues](https://github.com/abdellatif-laghjaj/todo-list-test/issues)
4. Create a new issue with detailed information

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
