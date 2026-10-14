---
title: "00 INDEX: docs modulo Mobile"
type: index
status: active
<<<<<<< .merge_file_qgORqA
updated: 2026-10-14
module: Mobile
tags: [mobile, nativephp, module, documentation]
qmd: "Mobile module NativePHP documentation index"
---

# Mobile - indice canonico

Ingresso unico del `docs/` di `laravel/Modules/Mobile`, secondo il
[contratto docs](../../../../bmad-output/docs-architecture/module-theme-docs-contract.md).
`INDEX.md` era un secondo ingresso: e' `superseded` da questo file.

## Identita' e ownership (verificato 2026-10-14)

- Modulo nwidart `Mobile` (`module.json`: alias `mobile`, richiede `fixcity`, `geo`, `user`), pacchetto `fixcity/mobile`
  che richiede `nativephp/mobile ^4.5` (installato 4.6.0). Repo git separato; working tree del modulo pulito.
- `MobileServiceProvider` estende `XotBaseServiceProvider` e non aggiunge nulla.
- Ruolo dichiarato: shell NativePHP per il front office FixCity. Il web e' Folio + Volt: i tre file in `routes/` restituiscono
  `[]` e `app/Http` non esiste (nessun controller). Le pagine Folio del modulo sono montate dal Cms su `/{locale}`.

## Cosa contiene davvero

| Area | Path | Stato |
|---|---|---|
| Schermate native per le segnalazioni | `app/Native/Screens/` (Map, TicketList, TicketDetail, CreateTicket) e `resources/views/native/screens/` | dati simulati; `CreateTicketScreen` rende la vista `ticket-detail` |
| Pagine Folio | `resources/views/pages/mobile/{index,map}.blade.php` | `/it/mobile` e `/it/mobile/map` danno 404; usano rotte `mobile.map` e `mobile.create` inesistenti |
| Configurazione | `config/mobile.php`, `config/bifrost.php` | `bifrost.php` ha ancora `updater.repo = restaurant-fila5` |
| Dominio cameriere, ordini, KDS | `app/Models` (`OrderQueue`, `WaiterSession`), `app/Actions/Mobile` (6), `app/Notifications` (2), `app/Filament`, `database/migrations/create_mobile_tables.php`, `database/seeders` (5), `app/Contracts/RestaurantGateway.php` | codice proprio del modulo, nessun import da `Modules\Restaurant`; decisione di rimozione aperta |
| Contratto agnostico | `app/Contracts/MobileGatewayContract.php` | namespace `Modules\Mobile\App\Contracts` non conforme al PSR-4: non caricabile |
| Test | `tests/Unit/Data/RestaurantDataTest.php` | 1 test, passa; copre solo i DTO ristorante |
| Qualita' | `phpstan analyse Modules/Mobile` | exit 0 il 2026-10-14 |

Prove di build e runtime (APK debug con SHA-256, log dell'emulatore, comandi `native:*` reali): [wiki/nativephp-build-and-run.md](wiki/nativephp-build-and-run.md).
iOS, push e offline non sono pronti: nessun artefatto.

## Directory

| Directory | Ruolo | Note |
|---|---|---|
| `wiki/` | canonica | regole e prove riusabili; [nativephp-build-and-run](wiki/nativephp-build-and-run.md), [log](wiki/log.md) |
| `stories/` | canonica (cronologia BMAD) | 9 file; le story 1.1, 1.7, 1.9, 1.10 sono del progetto ristorante, superseded da 1.11, da non cancellare ne' rinumerare |
| `decision-log.md` | canonica | decisioni indipendenti del modulo |
| `bmad/` | evidence | [README](bmad/README.md) e [dossier](bmad/bmad-dossier.md) trattano lo stesso tema (dossier BMAD) con testi diversi: nessuno dei due e' ancora il riferimento unico |
| `investigation/` | evidence | [investigation.md](investigation/investigation.md) e' un modello non compilato (Feature A/B/C), titolo diverso dal contenuto |
| `prompts/` | evidence | [01.md](prompts/01.md): richiesta dell'utente che ha portato alla rimozione dei controller |
| [README](README.md) | legacy, `stale` | 11 correzioni verificate in testa; criteri di accettazione, non stato |
| [INDEX](INDEX.md) | legacy, `superseded` | sostituito da questo file |

## Story e riferimenti

- [1.11 boundary del modulo](stories/1.11.mobile-module-boundary.story.md): ownership agnostica;
- [1.12 PHPStan cleanup](stories/1.12.phpstan-level-max-mobile-cleanup.story.md): rimozione dei controller Restaurant (sezione 2026-10-07);
- [1.8 integrazione NativePHP](stories/1.8.nativephp-integration.story.md): storico, non prova di stato attuale;
- [2026-10-14 studio docs e ingressi](stories/2026-10-14-docs-mobile-studio-e-ingressi.story.md);
- consumer FixCity: [contratto NativePHP](../../Fixcity/docs/bmad/fixcity-nativephp-mobile.md) e [flussi attori](../../Fixcity/docs/actor-flows.md).

## Regole

La documentazione NativePHP descrive capability e acceptance criteria; un segno positivo richiede evidence runtime o native.
Prima di creare un `.md`, cercare il canonico con `qmd` o `rg`. I nuovi `.md` usano nomi lowercase senza date e senza suffissi
numerici sequenziali. Le story non si cancellano ne' si rinumerano.
=======
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
>>>>>>> .merge_file_hKz7Pu
