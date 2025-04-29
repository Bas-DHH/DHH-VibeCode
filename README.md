# DHH MVP (De Horeca Helper)

A modern web application for tracking and managing hospitality hygiene tasks, built with Laravel, Inertia.js, and React.

## Tech Stack

- **Backend**: Laravel 12.x
- **Frontend**: 
  - React 18.x
  - Inertia.js
  - shadcn/ui components
  - Tailwind CSS
- **Database**: MySQL/PostgreSQL

## Project Purpose

DHH MVP is designed to help hospitality businesses maintain and track essential hygiene tasks. The application provides a structured way to manage daily, weekly, and monthly tasks across different categories such as temperature checks, goods receiving, cooking, verification, and cleaning.

## Setup Instructions

1. **Clone the repository**
   ```bash
   git clone [repository-url]
   cd dhh-mvp
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Update the `.env` file with your database credentials.

5. **Database setup**
   ```bash
   php artisan migrate:fresh --seed
   ```
   This will:
   - Create all necessary tables
   - Seed the database with 10 example tasks

6. **Start development servers**
   ```bash
   # Terminal 1: Laravel server
   php artisan serve
   
   # Terminal 2: Vite dev server
   npm run dev
   ```

## Current Features

### Task Management
- **Model**: `Task` with fields for title, status, category, frequency, due date, and completion tracking
- **Categories**: temperature, goods_receiving, cooking, verification, cleaning
- **Statuses**: pending, done, overdue
- **Frequencies**: daily, weekly, monthly

### Development Tools
- Factory for generating test tasks
- Seeder for populating the database with example data
- Comprehensive migration with field comments

## Development Conventions

Please refer to `.cursorrules` for detailed development guidelines. Key points include:

- Follow Laravel's coding standards
- Use Inertia.js for page components
- Implement shadcn/ui components for consistent UI
- Write clear, documented code
- Follow the established folder structure

## Project Structure

```
dhh-mvp/
├── app/                    # Laravel application code
├── database/              # Migrations, seeders, and factories
├── docs/                  # Project documentation
│   └── features/         # Feature-specific documentation
├── resources/
│   ├── js/               # React components and pages
│   │   ├── Components/   # Reusable components
│   │   └── Pages/        # Inertia page components
│   └── css/              # Stylesheets
├── routes/               # Application routes
└── tests/                # Test files
```

## Contributing

1. Create a new branch for your feature
2. Follow the established coding conventions
3. Write tests for new functionality
4. Submit a pull request with a clear description

## License

[License information to be added]
