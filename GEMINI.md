# EduSorong - Project Context for Gemini

## 1. Project Overview
**EduSorong** is an Indonesian crowdfunding platform dedicated to education. It allows users to create fundraising campaigns, donate using various payment methods (Midtrans), and manage fund withdrawals with a strict verification process.

### Tech Stack
-   **Framework:** Laravel 12 (PHP 8.2+)
-   **Frontend:** Blade Templates, Tailwind CSS 4, Vite 7
-   **Database:** SQLite (default for dev), MySQL/PostgreSQL (production)
-   **Payment Gateway:** Midtrans (Snap API, Core API)
-   **Testing:** PHPUnit 11.5
-   **Icons:** Lucide Icons

## 2. Key Directories & Files
-   **`app/Models/`**: Core entities (`User`, `Campaign`, `Payment`, `WithdrawalRequest`, `OrganizationVerification`).
-   **`app/Http/Controllers/`**: Business logic. Note separate controllers for specific features (e.g., `CampaignUpdateController`, `WithdrawalEvidenceController`).
-   **`routes/web.php`**: Single entry point for all web routes (Public, Auth, Admin, Payment Webhooks).
-   **`resources/views/`**: Blade templates.
    -   `admin/`: Admin dashboard.
    -   `campaigns/`: Public campaign pages.
    -   `dashboard/`: User dashboard.
    -   `emails/`: Email templates.
-   **`database/migrations/`**: Schema definitions.
-   **`tests/Feature/`**: Integration tests covering critical flows (Payments, Withdrawals, Admin actions).

## 3. Development Workflow

### Setup
1.  **Dependencies:** `composer install` && `npm install`
2.  **Environment:** `cp .env.example .env` && `php artisan key:generate`
3.  **Database:** `touch database/database.sqlite` && `php artisan migrate`
4.  **Storage:** `php artisan storage:link` (Critical for images)

### Running the App
-   **Backend:** `php artisan serve` (Runs on port 8000)
-   **Frontend:** `npm run dev` (Vite hot reload)
-   **Queue:** `php artisan queue:listen` (For emails/jobs)

### Testing
-   **Run All:** `php artisan test`
-   **Specific Test:** `php artisan test --filter=TestClassName`
-   **Coverage:** `php artisan test --coverage`

## 4. Key Business Rules & constraints
-   **Withdrawals:** Only allowed if campaign funds reached **>= 80%** of target (`Campaign::canRequestWithdrawal()`).
-   **Donations:** Minimum amount is **Rp 10,000**.
-   **Payments:** Handled via **Midtrans**. Webhooks are processed at `/notification/midtrans`.
-   **Verification:**
    -   **KTP:** Required for certain user actions.
    -   **Organization:** Required to create organization-backed campaigns.
    -   **Campaign Deletion:** Requires Admin approval.

## 5. Coding Conventions
-   **Style:** Follow Laravel Pint standards.
-   **Naming:** snake_case for database columns/variables, CamelCase for classes, kebab-case for URLs.
-   **Controllers:** Keep skinny. Move complex logic to Services (e.g., `MidtransService`) or Models.
-   **Views:** Use Blade Components (`resources/views/components`) for reusable UI elements.
-   **Commits:** Clear, descriptive messages (e.g., "Fix: campaign withdrawal logic", "Feat: add email verification").

## 6. Common Tasks & Commands
-   **Create Migration:** `php artisan make:migration create_table_name`
-   **Create Model + Migration:** `php artisan make:model ModelName -m`
-   **Create Controller:** `php artisan make:controller NameController`
-   **Clear Cache:** `php artisan optimize:clear`
-   **Link Storage:** `php artisan storage:link`

## 7. Known Issues / Notes
-   **Midtrans Sandbox:** Ensure `MIDTRANS_IS_PRODUCTION=false` in `.env`.
-   **Email:** Uses SMTP. For local dev, check `.env` mail settings (Mailtrap/Gmail).
-   **Role Management:** Users have a `role` column ('user' or 'admin').
