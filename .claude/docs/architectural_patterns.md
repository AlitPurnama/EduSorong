# Architectural Patterns & Conventions

This document describes recurring patterns and design decisions in the EduSorong codebase.

## 1. Service Layer Pattern

External integrations are encapsulated in dedicated service classes.

**Implementation**: `app/Services/MidtransService.php`

- Constructor initializes configuration (`app/Services/MidtransService.php:14-18`)
- Methods return consistent response structure: `['success' => bool, 'data' => array|null, 'error' => mixed]`
- Exception handling with try-catch blocks (`app/Services/MidtransService.php:52,120,195`)
- Centralized logging with `Log` facade (`app/Services/MidtransService.php:73,141,216`)

**Injection Pattern**:
```php
public function __construct(MidtransService $midtransService)
```
See: `app/Http/Controllers/PaymentController.php:16-20`, `app/Http/Controllers/NotificationController.php:14-19`

## 2. Form Request Validation

Validation logic is extracted to FormRequest classes.

**Location**: `app/Http/Requests/`

| Class | Purpose | File Reference |
|-------|---------|----------------|
| LoginRequest | Email/password validation | `app/Http/Requests/LoginRequest.php:7-32` |
| RegisterRequest | Registration with password confirmation | `app/Http/Requests/RegisterRequest.php:7-22` |
| StoreCampaignRequest | Campaign creation validation | `app/Http/Requests/StoreCampaignRequest.php:7-28` |

**Pattern**: All requests use `authorize() => true` (route middleware handles auth), validation in `rules()`.

## 3. Direct Authorization Checks

Instead of policies everywhere, ownership is verified directly in controllers.

**Standard Pattern**:
```php
if ($model->user_id !== Auth::id()) {
    abort(403, 'Unauthorized.');
}
```

**Locations**:
- `app/Http/Controllers/CampaignController.php:133` (destroy)
- `app/Http/Controllers/CampaignController.php:175` (requestDeletion)
- `app/Http/Controllers/WithdrawalRequestController.php:15,41,77`
- `app/Http/Controllers/WithdrawalEvidenceController.php:17,33,69`
- `app/Http/Controllers/CampaignUpdateController.php:16,46`
- `app/Http/Controllers/OrganizationVerificationController.php:116`

**Role-based**: `AdminMiddleware` at `app/Http/Middleware/AdminMiddleware.php:9-28` checks `auth()->user()->isAdmin()`.

## 4. Status Field Convention

All approval/verification models use a consistent `status` field pattern.

| Model | Status Values |
|-------|---------------|
| OrganizationVerification | pending, approved, rejected |
| WithdrawalRequest | pending, approved, rejected, completed |
| WithdrawalEvidence | pending, verified, rejected |
| CampaignDeletionRequest | pending, approved, rejected |
| Payment | pending, paid, failed, cancelled, expired |
| User.ktp_verification_status | pending, approved, rejected |

**Helper Methods Pattern**: Each model implements status checkers:
- `isPending()`, `isApproved()`, `isRejected()`, `isCompleted()`
- See: `app/Models/WithdrawalRequest.php:50-70`, `app/Models/Payment.php:56-70`

## 5. Database Transaction Pattern

Multi-step operations use explicit transactions for atomicity.

**Locations**:
- `app/Http/Controllers/AdminController.php:43-49` (approveVerification)
- `app/Http/Controllers/AdminController.php:92-101` (approveWithdrawal - updates status + decrements campaign amount)
- `app/Http/Controllers/NotificationController.php:65-133` (payment webhook - `DB::beginTransaction()`, `DB::commit()`, `DB::rollBack()`)

## 6. Eager Loading Pattern

Relationships are eager loaded to prevent N+1 queries.

**Examples**:
- `app/Http/Controllers/CampaignController.php:14`: `Campaign::with(['user', 'organizationVerification'])`
- `app/Http/Controllers/CampaignController.php:32-52`: Nested eager loading with closures
- `app/Http/Controllers/AdminController.php:34`: `OrganizationVerification::with(['user', 'verifier'])`
- `app/Http/Controllers/WithdrawalRequestController.php:81`: `with(['campaign', 'evidences.verifier'])`

## 7. File Storage Convention

Files stored in organized directories under `storage/app/public/`.

**Pattern**:
```php
$file->store('directory', 'public');  // Store
Storage::disk('public')->delete($path);  // Delete
```

**Directory Structure**:
| Directory | Controller | Line |
|-----------|------------|------|
| campaigns/ | CampaignController | 122 |
| profiles/ | UserSettingsController | 40 |
| ktp/ | UserSettingsController | 102 |
| organization-documents/ | OrganizationVerificationController | 95 |
| withdrawal-evidences/ | WithdrawalEvidenceController | 49 |

**Cleanup Pattern**: Delete old file before uploading new one:
- `app/Http/Controllers/UserSettingsController.php:35-36`
- `app/Http/Controllers/UserSettingsController.php:98-99`

## 8. Mail Notification Pattern

Email notifications use Laravel Mail classes (not Notification classes).

**Location**: `app/Mail/`

**Structure**: Each mail class uses:
- Constructor with model injection (e.g., `public function __construct(public User $user)`)
- `Envelope` for subject
- `Content` with markdown template reference

**Sending Pattern**:
```php
try {
    Mail::to($user->email)->send(new SomeMail($user));
    Log::info('Email sent successfully');
} catch (\Exception $e) {
    Log::error('Failed to send email: ' . $e->getMessage());
}
```
See: `app/Http/Controllers/AuthController.php:57-78`, `app/Http/Controllers/AdminController.php:260-268`

## 9. JSON Response Pattern

API endpoints return consistent JSON structure.

**Success**:
```json
{"success": true, "payment": {...}, "additional_data": "..."}
```
See: `app/Http/Controllers/PaymentController.php:149-154`

**Validation Error**:
```json
{"success": false, "message": "Validasi gagal", "error": {...}}
```
See: `app/Http/Controllers/PaymentController.php:47-51`

**General Error**:
```json
{"success": false, "message": "Error description", "error": {...}}
```
See: `app/Http/Controllers/PaymentController.php:126-130`

## 10. Token Generation Pattern

Unique tokens generated using `Str::random()`.

**Locations**:
- Email verification: `Str::random(64)` at `app/Http/Controllers/AuthController.php:46`
- Account deletion: `app/Http/Controllers/UserSettingsController.php:254,195`

**Payment Order IDs**:
- QRIS: `'QRIS-' . time() . '-' . Str::random(6)` at `app/Http/Controllers/PaymentController.php:55`
- E-Wallet: `'EWALLET-' . time() . '-' . Str::random(6)` at `app/Http/Controllers/PaymentController.php:192`
- Virtual Account: `'VA-' . strtoupper($bank) . '-' . time() . '-' . Str::random(6)` at `app/Http/Controllers/PaymentController.php:317`

## 11. Model Accessor Pattern

Models use accessor methods for computed/formatted values.

**Campaign** (`app/Models/Campaign.php`):
- `total_withdrawn` (lines 58-62): Sum of approved withdrawal amounts
- `remaining_balance` (lines 68-70): raised_amount - total_withdrawn
- `progress_percentage` (lines 110-116): Target percentage calculation
- `canRequestWithdrawal()` (lines 97-104): 80% threshold check

**Payment** (`app/Models/Payment.php`):
- `donor_display_name` (lines 76-82): Masked name for anonymous donations
- `maskName()` (lines 88-108): Name masking utility

**WithdrawalRequest** (`app/Models/WithdrawalRequest.php`):
- `formatted_amount` (lines 72-74): Currency formatting
- `hasUploadedEvidence()` (lines 85-87)
- `allEvidencesVerified()` (lines 93-99)

## 12. Pagination Pattern

List endpoints use pagination with query string preservation.

**Pattern**:
```php
Model::where(...)->paginate(N)->withQueryString()
```

**Locations**:
- `app/Http/Controllers/CampaignController.php:25`: `paginate(6)->withQueryString()`
- `app/Http/Controllers/AdminController.php:36,79,183,241`: `paginate(15)`

## 13. Logging Convention

Consistent logging using `Log` facade throughout controllers.

**Error logging**:
```php
Log::error('Error message: ' . $e->getMessage());
Log::error('Exception trace: ' . $e->getTraceAsString());
```

**Info logging** (successful operations):
```php
Log::info('Operation completed for user: ' . $user->email);
```

**Warning logging** (non-critical issues):
```php
Log::warning('Email written to log only - mailer is log driver');
```

## 14. Route Organization

Routes in `routes/web.php` are organized by authentication level:

| Lines | Group | Middleware |
|-------|-------|------------|
| 13-27 | Public | none |
| 36-55 | Auth (guest only) | guest |
| 58-135 | Authenticated user | auth |
| 138-164 | Admin panel | auth, admin |
| 167-179 | Payment endpoints | none (for guests) |
| 182-184 | Webhooks | none (CSRF exempt) |

## 15. Cache Invalidation Pattern

Cache cleared on data changes that affect cached queries.

**Example**: `app/Http/Controllers/NotificationController.php:107`
```php
Cache::forget('recent_donations_feed');
```
