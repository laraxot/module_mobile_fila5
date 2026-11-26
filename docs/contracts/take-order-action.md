---
title: "Contratto — TakeOrderAction"
type: contract
action: "Modules\\Mobile\\Actions\\Mobile\\TakeOrderAction"
module: Mobile
status: active
tags: [contract, action, mobile, ordine, idempotenza, offline]
created: 2026-11-26
updated: 2026-11-26
related:
  - sync-offline-orders-action.md
  - scan-qr-action.md
---

# TakeOrderAction

Contratto di comportamento (regola: `Modules/Xot/docs/rules/action-behavioral-contract.md`).
Sorgente: `app/Actions/Mobile/TakeOrderAction.php` (story 2.2). Presa ordine del cameriere offline-first: accoda l'ordine in
`mobile_order_queue` con stato `pending`; l'invio al server lo fa `SyncOfflineOrdersAction`.

Stato d'uso: nessun codice di produzione la chiama (`rg TakeOrderAction`: solo la classe e i test).

## Input

- `execute(array $payload)`: `waiter_session_id`, `table_id`, `idempotency_key` e `items` (lista non vuota) sono obbligatori;
  `waiter_id` e `qr_code` sono facoltativi.
- Ogni voce di `items`: `product_id` (intero ≥ 1), `quantity` (≥ 1, default 1), `notes`, `modifiers` (array).
- Implicito: un `RestaurantGateway` (costruttore) che risolve il tavolo.

## Output

- `string`: l'id (uuid) della riga in coda, nuova o già esistente per la stessa chiave.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| MOBILE-TAKEORDER-INV-1 | Stessa `waiter_session_id` + `idempotency_key` su una riga non `failed` restituisce quella riga, senza secondo INSERT. Una riga `failed` non conta: con la stessa chiave si crea una riga nuova. | — (non dimostrato) |
| MOBILE-TAKEORDER-INV-2 | Un tavolo che il gateway non restituisce (`null`) lancia `InvalidArgumentException('Tavolo non trovato o non attivo')`; l'Action non scrive la coda. «Attivo» lo decide il gateway, non l'Action. | `MOBILE-TAKEORDER-INV-2` |
| MOBILE-TAKEORDER-INV-3 | Payload senza sessione, tavolo, chiave di idempotenza o con `items` vuoto, un item non array o con `product_id` < 1 → `InvalidArgumentException`, prima di leggere il gateway o scrivere. | `MOBILE-TAKEORDER-INV-3` |
| MOBILE-TAKEORDER-INV-4 | La riga nuova ha `status` = `pending` e `order_data` con item normalizzati (`product_id` intero, `quantity` ≥ 1), `source` = `mobile_waiter`, `channel` = `waiter`, `status` = `open`, `table_name` dal gateway. | — (non dimostrato: nessun test scrive sul database) |

Test: `tests/Unit/Actions/TakeOrderActionTest.php` usa un `RestaurantGateway` finto e non tocca il database. Il test di INV-3 prova
sessione, tavolo, chiave e `items` vuoti; non prova gli item non array né `product_id` < 1.

## Effetti collaterali

Nell'ordine:

1. `RestaurantGateway::table($tableId)` (lettura).
2. `SELECT` su `mobile_order_queue` per la chiave di idempotenza (percorso JSON, con ripiego su scansione in PHP se il driver non
   lo supporta).
3. `INSERT` su `mobile_order_queue` (solo se non esiste già).

Nessuna notifica, nessuna chiamata di rete, nessuna transazione.

## Errori

- `InvalidArgumentException` per payload non valido o tavolo assente (INV-2, INV-3): nessuna riga scritta.
- Errori del database risalgono al chiamante.

## Non fa

- Non invia l'ordine al ristorante né al server (`SyncOfflineOrdersAction`) e non avvisa la cucina (`KdsNotificationAction`).
- Non controlla che i prodotti esistano nel catalogo: valida solo che `product_id` sia un intero positivo.
- Non autentica il cameriere né verifica che la sessione esista: `waiter_session_id` è preso com'è.
- Non calcola il totale dell'ordine.

## Limiti noti (difetti da correggere, non da imitare)

- `table_id` è convertito con `(int)` (colonna e `order_data`), ma `mobile_order_queue.table_id` è `foreignUuid`
  (`database/migrations/create_mobile_tables.php`, riga 35) e `RestaurantGateway::table()` accetta `int|string`: un id di tavolo
  uuid diventerebbe un intero sbagliato (0 o le sole cifre iniziali). Stesso schema `uuid` anche per `waiter_session_id`.
- L'idempotenza è un controllo prima dell'INSERT: nessun indice univoco sulla chiave (sta dentro `order_data`, colonna `text`) e
  nessuna transazione. Due richieste identiche in contemporanea possono creare due righe.
- I messaggi delle eccezioni sono testi italiani scritti nel codice (non passano dai file di lingua).
