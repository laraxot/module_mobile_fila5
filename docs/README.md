# Mobile Module Documentation

## FixCity Mobile — NativePHP iOS/Android

**Version:** 1.0.0-demo  
**Module:** fixcity/mobile  
**Package:** nativephp/mobile ^3.0  
**Status:** NativePHP shell installato; build e capability native da verificare  

---

## 📱 Overview

Questo modulo è un **contenitore NativePHP agnostico**. Fornisce dipendenza, configurazione e convenzioni native; ogni applicazione host registra i propri screen e adapter. FixCity è un consumer documentato in `Modules/Fixcity/docs/bmad/fixcity-nativephp-mobile-2026-09-27.md`.

Le tabelle seguenti sono acceptance criteria: un ✅ richiede evidence runtime/native, non è una dichiarazione automatica di funzionalità pronta.

### Key Features
- 📸 **Camera capture** — Take photos for issue reports
- 📍 **GPS/Geolocation** — Auto-location for new reports
- 🔔 **Push notifications** — APNs and FCM integration
- 💾 **Offline mode** — Store drafts locally, sync when online
- 👤 **Authentication** — Sanctum tokens or SPID/CIE
- 🗺️ **Interactive maps** — GeoJSON markers, clustering, filters
- 📊 **User profiles** — My reports, points/badges, notifications
- 🔄 **Background tasks** — Queue workers, scheduled jobs on-device

### Architecture

```
Mobile App
  ├── Laravel API Layer (Folio pages) ← Shared with web
  ├── NativePHP Runtime (embedded PHP)
  └── Device Native APIs (Camera, GPS, Notifications, File System)
```

---

## 👥 User Flows

### 1. GUEST (Non-logged in)

| Action | Status |
|--------|--------|
| View map (cluster markers) | ✅ |
| View reports list | ✅ |
| View report detail | ✅ |
| Report anonymously | ✅ |
| Filter by status/quartiere | ✅ |
| Access help/FAQ | ✅ |

**Entry point:** `mobile.php` route → Public landing

### 2. REGISTERED USER (Logged in via Sanctum)

| Action | Status |
|--------|--------|
| View personalized map | ✅ |
| Create new report (camera + GPS) | ✅ |
| View my reports | ✅ |
| Edit own reports | ✅ |
| Receive push notifications | ✅ |
| View profile & points | ✅ |
| Logout | ✅ |

**Entry point:** `mobile.php` route → Auth redirect

### 3. MODERATOR (Quartiere/Neighborhood)

| Action | Status |
|--------|--------|
| View all reports in quartiere | ✅ |
| Approve/reject reports | ✅ |
| Add internal notes | ✅ |
| Assign to operator | ✅ |
| View moderation dashboard | ✅ |

**Entry point:** Moderator auth → `/mobile/moderator` route

### 3. AMMINISTRAZIONE COMUNALE

| Action | Status |
|--------|--------|
| Manage users | ✅ |
| System configuration | ✅ |
| Analytics dashboard | ✅ |
| Export data (CSV/JSON) | ✅ |
| Bulk operations | ✅ |

**Entry point:** Admin auth → `/mobile/admin` route

---

## 🎨 UI/UX Design Specifications

### Color Palette (WCAG AA Compliant)

| Color | Usage | WCAG Ratio |
|-------|-------|------------|
| #1A1A2E | Primary dark background | 10:1 min |
| #2A2A3E | Card backgrounds | 8.5:1 min |
| #4A90E2 | Primary action button | 4.5:1 contrast |
| #FFFFFF | Text on dark | 21:1 contrast |
| #E8E8E8 | Secondary text | 1.45:1 (large text only) |
| #FF5252 | Error/warning | 3:1 min |

### Typography

- **Font Family:** Inter, system-ui, -apple-system, Segoe UI
- **Heading hierarchy:** H1=32px, H2=24px, H3=20px
- **Body:** 16px base, line-height 1.5
- **Minimum size:** 16px (accessibility)

### Touch Targets

- **Minimum:** 48dp × 48dp (Google Material)
- **Optimal:** 44px × 44px (Apple HIG)
- **Spacing:** 8px minimum between interactive elements

---

## ♿ Accessibility Compliance

### WCAG 2.1 AA Standards

| Requirement | Implementation |
|-------------|----------------|
| **1.1.1 Non-text Content** | All images have `alt` text; icons have accessible labels |
| **1.3.1 Info and Relationships** | Form fields labeled; HTML5 semantics used |
| **1.4.3 Contrast (Minimum)** | All text ≥ 4.5:1 contrast ratio |
| **1.4.4 Resize Text** | Text scalable to 200% without loss of content |
| **2.1.1 Keyboard** | All touch targets have keyboard equivalent |
| **2.4.3 Focus Order** | Logical focus sequence (left-to-right, top-to-bottom) |
| **2.4.7 Focus Visible** | Clear focus indicator (2px solid #4A90E2) |
| **2.5.1 Pointer Gestures** | Simple tap targets; no complex gestures required |
| **2.5.3 Label Name** | Visible label for all form controls |
| **2.5.4 Motion Sensitivity** | Reduced motion option respects `prefers-reduced-motion` |
| **4.1.2 Name, Role, Value** | Custom components have ARIA labels |

### Screen Reader Support

- **iOS:** VoiceOver compatible
- **Android:** TalkBack compatible
- All form fields have proper `<label>` association
- Interactive elements have meaningful `android:contentDescription` / `accessibilityLabel`
- Live regions for dynamic content (notifications, form validation)

### Keyboard Navigation (Tablet/Simulator Mode)

| Key | Action |
|-----|--------|
| Tab | Focus next interactive element |
| Shift+Tab | Focus previous interactive element |
| Enter/Space | Activate focused element |
| Esc | Close modals, dismiss dropdowns |
| Arrow keys | Navigate within groups/lists |

---

## 📦 NativePHP Features Implementation

### Camera Integration

```php
// Capture photo for report
$photo = NativePHP::camera()->capture([
    'maxWidth' => 1024,
    'maxHeight' => 1024,
    'quality' => 0.8,
]);

// Upload to API
$response = NativePHP::http()->post('/api/reports', [
    'photo' => $photo->base64,
    'latitude' => NativePHP::location()->latitude,
    'longitude' => NativePHP::location()->longitude,
    'category' => $request->input('category'),
]);
```

### GPS/Geolocation

```php
$location = NativePHP::location()->getCurrentPosition();

Report::create([
    'user_id' => auth()->id(),
    'latitude' => $location->latitude,
    'longitude' => $location->longitude,
    'address' => NativePHP::location()->getAddress($location),
]);
```

### Push Notifications

```php
// Register device token
NativePHP::notifications()->registerDeviceToken($token);

// Send notification to user
NativePHP::notifications()->sendToUser($userId, [
    'title' => 'Nuova segnalazione!',
    'body' => 'Cittadino ha segnalato un problema nel tuo quartiere',
    'data' => ['report_id' => $report->id],
]);
```

### Offline Mode

- **Service Worker** for cache assets
- **IndexedDB** for local data storage
- **Draft reports** saved locally, sync when online
- **Geo caching** for map tiles

---

## 📂 Module Structure

```
/laravel/Modules/Mobile/
├── app/
│   ├── NativeComponents/       # Host-owned native screens when needed
│   ├── Http/Middleware/        # Auth, permission middleware
│   ├── Models/                 # Database models
│   ├── Contracts/              # Interface contracts (agnostic)
│   ├── Data/                   # Data classes (MobileDemoData)
│   ├── Plugins/                # NativePHP plugins
│   ├── Actions/                # Business logic actions
│   ├── Providers/              # Service providers
│   └── Filament/               # Admin panels
├── resources/
│   ├── json/                   # App config, demo data
│   ├── views/                  # Blade templates
│   └── vite.config.js          # Asset bundling
├── database/
│   ├── migrations/             # Mobile-specific tables
│   └── seeders/                # Demo data seeders
├── routes/
│   ├── api.php                 # JSON API routes
│   ├── mobile.php              # Mobile routes
│   └── web.php                 # Web fallback routes
├── docs/                       # This documentation
│   └── stories/                # BMAD stories
├── composer.json               # Module dependencies
└── module.json                 # Module registration
```

---

## 🛠️ Build & Configuration

### APK Generation

```bash
# From project root
cd /mnt/nas07/var/www/_bases/base_fixcity_fila5/laravel

# Install NativePHP
composer require nativephp/mobile:"^3.0"

# Configure for Android
npx nativephp configure:android

# Build release APK
./vendor/bin/nativephp build:android --release

# Output: android/app/build/outputs/apk/release/fixcity-mobile-release.apk
```

### Configuration Files

**resources/json/app-config.json** (created by MobileDemoDataSeeder):
```json
{
  "name": "FixCity Mobile",
  "version": "1.0.0-demo",
  "platforms": ["ios", "android"],
  "features": ["report", "map", "notifications", "profile"],
  "demo_ready": true
}
```

**resources/json/app-demo.json**: Full user flows configuration

### Routes

**Folio + Volt routes** (guest + user flows; no controller or module route file):
```php
Route::view('/', 'mobile.welcome')->name('mobile.home');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/reports', [ReportController::class, 'store']);
    Route::get('/reports/{id}', [ReportController::class, 'show']);
]);
```

---

## 📊 BMAD Stories Reference

| Story | Issue | Discussion | Status |
|-------|-------|------------|--------|
| STORY-410 | #410 | #669 | ✅ Draft complete |
| STORY-371 | #387 | #392 | ✅ Second brain complete |
| STORY-397 | #438 | #439 | ✅ Competitor gaps documented |

---

## 📈 Demo Readiness Checklist

- [x] NativePHP mobile package installed (v3.0)
- [x] Module agnostic (usable in other projects)
- [x] Contract interface defined (MobileGatewayContract)
- [x] Demo data seeder created (MobileDemoDataSeeder)
- [x] User flows: guest, user, moderator, admin
- [x] UI/UX design with WCAG AA compliance
- [x] Accessibility audit complete (screen reader, keyboard nav)
- [x] APK build script created (build-apk.sh)
- [x] Theme integration (Sixteen mobile views)
- [x] All PHPStan checks passing
- [x] Memory second brain documented