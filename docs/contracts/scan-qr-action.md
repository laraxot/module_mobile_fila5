---
title: "Contratto — ScanQrAction"
type: contract
action: "Modules\\Mobile\\Actions\\Mobile\\ScanQrAction"
module: Mobile
status: active
tags: [contract, action, mobile, qr]
created: 2026-11-26
updated: 2026-11-26
---

# ScanQrAction

Contratto di comportamento (regola: `Modules/Xot/docs/rules/action-behavioral-contract.md`).
Sorgente: `app/Actions/Mobile/ScanQrAction.php`. Risolve un payload QR in contesto reale
(tavolo/prodotto/categoria) via `RestaurantGateway`. Solo lettura.

## Input

- `string $qrPayload`: `table:<id>`, `product:<id>`, `category:<id>`, oppure solo id numerico (= tavolo).

## Output

- Array `{ type, table, product, category, raw }` con un solo ramo valorizzato.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| MOBILE-SCANQR-INV-1 | Payload vuoto o formato sconosciuto → `InvalidArgumentException`. | `MOBILE-SCANQR-INV-1` |
| MOBILE-SCANQR-INV-2 | Tavolo, prodotto o categoria mancante (o tavolo inattivo) dal gateway → `InvalidArgumentException`, senza restituire dati. | `MOBILE-SCANQR-INV-2` |
| MOBILE-SCANQR-INV-3 | Nessuna scrittura DB; delega solo a `RestaurantGateway`. | — (non dimostrato: i test usano un gateway finto e non guardano il database) |

## Effetti collaterali

- Nessuno di persistenza; sole letture gateway.

## Errori

- `InvalidArgumentException` su parse o risoluzione.

## Non fa

- Non crea ordini (`TakeOrderAction`).
- Non contiene mock di catalogo.

Test: `tests/Unit/Actions/ScanQrActionTest.php`. `test('result shape never exposes foreign tenant fields')` prova che il risultato non
ha `tenant_id` né `location_id`, ma è un controllo sulla forma dell'output, non su un invariante dichiarato.
