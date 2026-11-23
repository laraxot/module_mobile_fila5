---
status: proposed
date: 2026-11-23
decision-makers: Mobile module owner
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

- Contratti: [`MobileGatewayContract`](../../app/Contracts/MobileGatewayContract.php)
  e [`RestaurantGateway`](../../app/Contracts/RestaurantGateway.php).
- Route: [`routes/web.php`](../../routes/web.php) e [`routes/api.php`](../../routes/api.php).
- Riferimento: [`docs/README.md`](../README.md).
