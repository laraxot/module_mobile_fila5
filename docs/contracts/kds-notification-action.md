---
title: "Contratto — KdsNotificationAction"
type: contract
action: "Modules\\Mobile\\Actions\\Mobile\\KdsNotificationAction"
module: Mobile
status: active
tags: [contract, action, mobile, kds, notifiche, legacy]
created: 2026-11-26
updated: 2026-11-26
related:
  - split-bill-action.md
  - sync-offline-orders-action.md
---

# KdsNotificationAction

Contratto di comportamento (regola: `Modules/Xot/docs/rules/action-behavioral-contract.md`).
Sorgente: `app/Actions/Mobile/KdsNotificationAction.php`. Invia un ordine in coda ai monitor di cucina (Kitchen Display System).

**Stato: scheletro dimostrativo.** Il codice contiene dati finti e un destinatario push sempre vuoto (vedi «Limiti noti»): oggi
non consegna nessuna notifica reale. Nessun codice di produzione la chiama (`rg KdsNotificationAction`: solo la classe e
`tests/Arch/debt.php`) e nessun test la esercita. Non è il modello per codice nuovo.

## Input

- `execute(int $orderId, bool $isNewOrder = true)`: `$orderId` è l'id di una riga `mobile_order_queue`; `$isNewOrder` distingue
  il primo invio (`new_order`) da una modifica (`update`).

## Output

- Nessuno (`void`).

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| MOBILE-KDS-INV-1 | Un ordine che non esiste in coda lancia `ModelNotFoundException` (`findOrFail`) prima di costruire o inviare qualunque notifica. | — (non dimostrato) |
| MOBILE-KDS-INV-2 | Il payload ha `type` = `new_order` se `$isNewOrder`, altrimenti `update`, e `order_id` = id della riga in coda. | — (non dimostrato) |
| MOBILE-KDS-INV-3 | La priorità è la somma di: +100 se le note contengono «rush» (senza distinguere maiuscole), +30 se le voci sono più di 3, altrimenti +15 se sono più di 1. | — (non dimostrato) |
| MOBILE-KDS-INV-4 | Il broadcast verso gli schermi della cucina è richiesto prima dell'invio push ai dispositivi: nessuno dei due passi è in `try/catch`, quindi un errore del primo salta il secondo. | — (non dimostrato) |

## Effetti collaterali

Nell'ordine:

1. `SELECT` su `mobile_order_queue` (`findOrFail`).
2. `broadcast(new KdsOrderNotification($payload))->toOthers()`.
3. `Notification::send($this->getKitchenStaff(), new KdsPushNotification($payload))`: servizio esterno OneSignal tramite il
   canale `onesignal`, con `app_id` da `config('mobile.onesignal_app_id')`.

Nessuna scrittura sul database: lo stato dell'ordine non cambia.

## Errori

- `ModelNotFoundException` se l'ordine non esiste (INV-1). Ogni altra eccezione (broadcast, notifica) arriva al chiamante.

## Non fa

- Non cambia lo stato dell'ordine né segna la consegna alla cucina.
- Non risolve tavolo, cameriere e prodotti dal catalogo: usa solo `order_data` della coda.
- Non ritenta e non accoda: `QueueableAction` la rende accodabile dal chiamante, l'Action non lo fa da sé.
- Non verifica chi la chiama.

## Limiti noti (difetti da correggere, non da imitare)

- Il payload contiene valori finti: `table_number` = `'1'`, `table_name` = `'Tavolo 1'`, `waiter_name` = `'Operatore Demo'` (commento
  «Simplified for demo»), nome prodotto `'Prodotto Demo'` quando manca, e `uniqid()` come id voce quando manca. La cucina vedrebbe
  sempre «Tavolo 1». Sono testi italiani nel codice, fuori dai file di lingua.
- `getKitchenStaff()` restituisce sempre una collezione vuota: `Notification::send` non raggiunge nessuno, quindi il push non parte
  mai. Se la collezione si riempisse, `KdsPushNotification::via()` usa il canale `onesignal`, ma nel progetto non c'è nessun pacchetto
  OneSignal (`vendor/laravel-notification-channels` contiene solo `fcm` e `telegram`): l'invio fallirebbe.
- `KdsOrderNotification` è una `Notification` (canali `broadcast` e `database`) ma viene passata all'helper `broadcast()`, che
  serve per gli eventi: nessun test prova che il messaggio arrivi sul canale della cucina.
- `$orderId` è dichiarato `int`, ma `mobile_order_queue.id` è un `uuid` (`database/migrations/create_mobile_tables.php`, riga 33,
  e `HasUuids` in `Models/OrderQueue.php`): con `strict_types` un id reale non si può nemmeno passare. Stesso difetto in
  `SplitBillAction`.
- `tests/Arch/debt.php` elenca questa classe tra il debito «security» (`uniqid`).
