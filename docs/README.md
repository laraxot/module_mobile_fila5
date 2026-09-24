---
title: "Mobile — Documentation index"
type: documentation
tags: [module, documentation, mobile, nativephp, restaurant-boundary]
created: 2026-09-17
updated: 2026-09-17
---

# Mobile module docs

On-demand index for `Modules/Mobile/docs`. Open only the story you need; this
file is the map, not a copy of their content.

## Story BMAD (`stories/`)

| Story | Argomento |
|---|---|
| [1.1](stories/1.1.mobile-nativephp-waiter-app.story.md) | Vision NativePHP waiter app: feature, stack, principi di sviluppo |
| [1.7](stories/1.7.mobile-waiter-order-taking.story.md) | Presa ordini offline, sync, KDS |
| [1.8](stories/1.8.nativephp-integration.story.md) | Integrazione NativePHP (build, Bifrost) |
| [1.9](stories/1.9.mobile-tenant-mapping.story.md) | Mappatura multi-tenant Mobile/Restaurant |
| [1.10](stories/1.10.legacy-to-mobile-migration.story.md) | Migrazione da legacy `_bases/ristorante` |
| [1.11](stories/1.11.mobile-module-boundary.story.md) | Confine architetturale Mobile → Restaurant (contratto + DTO); story più recente, canonica per lo stato attuale del boundary |

Le story 1.1/1.7/1.8/1.9/1.10 sono documenti di visione/pianificazione: non
riscriverle per inseguire lo stato del codice, che evolve più in fretta. La
1.11 è quella che riflette il confine architetturale attuale ed è il punto di
partenza per capire cosa è già stato fatto sul boundary Mobile/Restaurant.

## Mappa codice (verificata 2026-09-17)

- `app/Contracts/RestaurantGateway.php` — porta verso Restaurant (story 1.11)
- `app/Data/{CategoryData,ProductData,TableData}.php` — DTO immutabili dello scambio, coperti da `tests/Unit/Data/RestaurantDataTest.php`
- `app/Actions/Mobile/*` — `TakeOrderAction`, `SplitBillAction`, `ViewFloorPlanAction`, `KdsNotificationAction`, `ScanQrAction`, `SyncOfflineOrdersAction` (Spatie `QueueableAction`)
- `app/Models/Mobile/{WaiterSession,OrderQueue}.php` — UUID PK, vedi `database/migrations/create_mobile_tables.php`
- `app/Filament/{Pages/Mobile/WaiterDashboard,Resources/Mobile/{OrderResource,TableResource},Widgets/Mobile/OrderWidget,Forms/Components/OrderForm}.php` — UI XotBase; `TableResource`/`OrderResource` sono già stati ripuliti dalla dipendenza diretta da Restaurant (vedi commento `RIMOSSO: dipendenza Mobile -> Restaurant proibita` in `TableResource.php`)
- `app/Http/Controllers/MobileController.php` (web) + `Http/Controllers/Api/MobileController.php` (api, fuori da `app/`) — due controller distinti, vedi gap sotto
- `routes/{web,api,mobile}.php` — tre file rotte separati (web con Blade, api REST, mobile con Actions chiamate direttamente da closure)
- `app/Plugins/MobilePluginRegistry.php` — elenco/config dei plugin NativePHP (camera, push, biometrics, location, haptics, sharing, deep links, secure storage, gallery, offline)

## Gap noti (non risolti in questo pass — documentazione only)

- **Boundary incompleto**: la story 1.11 introduce `RestaurantGateway` + DTO, ma
  5 delle 6 Actions in `app/Actions/Mobile/*` (`TakeOrderAction`, `SplitBillAction`,
  `ViewFloorPlanAction`, `KdsNotificationAction`, `ScanQrAction` — non
  `SyncOfflineOrdersAction`, che tocca solo `OrderQueue` via HTTP),
  `app/Models/Mobile/{WaiterSession,OrderQueue}`, entrambi i `MobileController`
  (web e api) e `routes/mobile.php` importano ancora direttamente classi
  `Modules\Restaurant\Models\*`. La migrazione dei consumer verso il gateway —
  esplicitamente indicata come da completare nella story 1.11 stessa — non
  risulta fatta per questi file.
- **Principio "No HTTP Controllers" (story 1.1) non applicato**: esistono
  `app/Http/Controllers/MobileController.php` (rotte web, un metodo per azione) e
  `Http/Controllers/Api/MobileController.php` (rotte api). Nessuna pagina Folio nel
  modulo.
- **View Blade mancanti**: `routes/web.php` referenzia le view `mobile::mobile.order`
  e `mobile::mobile.menu`; `WaiterDashboard::getView()` referenzia
  `mobile::waiter-dashboard`. Nessuna delle tre esiste sotto `resources/views/mobile/`
  (solo `app.blade.php` e `tables.blade.php` sono presenti). Probabile bug
  applicativo, non corretto qui perché fuori scope per un pass documentazione-only —
  segnalato per un follow-up sul codice.
- **Nessuna documentazione per l'area Filament/UI** (`OrderResource`, `TableResource`,
  `WaiterDashboard`, `OrderWidget`, `OrderForm`) né per `MobilePluginRegistry`: solo le
  story BMAD sopra esistono. Non viene inventata qui una pagina per queste aree
  (sarebbe documentazione speculativa); segnalato come gap da colmare quando
  quell'area del codice si stabilizza.

## Altri link

- [README del modulo](../README.md)
- [Regole di progetto](../../../../docs/wiki/)
