---
title: "Mobile: ownership e stato verificato"
type: index
module: Mobile
status: superseded
superseded_by: 00-INDEX.md
created: 2026-09-27
updated: 2026-10-14
<<<<<<< .merge_file_bUNUv8
tags: [mobile, nativephp, index, legacy]
=======
>>>>>>> .merge_file_JEylsM
qmd: "mobile nativephp agnostic module folio volt documentation index"
tags: [mobile, nativephp, ownership, stato-verificato]
issues:
  - "https://github.com/laraxot/module_mobile_fila5/issues/1"
discussions:
  - "https://github.com/laraxot/module_mobile_fila5/discussions/2"
---

<<<<<<< .merge_file_bUNUv8
> Nota 2026-10-14 (docs-audit): questo file e' stato sostituito da [00-INDEX.md](00-INDEX.md), l'ingresso canonico previsto dal contratto dei docs. Le sezioni verificate sono state integrate li'; due affermazioni qui sotto sono superate: `nativephp/mobile` e' installato in 4.6.0 (non 4.5.2) e "agnostico" significa solo assenza di import da `Modules\Restaurant` (verificato con grep): il modulo contiene ancora modelli, Actions e tabelle proprie del dominio cameriere/ordini/KDS. Contenuto non riscritto.

# Mobile — indice canonico (legacy)
=======
# Mobile: ownership e stato verificato

> Ruolo: dettaglio di ownership, stato verificato e story superseded. L'indice canonico della cartella e' [00-INDEX.md](00-INDEX.md); questo file non e' piu' un ingresso alternativo.
>>>>>>> .merge_file_JEylsM

## Ownership

`Modules/Mobile` è un contenitore NativePHP agnostico. Non contiene controller FO,
rotte web/API proprie, modelli Restaurant o policy FixCity. Il front office web è
Folio + Volt; la business logic vive nelle Actions del modulo owner.

## Stato verificato

- `nativephp/mobile` 4.6.0 installato nel progetto Laravel;
- configurazione package parametrica via env, senza default ristorante;
- shell Android NativePHP generato localmente;
- APK Android release prodotto il 2026-10-14 in `nativephp/android/app/build/outputs/apk/release/app-release.apk`;
- APK verificato con `apksigner verify` (APK Signature Scheme v2), package `com.restaurant.waiter`, SHA-256 `7875881ac87a927375f122be3fbed469fd352e0c8d04d844286d9cb063b1db5b`;
- build riproducibile tramite `build-apk.sh`, con Java 17, Android SDK e keystore esterno;
- runtime Android e installazione su emulatore non verificati in questa sessione: non risultano device/AVD disponibili;
- iOS/push/offline non sono dichiarati pronti senza artefatto e test runtime;
- iOS richiede macOS/Xcode; Linux non produce una build iOS firmata.

## Flussi canonici

| Attore | Superficie | Owner | Stato |
|---|---|---|---|
| Guest | Folio/Volt pubblico | Fixcity + Sixteen | web verificabile; native adapter da decidere |
| Cittadino | Folio/Volt + Actions | Fixcity | web owner; mobile consumer pending |
| Moderatore | policy/capability Fixcity | Fixcity | non abilitato: ruolo da definire |
| PA | Filament web | Fixcity | web owner; native adapter pending |

## Documenti da leggere

- [README del modulo](README.md): contratto e boundary;
- [NativePHP integration story](stories/1.8.nativephp-integration.story.md): storico,
  non prova di stato attuale;
- [boundary story](stories/1.11.mobile-module-boundary.story.md): ownership;
- FixCity NativePHP contract (`Modules/Fixcity/docs/bmad/fixcity-nativephp-mobile.md`, non presente in questo monorepo, vive nel progetto FixCity):
  consumer e gate esterni;
- FixCity actor flows (`Modules/Fixcity/docs/actor-flows.md`, non presente in questo monorepo): journey completo;
- [story 1.12 PHPStan cleanup](stories/1.12.phpstan-level-max-mobile-cleanup.story.md):
  sezione 2026-10-07 con la rimozione dei controller Restaurant, prove e follow-up.

## Story storiche del dominio ristorante (superseded)

Le story 1.1, 1.7, 1.9 e 1.10 descrivono l'app cameriere del progetto ristorante
(`Modules/Restaurant`, che non esiste in questo monorepo). Restano come cronologia, non
come requisiti: il contratto vigente e' quello agnostico di README e story 1.11. Non
cancellarle ne' rinumerarle.

| Story | Stato |
|---|---|
| 1.1 waiter app, 1.7 presa ordini, 1.9 tenant mapping, 1.10 legacy migration | superseded da 1.11 |
| 1.8 NativePHP integration | storico, non prova di stato attuale |
| STORY-MOBILE-INVESTOR-DEMO | scaffolding da riscrivere, non riparare |

I controller `Http/Controllers/Api/MobileController.php` e
`app/Http/Controllers/MobileController.php` sono stati cancellati tre volte e riportati da
merge con snapshot diversi: se ricompaiono dopo un merge, sono un residuo e vanno rimossi
di nuovo (vedi 1.12).

Il test consumer `/it/tickets` raggiunge il runtime ma richiede ancora l'adapter CMS
del componente `page`; il modulo Mobile non lo implementa per preservare il boundary
agnostico.

## Regola di aggiornamento

Prima di creare un `.md`, cercare il canonico con `qmd`/`rg`; aggiornare questo indice
solo quando cambia ownership o stato. I documenti storici restano referenziati dalle
story, ma non sono acceptance evidence senza una verifica attuale.

I nuovi `.md` usano nomi lowercase senza date/timestamp e senza suffissi numerici
sequenziali (`-1`, `-2`, `-3`).
