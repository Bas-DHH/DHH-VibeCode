# De Horeca Helper (DHH)

De Horeca Helper (DHH) is a mobile-first hygiene and task management platform for hospitality businesses.  
Built with **Laravel**, **Inertia.js**, and **React**, it helps teams manage HACCP processes through digital task flows, alerts, and reporting.

---

## 🚀 Tech Stack

- **Laravel 10+** (PHP 8.3)
- **Inertia.js**
- **React + TypeScript**
- **Tailwind CSS** with [shadcn/ui](https://ui.shadcn.com/)
- **MySQL** (MariaDB compatible)
- **Laravel Cashier** with Mollie integration

---

## 📦 Features

- Digital task flows by category (e.g. Cleaning, Temperature)
- Daily & recurring task scheduling
- Overdue task alerts
- User roles: Admin & Staff
- Manual task creation
- Mobile-first responsive design
- Localization support (Dutch, English)
- Subscription billing with Mollie

---

## ⚙️ Getting Started

### 1. Clone the repo
```bash
git clone git@github.com:yourname/dhh.git
cd dhh
```

### 2. Install dependencies
```bash
composer install
npm install && npm run dev
```

### 3. Setup environment
```bash
cp .env.example .env
php artisan key:generate
```

Configure `.env` with your:
- DB credentials
- Mollie API keys
- Mail provider

### 4. Run migrations & seed
```bash
php artisan migrate --seed
```

---

## 🧪 Testing
```bash
php artisan test
```

---

## 🎨 Design System

- Uses Tailwind CSS
- Primary color: `#5095cc`
- All components via [shadcn/ui](https://ui.shadcn.com/docs)

---

## 📁 Folder Structure

- `resources/js/Pages/` → Inertia React pages
- `resources/js/Components/` → React components
- `app/Http/Controllers/` → Laravel controllers
- `app/Models/` → Eloquent models
- `routes/web.php` → All routes (Inertia pages)
- `.cursorrules` → AI instructions for Cursor AI

---

## 📄 License

MIT (or your preferred license)

---

## ✨ Credits

Built by [Your Name] using Laravel + Inertia + React.