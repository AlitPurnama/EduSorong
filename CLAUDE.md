# EduSorong

Indonesian education crowdfunding platform enabling users to create fundraising campaigns for educational purposes with Midtrans payment integration.

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+), Eloquent ORM
- **Frontend**: Blade templates, Tailwind CSS 4, Vite 7
- **Database**: SQLite (default), MySQL/PostgreSQL supported
- **Payments**: Midtrans (QRIS, e-wallets, virtual accounts)
- **Testing**: PHPUnit 11.5
- **Code Style**: Laravel Pint

## Project Structure

```
app/
├── Http/
│   ├── Controllers/       # 13 controllers (Auth, Campaign, Payment, Admin, etc.)
│   ├── Requests/          # Form validation (Login, Register, StoreCampaign, etc.)
│   └── Middleware/        # AdminMiddleware for role-based access
├── Models/                # 8 Eloquent models
├── Services/              # MidtransService for payment gateway
├── Mail/                  # 7 mailable classes for notifications
├── Policies/              # CampaignPolicy for authorization
└── View/Components/       # Blade components

database/
├── migrations/            # 21 migrations
├── factories/             # 8 factories for testing
└── seeders/

routes/web.php             # All routes (public, auth, admin, payment, webhook)

resources/views/
├── admin/                 # Admin dashboard pages
├── auth/                  # Login/register forms
├── campaigns/             # Campaign listing & detail
├── dashboard/             # User dashboard
├── settings/              # User profile, KTP, email settings
├── withdrawal/            # Withdrawal requests & evidence
└── emails/                # Email templates
```

## Key Models & Relationships

| Model | Key Fields | Primary Relationships |
|-------|------------|----------------------|
| User | role, ktp_verification_status, email_verified_at | campaigns, organizationVerifications |
| Campaign | target_amount, raised_amount | user, payments, withdrawalRequests |
| Payment | amount, payment_method, status | campaign, user |
| WithdrawalRequest | status (pending/approved/completed) | campaign, evidences |
| OrganizationVerification | status (pending/approved/rejected) | user, campaigns |

## Commands

```bash
# Setup & Development
composer setup              # Full setup (install, key, migrate, build)
composer dev                # Dev server + queue + logs + vite
php artisan serve           # Start dev server only

# Testing
composer test               # Run all tests
php artisan test            # Alternative test command
php artisan test --filter=CampaignManagementTest  # Run specific test

# Build
npm run dev                 # Development with hot reload
npm run build               # Production build

# Database
php artisan migrate         # Run migrations
php artisan migrate:fresh --seed  # Reset and seed database

# Utilities
php artisan storage:link    # Create storage symlink
php artisan test:email test@example.com  # Test email configuration
./vendor/bin/pint           # Run code style fixer
```

## Environment Variables

Required in `.env`:
```
# Database
DB_CONNECTION=sqlite

# Mail (for email verification)
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@example.com

# Midtrans Payment Gateway
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
```

## Key Business Rules

1. **Withdrawals**: Campaign must reach 80% of target before withdrawal (`Campaign::canRequestWithdrawal()` at `app/Models/Campaign.php:97`)
2. **Minimum donation**: Rp 10,000 (validated in `PaymentController`)
3. **Anonymous donations**: Donor names masked via `Payment::maskName()` at `app/Models/Payment.php:88`
4. **Admin approval required**: Organization verification, KTP verification, withdrawal requests, campaign deletions

## Test Structure

Tests in `tests/Feature/`:
- `AdminActionsTest` - Admin verification, approval, rejection workflows
- `CampaignManagementTest` - Campaign CRUD operations
- `PaymentEndpointsTest` - Payment creation, status, webhooks
- `WithdrawalFlowTest` - Withdrawal request and evidence upload
- `UserSettingsTest` - Profile, password, email, KTP, account deletion

## File Storage Directories

| Directory | Purpose |
|-----------|---------|
| `storage/app/public/campaigns/` | Campaign images |
| `storage/app/public/ktp/` | KTP verification photos |
| `storage/app/public/organization-documents/` | Organization verification docs |
| `storage/app/public/withdrawal-evidences/` | Withdrawal evidence files |

## Additional Documentation

When working on specific features, check these files for detailed patterns:

| Topic | File |
|-------|------|
| Architectural patterns & conventions | `.claude/docs/architectural_patterns.md` |

## Quick Reference

- **Admin routes**: Prefixed with `/admin`, protected by `AdminMiddleware`
- **Payment webhook**: `POST /notification/midtrans` (CSRF excluded)
- **Email templates**: `resources/views/emails/`
- **Service registration**: `app/Providers/AppServiceProvider.php`
