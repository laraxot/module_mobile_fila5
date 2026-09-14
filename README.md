---
id: module-mobile-readme
title: "Mobile — Esperienza Mobile per Operatori e Camerieri"
type: module-readme
category: module-documentation
module: Mobile
status: active
tags: [mobile, nativephp, offline, sync, restaurant]
created: 2026-09-14
updated: 2026-09-14
qmd: "mobile nativephp offline orders synchronization qr module documentation"
issues:
  - "https://github.com/laraxot/module_mobile_fila5/issues/1"
discussions:
  - "https://github.com/laraxot/module_mobile_fila5/discussions/2"
related:
  - "./docs/"
sources: []
---

# 📱 Mobile

> **Esperienza mobile per operatori e camerieri.**

App NativePHP per ordini offline, sync e comunicazione con Restaurant.

## Cosa offre

- **Sessioni** – gestione dello stato dell'operatore
- **Ordini offline** – completamento senza connessione
- **QR e notifiche** – alert in tempo reale
- **Contratti Restaurant** – integrazione con il modulo principale

## Confini architetturali

This module publishes contracts usable by other modules. Logic lives in `Actions`; admin UI follows Laraxot/XotBase.

## Integrazione rapida

```bash
cd laravel
php artisan module:list
./vendor/bin/phpstan analyse Modules/Mobile
```

See local docs for integration patterns.

## Documentazione

The technical map is in [docs/README.md](./docs/README.md).

- [Story BMAD del modulo](./docs/stories/)
- [Regole del progetto](../../../docs/wiki/)
- [README del progetto](../../README.md)

## Qualità e manutenzione

Keep `declare(strict_types=1);` in PHP, respect project PHPStan config, and update docs when contracts evolve.

---

**Modulo** `mobile` · **Laraxot ecosystem** · **Project-agnostic**
