---
title: "00 INDEX: docs modulo Mobile"
type: index
status: active
module: Mobile
tags: [mobile, nativephp, module, documentation, index]
created: 2026-10-13
updated: 2026-10-14
qmd: "mobile docs 00-index indice canonico nativephp contratto stato verificato story bmad decision log investigation"
issues:
  - "https://github.com/laraxot/module_mobile_fila5/issues/1"
discussions:
  - "https://github.com/laraxot/module_mobile_fila5/discussions/2"
related:
  - ./README.md
  - ./INDEX.md
  - ../README.md
---

# 00 INDEX: docs modulo Mobile

> **Indice canonico della cartella `docs/`.** [INDEX.md](INDEX.md) non e' un secondo ingresso: e' il dettaglio di ownership e stato
> verificato, linkato qui sotto. Vetrina del modulo: [../README.md](../README.md). Standard:
> [README doppio root e docs](../../../../docs/wiki/standards/module-theme-readme-dual.md).

La documentazione NativePHP descrive capability e acceptance criteria; un segno positivo richiede evidence runtime/native.

## Documenti per natura

| Natura | File | Contenuto |
|---|---|---|
| Contratto | [README.md](README.md) | responsabilita' del modulo, user flows, UI/UX, accessibilita', struttura, build |
| Stato | [INDEX.md](INDEX.md) | ownership, stato verificato (APK, runtime Android), flussi canonici, story superseded |
| Decisioni | [decision-log.md](decision-log.md) | decisioni e domande aperte sulla documentazione |
| Processo | [bmad/README.md](bmad/README.md) | dossier BMAD canonico: scopo, architettura, PRD, epic, gap, release gate |
| Processo | [bmad/bmad-dossier.md](bmad/bmad-dossier.md) | superseded da bmad/README.md, conservato |
| Ricerca | [investigation/investigation.md](investigation/investigation.md) | bozza da template sulla affidabilita' delle push, non completata |
| Archivio | [prompts/01.md](prompts/01.md) | prompt storico utente (controller e rotte via Folio e Volt), non normativo |
| Backlog | `stories/` | story BMAD, vedi sotto |

## Story BMAD (`stories/`)

Le story non si cancellano ne' si rinumerano: si collegano o si marcano superseded. Le story 1.1, 1.7, 1.9 e 1.10 descrivono l'app
cameriere del dominio ristorante e restano come cronologia, superseded dal contratto agnostico (README e story 1.11), come spiegato in
[INDEX.md](INDEX.md).

| Story | Stato |
|---|---|
| [1.1 mobile NativePHP waiter app](stories/1.1.mobile-nativephp-waiter-app.story.md) | superseded da 1.11 |
| [1.7 mobile waiter order taking](stories/1.7.mobile-waiter-order-taking.story.md) | superseded da 1.11 |
| [1.8 NativePHP integration](stories/1.8.nativephp-integration.story.md) | storico, non prova di stato attuale |
| [1.9 mobile tenant mapping](stories/1.9.mobile-tenant-mapping.story.md) | superseded da 1.11 |
| [1.10 legacy to mobile migration](stories/1.10.legacy-to-mobile-migration.story.md) | superseded da 1.11 |
| [1.11 mobile module boundary](stories/1.11.mobile-module-boundary.story.md) | ready-for-dev (contratto vigente) |
| [1.12 PHPStan level max mobile cleanup](stories/1.12.phpstan-level-max-mobile-cleanup.story.md) | done |
| [2026-10-08 PHPStan ScanQrAction e SplitBillAction](stories/2026-10-08-phpstan-mobile-scanqr-splitbill.story.md) | done |
| [PHPStan TakeOrderAction senza modello Fixcity](stories/phpstan-mobile-take-order.story.md) | done |
| [2.1 catalogo reale e contesto QR tavolo](stories/2.1.real-catalog-qr-context.story.md) | done |
| [STORY-MOBILE-INVESTOR-DEMO](stories/STORY-MOBILE-INVESTOR-DEMO.md) | in_progress, scaffolding da riscrivere (vedi INDEX.md) |

## Riferimenti esterni al monorepo

La documentazione del consumer FixCity (`Modules/Fixcity/docs/bmad/fixcity-nativephp-mobile.md`, `Modules/Fixcity/docs/actor-flows.md`)
vive nel progetto FixCity e non e' presente qui: non e' linkata per evitare link rotti.
