# CosyClinic HMS — Hospital Management System

Enterprise-grade OPD clinic and hospital management system built with **Laravel 13**, **PHP 8.3**, **Livewire 4**, **MySQL/SQLite**, **Spatie Permissions**, **Tabler UI**, and **DomPDF**.

## Features

- **Reception** — Walk-in/OPD/Emergency registration, live patient search, token queue, OPD slip + QR print
- **Doctor Panel** — Live waiting queue, consultation, prescriptions (public/private), send to pharmacy
- **Pharmacy** — Prescription queue, dispensing, GST billing, invoice print
- **Billing** — OPD/Pharmacy/Lab invoices, multi-payment (Cash/UPI/Card)
- **Lab** — Pending tests, result entry, PDF report upload
- **Admin** — Users, medicines, inventory alerts, hospital settings, analytics dashboard
- **Token Display** — Real-time OPD queue screen (Livewire polling)
- **REST API** — Sanctum-ready endpoints for patients, visits, queue, stats
- **Architecture** — Repository + Service pattern, audit logs, role-based access

## Requirements

- PHP 8.3+
- Composer
- MySQL 8+ (or SQLite for development)
- Node.js (optional, for asset building)

## Installation (XAMPP / LAMPP)

```bash
cd "/opt/lampp/htdocs/CosyClinic HMS"
composer install
cp .env.example .env
php artisan key:generate
```

### MySQL Configuration (.env)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=CosyClinic_hms
DB_USERNAME=root
DB_PASSWORD=
```

```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

### Run Application

```bash
php artisan serve
# Visit http://127.0.0.1:8000
```

For XAMPP, point Apache document root or create a virtual host to `/public`.

## Demo Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@CosyClinic.test | password |
| Receptionist | reception@CosyClinic.test | password |
| Doctor | doctor@CosyClinic.test | password |
| Pharmacist | pharmacy@CosyClinic.test | password |
| Accountant | accounts@CosyClinic.test | password |
| Lab Technician | lab@CosyClinic.test | password |
| Nurse | nurse@CosyClinic.test | password |

## Workflow

1. **Reception** registers patient → prints OPD token
2. Click **Send to Doctor** → appears in doctor queue
3. **Doctor** starts consultation → adds diagnosis & prescription → **Send to Pharmacy**
4. **Pharmacy** dispenses medicines → completes order → billing
5. **Accountant** records payments on invoices

## API (Sanctum)

Create token: `$user->createToken('api')->plainTextToken`

```
GET /api/patients/search?q=raj
GET /api/visits/queue
GET /api/queue/display
GET /api/dashboard/stats
```

## Print Routes

- `/print/opd-slip/{visit}`
- `/print/prescription/{visit}`
- `/print/pharmacy-invoice/{order}`
- `/print/patient-card/{patient}`

## Project Structure

```
app/
├── Enums/           # VisitStatus, VisitPriority
├── Http/Controllers/
├── Models/
├── Repositories/
├── Services/        # Patient, Consultation, Pharmacy, Invoice
database/
├── migrations/      # Full HMS schema
├── seeders/         # Roles, demo users, medicines
resources/views/
├── components/      # Livewire 4 single-file components
├── layouts/         # Tabler admin layout
├── pages/           # Module pages
└── prints/          # Indian clinic style print templates
```

## Real-Time (Laravel Reverb + Echo)

The premium UI uses **WebSockets** for instant queue/dashboard updates (no page refresh).  
`wire:poll` is used as a fallback when Reverb is offline.

### Start real-time services

```bash
# Terminal 1 — Application
php artisan serve

# Terminal 2 — WebSocket server
php artisan reverb:start

# Terminal 3 — Queue worker (broadcasts + jobs)
php artisan queue:work

# Terminal 4 — Frontend (dev)
npm run dev
```

Ensure `.env` has:

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=CosyClinic-hms
REVERB_APP_KEY=your-key
REVERB_APP_SECRET=your-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

### What updates live

- Reception / Doctor / Pharmacy queues  
- Token display screen (`/queue/display`)  
- Admin dashboard stats & charts  
- Toast + notification center (per user)  

## Premium UI Features

- Fixed glassmorphism sidebar + mobile bottom nav  
- Registration wizard (3 steps)  
- Doctor split-screen consultation workspace  
- Pharmacy POS layout with sticky bill summary  
- ApexCharts analytics dashboard  
- Dark mode, toast alerts, skeleton-ready components  

## License

MIT — Built for charitable trust hospitals, OPD clinics, and dispensary workflows.
