---
title: "Contratto — ScanQrAction"
type: contract
action: "Modules\\Mobile\\Actions\\Mobile\\ScanQrAction"
module: Mobile
status: accepted
updated: 2026-11-26
---

# ScanQrAction

Classe: `Modules\\Mobile\\Actions\\Mobile\\ScanQrAction`

## Input

- `string $qrPayload`

## Output

- `array` contesto catalogo/ordine risolto dal gateway (niente mock inline).

## Invarianti

| ID | Regola | Verifica |
|---|---|---|
| MOBILE-QR-INV-1 | Delega a `RestaurantGateway`; non contiene dati mock hard-coded nell'Action. | `->group('MOBILE-QR-INV-1')` |
| MOBILE-QR-INV-2 | Record mancanti/inattivi non inventano un menu: falliscono o tornano vuoto secondo il gateway. | `->group('MOBILE-QR-INV-2')` |

## Effetti collaterali

- Letture via gateway; nessun write di ordine qui.

## Errori

- Errori gateway / payload invalido.

## Non fa

- Non sincronizza ordini offline (`SyncOfflineOrdersAction`).
