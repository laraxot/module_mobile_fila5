---
title: "Contratto — SplitBillAction"
type: contract
action: "Modules\\Mobile\\Actions\\Mobile\\SplitBillAction"
module: Mobile
status: active
tags: [contract, action, mobile, conto, importi, legacy]
created: 2026-11-26
updated: 2026-11-26
related:
  - kds-notification-action.md
  - sync-offline-orders-action.md
---

# SplitBillAction

Contratto di comportamento (regola: `Modules/Xot/docs/rules/action-behavioral-contract.md`).
Sorgente: `app/Actions/Mobile/SplitBillAction.php`. Divide il conto di un ordine in coda tra più persone: in parti uguali, per
importi liberi o per voci. Maneggia importi di denaro.

Stato d'uso: nessun codice di produzione la chiama (`rg SplitBillAction`: solo la classe) e nessun test la esercita. **Non registra
pagamenti**: calcola le quote e le salva nell'ordine in coda.

## Input

- `execute(int $orderId, string $splitType, array $splits, ?array $paymentMethods = null)`.
- `$orderId`: riga di `mobile_order_queue` (il totale è `order_data['total']`, 0 se non numerico).
- `$splitType`: `equal`, `custom` o `items`.
- `$splits`: una voce per persona, con `notes`, e `amount` (`custom`) oppure `item_ids` (`items`); per `equal` conta solo il numero.
- `$paymentMethods`: metodo per indice di `$splits`, default `cash`.

## Output

- `list<array{order_id, amount: float, method: string, notes: string}>`: una quota per voce di `$splits`.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| MOBILE-SPLIT-INV-1 | Tipo di divisione sconosciuto → `InvalidArgumentException('Unknown split type: …')` e nessuna scrittura. | — (non dimostrato) |
| MOBILE-SPLIT-INV-2 | `equal`: ogni quota vale `round(totale / numero di split, 2)`, con almeno 1 split a denominatore. | — (non dimostrato) |
| MOBILE-SPLIT-INV-3 | `custom`: la somma degli importi deve coincidere col totale entro 0,01, altrimenti `InvalidArgumentException('Split amounts do not match total')` e nessuna scrittura. | — (non dimostrato) |
| MOBILE-SPLIT-INV-4 | `items`: la quota di una persona è la somma di `quantity × (unit_price o price)` delle voci dell'ordine i cui id sono in `item_ids`; un id inesistente vale 0. | — (non dimostrato) |
| MOBILE-SPLIT-INV-5 | Il calcolo e la scrittura delle quote in `order_data['splits']` stanno nella stessa `DB::transaction`: o c'è l'esito completo o l'ordine resta com'era. | — (non dimostrato) |
| MOBILE-SPLIT-INV-6 | Un ordine con `status` = `paid` lancia `InvalidArgumentException('Order already paid')` e non si modifica. | — (non dimostrato; vedi limite noto: oggi la regola non può scattare) |

## Effetti collaterali

Nell'ordine:

1. `SELECT` su `mobile_order_queue` (`findOrFail`).
2. Dentro `DB::transaction`: calcolo delle quote e `UPDATE` di `order_data` = dati precedenti + `splits` (le quote), che
   sostituiscono quelle di una chiamata precedente.

Nessun pagamento, nessuna notifica, nessun evento.

## Errori

- `ModelNotFoundException` se l'ordine non esiste.
- `InvalidArgumentException` per ordine già pagato (INV-6), tipo sconosciuto (INV-1), importi che non tornano (INV-3).
- Errori del database: rollback della transazione e risalita al chiamante.

## Non fa

- Non incassa e non registra pagamenti: `method` è solo un'etichetta (default `cash`).
- Non controlla che le voci siano tutte assegnate né che una voce non sia assegnata a due persone (`items`).
- Non assegna i centesimi di resto in `equal`.
- Non cambia `status` dell'ordine in coda (non esiste uno stato «paid»).
- Non verifica chi la chiama.

## Limiti noti (difetti da correggere, non da imitare)

- Il controllo `status === 'paid'` è codice morto: `OrderQueue` ha solo `pending`, `syncing`, `synced`, `failed`
  (`Models/OrderQueue.php`), e il commento nell'Action dice che non esiste uno stato «paid». Un conto già diviso si può dividere
  di nuovo: le quote precedenti vengono sovrascritte senza traccia.
- `equal` arrotonda ogni quota: 10,00 diviso 3 dà tre quote da 3,33 e la somma è 9,99. Il centesimo mancante non è assegnato a nessuno.
- `$orderId` è `int`, ma `mobile_order_queue.id` è un `uuid` (`database/migrations/create_mobile_tables.php`, riga 33): con
  `strict_types` un id reale non si può passare. Il tipo di ritorno dichiara `order_id: string`, in contraddizione col parametro.
- Importi in `float` e `round()`: nessun tipo per il denaro; per codice nuovo usare interi in centesimi.
- I messaggi delle eccezioni sono in inglese e scritti nel codice (non passano dai file di lingua).
