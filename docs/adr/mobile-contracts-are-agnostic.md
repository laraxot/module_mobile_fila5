---
title: "ADR — Contratti Mobile agnostici"
type: decision
status: accepted
created: 2026-11-23
updated: 2026-11-23
issues: []
module: Mobile
---

# ADR — Contratti Mobile agnostici

## Contesto

Client e integrazioni mobili possono cambiare piattaforma e versione.

## Driver della decisione

- Mantenere il contratto riusabile e testabile.

## Decisione

Mobile espone contratti e gateway agnostici; UI e ciclo di rilascio restano ai client.

## Alternative scartate

- Legare il modulo a un singolo client nativo.

## Conseguenze

Le integrazioni sono sostituibili; cambi incompatibili richiedono versionamento.

## Conferma

Verificare contratti, gateway e test API prima di cambiare il payload.
