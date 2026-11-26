---
title: "ADR — Contratti Mobile agnostici"
type: decision
status: accepted
created: 2026-11-23
updated: 2026-11-26
issues: []
module: Mobile
decision-makers: Mobile module owner
related:
  - 0001-mobile-api-is-platform-agnostic.md
---

# ADR — Contratti Mobile agnostici

Questa decisione e [Mobile resta un guscio agnostico](0001-mobile-api-is-platform-agnostic.md) dicono la stessa cosa da due lati: quella
spiega perché il modulo non conosce il dominio dell'host, questa fissa che il confine verso i client è fatto di contratti. Restano
due file perché hanno stato diverso (`proposed` e `accepted`): vedi «Stato attuale».

## Contesto

Client e integrazioni mobili possono cambiare piattaforma e versione. Il modulo definisce due porte in `app/Contracts`:
`MobileGatewayContract` (segnalazioni: `reportIssue`, `getNearbyReports`, `getReport`, `updateStatus`) e `RestaurantGateway`
(tavoli, prodotti, categorie, `createOrder`). La domanda: chi possiede la forma dei dati che passano tra client, modulo e host?

## Driver della decisione

- Il contratto deve restare riusabile e testabile senza un client nativo né un dominio host (`TakeOrderActionTest` usa un gateway finto).
- Il ciclo di rilascio di un'app nativa (negozi, aggiornamenti) è più lento di quello del server: il server non può assumere che tutti
  i client siano aggiornati.
- UI e ciclo di rilascio appartengono ai client; il modulo non deve imporli.

## Alternative scartate

- Legare il modulo a un singolo client nativo: ogni cambio del client obbligherebbe a rilasciare il modulo.
- Esporre rotte API proprie del modulo: duplicherebbe il confine che l'host già ha (le rotte di `routes/api.php` e `routes/web.php`
  sono volutamente vuote, vedi l'altro ADR).

## Decisione

Mobile espone contratti e gateway agnostici (`MobileGatewayContract`, `RestaurantGateway`); l'host registra l'adapter concreto. UI e
ciclo di rilascio restano ai client.

## Conseguenze

- Buono: le integrazioni sono sostituibili dietro il contratto.
- Cattivo: un cambio incompatibile del contratto richiede coordinamento con ogni host e con le app già installate.
- Cattivo: il versionamento che la decisione richiede per i cambi incompatibili non esiste ancora (vedi «Stato attuale»).

## Conferma

Nessuna conferma automatica oggi sul versionamento. Controlli che esistono:

- `tests/Unit/Actions/TakeOrderActionTest.php` e `tests/Unit/Actions/ScanQrActionTest.php` usano un `RestaurantGateway` finto: se il
  contratto cambia firma, falliscono.
- Regola di review: un cambio a `app/Contracts/*.php` va annunciato nel contratto delle Action che lo usano
  (`docs/contracts/sync-offline-orders-action.md`).

Proposta: aggiungere un campo di versione al payload scambiato con i client prima del primo cambio incompatibile.

## Stato attuale

- Non c'è versionamento del payload: le rotte API sono vuote (`routes/api.php` restituisce `[]`) e nessun contratto porta una
  versione. «Cambi incompatibili richiedono versionamento» è un impegno, non un meccanismo.
- `MobileGatewayContract` non ha implementazioni nel repository (`rg MobileGatewayContract`: solo l'interfaccia): è una porta
  dichiarata ma non usata. `RestaurantGateway` ha solo le implementazioni finte dei test.
- Le due decisioni sono ridondanti. Decisione umana aperta: fonderle in un solo ADR (e quale stato dargli) oppure tenerle distinte.
