---
title: "Architecture Decision Records"
type: index
tags: [adr, decisioni]
created: 2026-11-18
updated: 2026-11-26
---

# Architecture Decision Records

Il **perche'** delle scelte di questo pacchetto: una decisione per file, con le alternative scartate.
Il nome del file e' `NNNN-argomento.md` (numero di decisione, niente date: la data sta nella frontmatter).

## Formato

```markdown
---
title: "ADR — <decisione in una riga>"
type: decision
status: accepted | proposed | rejected | deprecated | superseded   # se superseded: superseded_by
created: AAAA-MM-GG
updated: AAAA-MM-GG
module: Mobile
decision-makers: <chi decide>
issues: []
---

# ADR: <decisione>

## Contesto
Fatti e vincoli che hanno reso necessaria la scelta (2-3 frasi) e la domanda a cui risponde.

## Driver della decisione
Le forze che pesano: norme, misure, vincoli tecnici, incidenti.

## Alternative scartate
Ogni alternativa seria, con il motivo per cui non e' stata scelta.

## Decisione
Cosa si fa, in concreto, e dove sta nel codice.

## Conseguenze
Pro, contro, debito che resta, cosa misurare. Chi cambia la decisione scrive un nuovo ADR e segna questo `superseded`.

## Conferma
Come si controlla che il codice rispetti la decisione: un test (percorso), un comando eseguibile da `laravel/` o una regola
di review. Se oggi non esiste nulla di automatico: «Nessuna conferma automatica oggi; proposta: ...».
```

Se il codice di oggi contraddice la decisione, si aggiunge `## Stato attuale` con cio' che fa davvero (file e riga). Il test
`Modules/Xot/tests/Unit/Docs/PackagesHaveContractsAndAdrTest.php` impone le intestazioni in italiano.

## Decisioni

- [Mobile resta un guscio agnostico rispetto all'applicazione host](0001-mobile-api-is-platform-agnostic.md) — `proposed`: il modulo definisce contratti e DTO, l'host registra l'adapter; rotte volutamente vuote.
- [Contratti Mobile agnostici](0002-mobile-contracts-are-agnostic.md) — `accepted`: il confine verso i client e' fatto di `MobileGatewayContract` e `RestaurantGateway`; ridondante con il precedente.

## Stato di copertura (2026-11-26)

- 2 decisioni, entrambe con le sei sezioni in italiano, `decision-makers` e `Conferma`.
- Nessuna conferma automatica: la regola sulle dipendenze dall'host e' una voce di review (comando `rg` nell'ADR), il versionamento
  dei contratti non esiste. I due ADR sono ridondanti e uno e' ancora `proposed`: decisione umana aperta (fusione o stato).
- Due punti in cui il codice tradisce l'agnosticismo sono descritti in `## Stato attuale` del primo ADR (host `api.sottana.com`
  in `SyncOfflineOrdersAction`, updater di `config/bifrost.php`).
