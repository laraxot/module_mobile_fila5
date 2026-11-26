---
title: "Contratti"
type: index
tags: [contracts, actions, interfaces]
created: 2026-11-18
updated: 2026-11-26
---

# Contratti

Cosa promette la superficie pubblica di questo pacchetto: le Action che scrivono dati, chiamano servizi esterni o hanno
effetti collaterali, e le interfacce (`app/Contracts`). Regola: `Modules/Xot/docs/rules/action-behavioral-contract.md`.

## Contratto di comportamento di un'Action

Un file per Action, nome in kebab-case della classe (`create-ticket-action.md`), frontmatter `type: contract` e
`action: <FQCN>`, e queste sei sezioni nell'ordine:

```markdown
## Input
## Output
## Invarianti
## Effetti collaterali
## Errori
## Non fa
```

Ogni invariante ha un ID `MOBILE-<OPERAZIONE>-INV-<N>` e, se esiste un test, un group Pest con lo stesso nome
(`cd laravel && vendor/bin/pest Modules/Mobile/tests --group=<ID>`); `— (non dimostrato)` e' debito dichiarato.
Il docblock dell'Action contiene `@see docs/contracts/<file>.md` e `@contract docs/contracts/<file>.md` (lo controlla
`Modules/Xot/tests/Unit/Docs/ActionContractsAreCompleteTest.php`). Chi cambia il comportamento aggiorna il contratto nello stesso commit.

## Contratti

- [KdsNotificationAction](kds-notification-action.md) — invia l'ordine ai monitor di cucina; 4 invarianti, nessun group. Scheletro dimostrativo: dati finti, destinatari push sempre vuoti.
- [SplitBillAction](split-bill-action.md) — divide il conto (parti uguali, importi, voci) e salva le quote nell'ordine in coda; 6 invarianti, nessun group. Non registra pagamenti.
- [SyncOfflineOrdersAction](sync-offline-orders-action.md) — invia al server gli ordini offline `pending`; 5 invarianti, nessun group. Senza gateway scrive a `api.sottana.com`.
- [TakeOrderAction](take-order-action.md) — accoda l'ordine del cameriere con chiave di idempotenza; 4 invarianti, nessun group.
- [ScanQrAction](scan-qr-action.md) — risolve un QR in tavolo, prodotto o categoria; sola lettura; 3 invarianti, nessun group.

`index.md` e' il registro operativo previsto da `Modules/Xot/docs/adr/0012-behavioral-contract-governance.md`; questo README resta l'indice richiesto dai test di pacchetto.

## Stato di copertura (2026-11-26)

| Misura | Valore |
|---|---|
| Contratti completi (sei sezioni, `action` esistente, `@contract` nel docblock) | 5 |
| Invarianti dichiarate | 22 (KDS 4, SPLIT 6, SYNC 5, TAKEORDER 4, SCANQR 3) |
| Con group Pest e test che le esercita | 4 (`MOBILE-TAKEORDER-INV-2`, `MOBILE-TAKEORDER-INV-3`, `MOBILE-SCANQR-INV-1`, `MOBILE-SCANQR-INV-2`) |
| Non dimostrate | 18 |

I 4 group sono stati aggiunti leggendo i test, non eseguendoli (Pest non gira in questa sessione): il test e' la prova solo se e' verde.
Nessun test del modulo esercita Kds, SplitBill e SyncOfflineOrders.

## Backlog contract

- `Mobile\ViewFloorPlanAction`: costruisce la vista di un tavolo (`execute(TableData)`), nessuna scrittura ne' chiamata esterna.

Le altre Action del modulo hanno un contratto.

## Backlog test

Invarianti senza prova, in ordine di rischio:

- `MOBILE-SYNC-INV-1`…`5`: il fallimento isolato per riga e la chiave di idempotenza, con un `RestaurantGateway` finto (come in `TakeOrderActionTest`) e `Http::fake`.
- `MOBILE-SPLIT-INV-1`…`6`: importi, con un caso che mostri il centesimo perso in `equal` e uno sul controllo `paid` (che oggi non puo' scattare).
- `MOBILE-KDS-INV-1`…`4`: payload e priorita', con `Notification::fake`; prima bisogna decidere cosa deve fare davvero l'Action (oggi e' uno scheletro).
- `MOBILE-TAKEORDER-INV-1` e `INV-4`: servono la tabella `mobile_order_queue` e un database di prova (la chiave di idempotenza e la riga `pending`).
- `MOBILE-SCANQR-INV-3`: un test che asserisca zero query durante `execute`.
- Prima di qualunque test su Kds e SplitBill: allineare il tipo di `$orderId` (`int`) con la colonna `uuid` di `mobile_order_queue`, altrimenti
  l'Action non si puo' chiamare con un id reale. Stessa incoerenza in `TakeOrderAction` (`(int) $tableId` su una colonna `foreignUuid`).
