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

### Business Registration
- **Model**: `Business` with fields for business_name, created_by, trial_starts_at, and trial_ends_at
- **Registration Flow**:
  - Create a new business with unique business name
  - Create an admin user for the business
  - Set up 14-day trial period
  - Automatic login after registration
- **Validation Rules**:
  - Business name must be unique
  - Email must be unique
  - Password must be confirmed
  - All fields are required
- **Security**:
  - Password hashing
  - Database transactions
  - Proper error handling

### Task Management
- **Model**: `Task` with fields for title, status, category, frequency, due date, and completion tracking
- **Categories**: temperature, goods_receiving, cooking, verification, cleaning
- **Statuses**: pending, done, overdue
- **Frequencies**: daily, weekly, monthly

### Task Verification Features

The application includes comprehensive task verification features for different types of hygiene tasks:

#### Cleaning Tasks
- Form component with cleaning and disinfection status tracking
- Notes and corrective action fields
- Validation for required fields
- Multi-language support (Dutch/English)

#### Critical Cooking Tasks
- Temperature monitoring with minimum threshold
- Cooking time tracking
- Visual inspection checks
- Corrective action requirements for failed checks
- Multi-language support

#### Temperature Control Tasks
- Temperature range validation (min/max thresholds)
- Location tracking
- Notes and corrective action fields
- Multi-language support

#### Goods Receiving Tasks
- Supplier and product details
- Batch number and expiry date tracking
- Temperature checks (when required)
- Packaging integrity verification
- Visual inspection options
- Corrective action handling
- Multi-language support

Common features across all task types:
- Task completion status tracking (completed/warning)
- Corrective action requirements when checks fail
- Business-specific access control
- Audit trail logging
- Event dispatching on completion
- Database transaction handling
- Form validation
- Multi-language support (Dutch/English)

### Development Tools
- Factory for generating test tasks
- Seeder for populating the database with example data
- Comprehensive migration with field comments

## UI Components

The application uses shadcn/ui components for a consistent and modern look. Key components include:

### Card Component
Used for displaying grouped tasks by category. Features:
- Responsive design
- Customizable header and content
- Support for badges and icons
- Consistent spacing and typography

Example usage:
```jsx
<Card>
  <CardHeader>
    <CardTitle>Category Title</CardTitle>
  </CardHeader>
  <CardContent>
    {/* Content */}
  </CardContent>
</Card>
```

### Badge Component
Used for displaying task counts and status indicators. Features:
- Multiple variants (default, secondary, destructive, outline)
- Responsive design
- Consistent styling with the theme

Example usage:
```jsx
<Badge variant="secondary">5 tasks</Badge>
```

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
│   ├── Http/              # Controllers and middleware
│   │   ├── Controllers/   # Application controllers
│   │   │   └── Auth/      # Authentication controllers
│   │   └── Requests/      # Form request validation
│   ├── Models/            # Eloquent models
│   └── Exceptions/        # Exception handlers
├── database/              # Migrations, seeders, and factories
├── docs/                  # Project documentation
│   └── features/         # Feature-specific documentation
├── resources/
│   ├── js/               # React components and pages
│   │   ├── Components/   # Reusable components
│   │   │   └── ui/      # shadcn/ui components
│   │   └── Pages/        # Inertia page components
│   ├── lib/              # Utility functions
│   └── css/              # Stylesheets
├── routes/               # Application routes
└── tests/                # Test files
```

## Layout Conventions

The application uses `AuthenticatedLayout` as the default layout for all user-facing pages. This layout provides:

- Authentication handling
- Navigation menu
- User profile dropdown
- Responsive design
- Proper routing integration

Example usage in a page component:
```jsx
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

const MyPage = () => {
    return (
        <AuthenticatedLayout>
            <Head title="Page Title" />
            {/* Page content */}
        </AuthenticatedLayout>
    );
};

export default MyPage;
```

Use this layout for all pages that require authentication and navigation. Only use alternative layouts when explicitly specified.

## Contributing

1. Create a new branch for your feature
2. Follow the established coding conventions
3. Write tests for new functionality
4. Submit a pull request with a clear description

## License

[License information to be added]
