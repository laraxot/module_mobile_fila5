---
title: "Mobile module documentation index"
type: index
module: Mobile
created: 2026-09-27
updated: 2026-10-07
qmd: "mobile nativephp agnostic module folio volt documentation index"
---

# Mobile — indice canonico

## Ownership

`Modules/Mobile` è un contenitore NativePHP agnostico. Non contiene controller FO,
rotte web/API proprie, modelli Restaurant o policy FixCity. Il front office web è
Folio + Volt; la business logic vive nelle Actions del modulo owner.

## Stato verificato

- `nativephp/mobile` 4.5.2 installato nel progetto Laravel;
- configurazione package parametrica via env, senza default ristorante;
- shell Android NativePHP generato localmente;
- APK Android debug prodotto in `nativephp/android/app/build/outputs/apk/debug/app-debug.apk`;
- runtime Android verificato: bundle estratto, persistent PHP avviato, Activity
  NativePHP fully drawn, `/it/tickets` HTTP 200, Vite CSS/JS HTTP 200 e logo
  Design-Comuni HTTP 200;
- APK debug v24 installato nell’emulatore Android API 36; SHA-256
  `3b9cd7efc35a3f30c0f27c8a9d84baac9eccb77828ff28e6d603831eea3b32a0`;
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
- [FixCity NativePHP contract](../../Fixcity/docs/bmad/fixcity-nativephp-mobile.md):
  consumer e gate esterni;
- [FixCity actor flows](../../Fixcity/docs/actor-flows.md): journey completo;
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
