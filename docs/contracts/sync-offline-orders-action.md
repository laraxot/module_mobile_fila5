---
title: "Contratto — SyncOfflineOrdersAction"
type: contract
action: "Modules\\Mobile\\Actions\\Mobile\\SyncOfflineOrdersAction"
module: Mobile
status: active
tags: [contract, action, mobile, offline, sync, servizi-esterni]
created: 2026-11-26
updated: 2026-11-26
related:
  - kds-notification-action.md
  - split-bill-action.md
  - ../adr/mobile-api-is-platform-agnostic.md
---

# SyncOfflineOrdersAction

Contratto di comportamento (regola: `Modules/Xot/docs/rules/action-behavioral-contract.md`).
Sorgente: `app/Actions/Mobile/SyncOfflineOrdersAction.php` (story 2.2). Invia al server gli ordini che il cameriere ha preso senza
rete e che `TakeOrderAction` ha accodato in `mobile_order_queue` con stato `pending`.

Stato d'uso: nessun codice di produzione la chiama (`rg SyncOfflineOrdersAction`: solo la classe) e nessun test la esercita.

## Input

- `execute(string $waiterSessionId)`: la sessione del cameriere di cui sincronizzare la coda.
- Implicito: un `RestaurantGateway` (porta del modulo: costruttore, altrimenti il binding del container) e, in sua assenza,
  `config('mobile.api_url')`.

## Output

- `array{synced: list<id>, failed: list<id>, total: int, timestamp: string}`: gli id elaborati, il numero di righe `pending`
  trovate (`total`) e l'istante della chiamata.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| MOBILE-SYNC-INV-1 | Si elaborano solo le righe della sessione indicata con `status` = `pending`: righe `synced`, `failed` o `syncing` non vengono mai rinviate da questa Action. | — (non dimostrato) |
| MOBILE-SYNC-INV-2 | Il fallimento di una riga non ferma le altre e non risale al chiamante: ogni `Throwable` marca la riga `failed` e il ciclo prosegue. | — (non dimostrato) |
| MOBILE-SYNC-INV-3 | Esito positivo → `synced`, `sync_attempts` +1, `last_sync_at`, `error_message` nullo. Esito negativo → `failed`, `sync_attempts` +1, `error_message` valorizzato. | — (non dimostrato) |
| MOBILE-SYNC-INV-4 | Ogni invio porta una chiave di idempotenza: `order_data.idempotency_key`, altrimenti l'id della riga in coda, così un rinvio non duplica l'ordine sul server. | — (non dimostrato) |
| MOBILE-SYNC-INV-5 | Con un `RestaurantGateway` disponibile l'ordine passa solo da `createOrder` (`source` = `mobile_waiter`, `send_to_stations` = `true`); la chiamata HTTP parte solo senza gateway. | — (non dimostrato) |

## Effetti collaterali

Per ogni riga `pending`, nell'ordine:

1. Con gateway: `RestaurantGateway::createOrder([...])` con `table_id`, `idempotency_key`, `items`, `covers`, `waiter_id`.
   Senza gateway: `POST` HTTP (timeout 15 s) a `<config('mobile.api_url') o https://api.sottana.com>/api/mobile/orders` con
   `order_data`, `waiter_session_id`, `table_id`, `queue_id`, `idempotency_key`, senza intestazione di autenticazione.
2. `UPDATE` della riga in coda (`synced` o `failed`, `sync_attempts`, `last_sync_at`, `error_message`).

## Errori

- Nessuna eccezione verso il chiamante per i singoli ordini (INV-2): finiscono in `failed` con il messaggio.
- Un errore di lettura iniziale (`SELECT` della coda) risale al chiamante.

## Non fa

- Non riprova gli ordini `failed`: restano tali finché qualcuno non li riporta a `pending` (lo `scopeFailed` del modello non è
  usato qui).
- Non marca le righe `syncing` e non usa blocchi: due esecuzioni contemporanee per la stessa sessione leggono le stesse righe e
  le inviano due volte. L'unica difesa è la chiave di idempotenza (INV-4), che il server deve rispettare.
- Non crea ordini in coda (`TakeOrderAction`) e non notifica la cucina (`KdsNotificationAction`).
- Non valida `order_data`: un ordine con `items` mancanti viene inviato con `items` vuoto.

## Limiti noti (difetti da correggere, non da imitare)

- **Dati verso un dominio di un altro progetto.** `config/mobile.php` non definisce `api_url` (`rg api_url` nel modulo: solo questa
  Action), quindi senza gateway l'Action invia gli ordini a `https://api.sottana.com`, un dominio che non è di questo progetto
  (compare anche in `config/bifrost.php`, `owner` = `sottana`). Un host che non lega nessun `RestaurantGateway` spedisce righe
  d'ordine, id di sessione e di tavolo a quel server, senza autenticazione. Prima di usarla: definire `mobile.api_url` o
  legare il gateway.
- `error_message` salva l'intero corpo della risposta HTTP in caso di errore (colonna `text`): contenuto non controllato.
- `OrderQueue::$table_id` è documentato `int` ma la colonna è `foreignUuid`
  (`database/migrations/create_mobile_tables.php`, riga 35): il tipo dichiarato non corrisponde allo schema.
- `now()->toISOString() ?? now()->toAtomString()`: il ramo `??` non scatta mai (`toISOString` restituisce stringa).
