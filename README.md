---
id: module-mobile-readme
title: Mobile — Esperienza Mobile per Operatori e Camerieri
category: module-documentation
status: improved
document_type: bmad_readme
updated_at: '2026-09-13'
tags: [mobile, bmad, second-brain, phpstan-10, filament-5]
language: it-IT
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
