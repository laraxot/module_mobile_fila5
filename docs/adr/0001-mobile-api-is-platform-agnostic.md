---
title: "ADR — Mobile resta un guscio agnostico rispetto all'applicazione host"
type: decision
status: proposed
created: 2026-11-23
updated: 2026-11-26
module: Mobile
decision-makers: Mobile module owner
related:
  - 0002-mobile-contracts-are-agnostic.md
  - ../contracts/sync-offline-orders-action.md
---

# Mobile resta un guscio agnostico rispetto all'applicazione host

## Contesto

Mobile espone contratti per consumare capacità dell'applicazione, ma non possiede
i modelli o la persistenza del dominio che li implementa. `MobileGatewayContract`
definisce operazioni sui report e `RestaurantGateway` una porta per dati e ordini;
le route web e API del modulo sono intenzionalmente vuote. La decisione risponde a
questa domanda: dove deve stare la logica applicativa quando Mobile viene installato
in host diversi?

## Driver della decisione

- riusabilità del pacchetto in applicazioni diverse;
- separazione tra contratti Mobile e modelli del dominio host;
- integrazione con Folio/Volt senza introdurre route controller proprietarie;
- sostituibilità dell'adapter senza cambiare il contratto del client.

## Alternative scartate

- Incorporare in Mobile modelli e persistenza di FixCity o Restaurant: legherebbe il
  pacchetto a un solo dominio.
- Aggiungere controller e route per ogni host: duplicherebbe il confine d'integrazione.
- Chiamare direttamente classi concrete dei consumer: eliminerebbe le porte del modulo.

## Decisione

Mobile è **agnostico rispetto all'applicazione host**: definisce contratti e DTO per
le capacità che consuma, mentre l'host registra l'adapter concreto e conserva la
propria logica. Le route del modulo rimangono vuote; il front office usa Folio e
Volt secondo la composizione dell'applicazione.

## Conseguenze

- Buono, perché il modulo non dipende dai modelli, enum o database di un dominio.
- Buono, perché i consumer possono sostituire l'implementazione dietro il contratto.
- Cattivo, perché ogni host deve fornire e mantenere l'adapter concreto.
- Cattivo, perché modifiche incompatibili richiedono coordinamento con gli host.

## Conferma

- Dipendenze: da `laravel/`, `rg -n 'Modules\\(Restaurant|Fixcity|Ticket)\b' Modules/Mobile/app Modules/Mobile/routes Modules/Mobile/config`
  deve restare senza risultati (verificato il 2026-11-26: nessun risultato). Nessun test automatico lo controlla: finché non esiste,
  è una voce di review. Proposta: una regola Pest arch in `Modules/Mobile/tests/Arch` che vieta quei namespace.
- Porte: `app/Contracts/MobileGatewayContract.php` e `app/Contracts/RestaurantGateway.php`. `tests/Unit/Actions/TakeOrderActionTest.php`
  gira con un gateway finto (`TakeOrderFakeGateway implements RestaurantGateway`): prova che `TakeOrderAction` funziona senza il dominio host.
- Route: `routes/web.php` e `routes/api.php` restituiscono `[]` (verifica a vista).

## Stato attuale

Il codice rispetta la decisione sulle dipendenze, ma due punti la tradiscono nello spirito:

- `Actions/Mobile/SyncOfflineOrdersAction.php` (riga 60): senza `RestaurantGateway` invia gli ordini a `https://api.sottana.com`, un host
  di un progetto preciso, perché `config/mobile.php` non ha la chiave `api_url`. Dettaglio nel [contratto](../contracts/sync-offline-orders-action.md).
- `config/bifrost.php` (`updater.owner` = `sottana`, `repo` = `restaurant-fila5`): l'aggiornamento dell'app punta al repository
  del progetto di origine, non a quello dell'host.

`MobileGatewayContract` non ha nessuna implementazione nel repository (`rg MobileGatewayContract`: solo l'interfaccia).
