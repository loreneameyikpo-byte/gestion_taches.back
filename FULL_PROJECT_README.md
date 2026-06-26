# 🚀 Gestion Tâches - Full-Stack Project

**Unified repository combining Laravel 13 backend + Nuxt 4 frontend**

## 📁 Project Structure

```
gestion_taches.back/
├── backend/ (Laravel 13)
│   ├── app/                     ← Controllers, Models, Services
│   ├── config/                  ← Configuration files
│   ├── database/                ← Migrations & Database
│   ├── routes/                  ← API Routes
│   ├── vendor/                  ← PHP Dependencies
│   ├── artisan                  ← Laravel CLI
│   ├── composer.json            ← PHP Dependencies Config
│   └── .env.example             ← Backend Configuration Template
│
└── frontend/ (Nuxt 4)
    ├── app/                     ← Pages, Layouts, Components
    ├── node_modules/            ← JavaScript Dependencies
    ├── nuxt.config.ts           ← Nuxt Configuration
    ├── package.json             ← Frontend Dependencies Config
    ├── tailwind.config.ts       ← Tailwind CSS Config
    └── tsconfig.json            ← TypeScript Config
```

## 🔧 Technology Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend API | Laravel | 13 |
| Backend Auth | Laravel Sanctum | - |
| Backend DB | SQLite | - |
| Frontend Framework | Nuxt | 4 |
| Frontend Styling | Tailwind CSS | Latest |
| State Management | Pinia | Latest |
| HTTP Client | $fetch (built-in) | - |

## ⚙️ Setup & Run

### Prerequisites
- PHP 8.2+ (for backend)
- Node.js 18+ (for frontend)
- Git

### 1️⃣ Backend Setup

```bash
# From project root
cd gestion_taches.back

# Install PHP dependencies
composer install

# Copy environment file (if needed)
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations (if needed)
php artisan migrate

# Start backend server
php artisan serve
# Backend available at: http://localhost:8000
```

### 2️⃣ Frontend Setup

```bash
# From project root
cd frontend

# Install JavaScript dependencies
npm install

# Start frontend development server
npm run dev
# Frontend available at: http://localhost:3004
```

## 🔌 API Integration

The frontend is pre-configured to communicate with the backend:

- **API Base URL**: `http://localhost:8000`
- **Authentication**: Bearer tokens (Sanctum)
- **CORS**: Enabled on backend

### Default API Endpoints

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/api/auth/register` | POST | User registration |
| `/api/auth/login` | POST | User login |
| `/api/auth/logout` | POST | User logout |
| `/api/user` | GET | Get current user |
| `/api/projects` | GET/POST | Manage projects |
| `/api/tasks` | GET/POST | Manage tasks |
| `/api/tasks/{id}` | PUT/DELETE | Edit/delete tasks |

## 🔑 Authentication Flow

1. User registers/logs in via frontend
2. Backend generates Bearer token (Sanctum)
3. Frontend stores token in localStorage
4. All API requests include `Authorization: Bearer {token}` header
5. Backend validates token and returns data

## 📊 Database

The backend uses **SQLite** with the following structure:

- **users** - User accounts
- **projects** - User projects
- **tasks** - Project tasks
- **personal_access_tokens** - Sanctum auth tokens

Sample test data is pre-populated in the database.

## 🧪 Testing

### Backend API Testing
```bash
cd gestion_taches.back

# Run tests (if configured)
php artisan test

# Test specific endpoint
php artisan tinker
```

### Frontend Testing
```bash
cd frontend

# Run Vitest tests (if configured)
npm run test

# Build for production
npm run build

# Preview production build
npm run preview
```

## 🚀 Deployment

### Backend (Laravel)
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Frontend (Nuxt)
```bash
npm run build
# Outputs to: .output/public/
```

## 📝 Key Files

### Backend
- `routes/api.php` - API route definitions
- `app/Models/` - Database models
- `app/Http/Controllers/` - API controllers
- `database/migrations/` - Database schema

### Frontend
- `app/pages/` - Nuxt pages (auto-routed)
- `app/components/` - Reusable Vue components
- `app/composables/` - Vue 3 composables
- `app/stores/` - Pinia stores (state management)
- `nuxt.config.ts` - Nuxt configuration

## 🐛 Troubleshooting

### Backend won't start
```bash
php artisan migrate          # Run pending migrations
php artisan cache:clear      # Clear application cache
php artisan config:clear     # Clear config cache
```

### Frontend won't connect to backend
1. Verify backend is running: `curl http://localhost:8000/api/user`
2. Check CORS is enabled in backend
3. Verify API base URL in `frontend/nuxt.config.ts`

### Database issues
```bash
php artisan db:fresh --seed   # Reset database with seed data
php artisan tinker            # Interactive shell for debugging
```

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Nuxt Documentation](https://nuxt.com)
- [Tailwind CSS Documentation](https://tailwindcss.com)
- [Pinia Documentation](https://pinia.vuejs.org)

## 📄 License

This project is provided as-is for educational purposes.

---

**Last Updated**: 2026-06-26
**Status**: ✅ Fully Functional & Integrated
