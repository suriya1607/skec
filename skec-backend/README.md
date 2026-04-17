# SKEC Backend — Laravel 11 REST API

> Sri Kumaran Education Centre — Educational Learning Platform Backend

## Requirements

| Requirement | Version |
|---|---|
| PHP | ≥ 8.2 |
| Composer | ≥ 2.x |
| MySQL | ≥ 8.0 |
| Node.js (optional, for Vite) | ≥ 18 |

---

## 1. Clone & Install

```bash
git clone <repository-url> skec-backend
cd skec-backend
composer install
```

---

## 2. Environment Setup

Copy the example file and configure:

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

| Variable | Description |
|---|---|
| `APP_NAME` | Platform display name |
| `APP_URL` | Backend URL (default: `http://localhost:8000`) |
| `FRONTEND_URL` | Vue frontend URL (default: `http://localhost:5173`) |
| `DB_DATABASE` | MySQL database name (`skec_platform`) |
| `DB_USERNAME` | MySQL username |
| `DB_PASSWORD` | MySQL password |
| `SANCTUM_STATEFUL_DOMAINS` | Should match frontend origin (`localhost:5173`) |
| `MAIL_*` | SMTP settings for invitation emails |
| `NOTES_STORAGE_PATH` | Local storage path for PDFs (default: `private/notes`) |

---

## 3. Database Setup

Create the MySQL database first:

```sql
CREATE DATABASE skec_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Then run migrations and seeders **in order**:

```bash
# Run all migrations
php artisan migrate

# Seed in order (Settings → Admin → Categories)
php artisan db:seed --class=SettingsSeeder
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=CategorySeeder

# OR seed all at once (DatabaseSeeder handles order)
php artisan db:seed
```

---

## 4. Storage Setup

```bash
php artisan storage:link
```

The notes are stored in `storage/app/private/notes/` — **never exposed directly** to the public.

---

## 5. Run the Server

```bash
php artisan serve --port=8000
```

API base URL: `http://127.0.0.1:8000/api/v1`

---

## 6. Default Admin Credentials

| Field | Value |
|---|---|
| Email | `admin@srikumaran.in` |
| Password | `Admin@123` |

> ⚠️ **Change this password immediately in production!**

---

## 7. API Overview

### Authentication Headers

All authenticated requests require:
```
Authorization: Bearer {sanctum_token}
X-Session-Token: {session_token}
```

Both tokens are returned on successful login.

### Base URL

```
http://127.0.0.1:8000/api/v1
```

### Response Format

```json
// Success
{ "success": true, "data": {...}, "message": "..." }

// List
{ "success": true, "data": [...], "meta": { "current_page": 1, "total": 50 }, "message": "..." }

// Error
{ "success": false, "error": "error_code", "message": "...", "errors": {} }
```

---

## 8. Key Endpoints

| Method | Endpoint | Description |
|---|---|---|
| POST | `/auth/login` | Login |
| GET | `/auth/invitation/{token}` | Validate invitation token |
| POST | `/auth/register` | Register from invitation |
| POST | `/auth/logout` | Logout |
| GET | `/auth/me` | Current user info |
| GET | `/notes` | List published notes (student) |
| GET | `/notes/{id}/stream-token` | Get signed PDF stream URL |
| GET | `/admin/dashboard` | Admin dashboard stats |
| POST | `/admin/invitations` | Send student invitation |
| POST | `/admin/notes` | Upload PDF note |
| GET | `/admin/settings` | Platform settings |
| GET | `/settings/public` | Public settings (no auth) |

---

## 9. Testing with Postman

1. Import the API base URL: `http://127.0.0.1:8000/api/v1`
2. Login via `POST /auth/login` with `{"email": "admin@srikumaran.in", "password": "Admin@123"}`
3. Copy `token` → set as `Authorization: Bearer {token}` header
4. Copy `session_token` → set as `X-Session-Token: {session_token}` header
5. All subsequent requests use both headers

---

## 10. Artisan Commands

```bash
# Create admin user
php artisan skec:create-admin admin@example.com "Full Name" --password=Secret@123

# Platform statistics
php artisan skec:stats

# Cleanup expired invitations (>7 days old)
php artisan skec:cleanup-invitations

# Cleanup timed-out sessions
php artisan skec:cleanup-sessions
```

---

## 11. Scheduler (Production)

Add to your server's crontab:

```cron
* * * * * cd /path/to/skec-backend && php artisan schedule:run >> /dev/null 2>&1
```

The scheduler runs:
- `skec:cleanup-invitations` — daily at midnight
- `skec:cleanup-sessions` — every 30 minutes

---

## 12. Queue Worker (for emails)

```bash
php artisan queue:work
```

Invitation emails are queued. Ensure a queue worker is running in production.

---

## Security Notes

- PDF files are stored outside `public/` — never served directly
- File paths (`file_path`) are hidden from all API responses
- PDF streaming uses signed URLs with 30-minute TTL
- Session tokens are SHA-256 hashed before storage
- All API responses use consistent error codes for frontend handling
