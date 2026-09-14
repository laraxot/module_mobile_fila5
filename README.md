# 📱 Mobile

> **Esperienza mobile per operatori e camerieri.**

App NativePHP per ordini offline, sync e comunicazione con Restaurant.

## Cosa offre

- **sessioni**
- **ordini offline**
- **QR e notifiche**
- **contratti Restaurant**

## Confini architetturali

Questo modulo possiede le responsabilità elencate sopra e pubblica contratti riusabili agli altri moduli. La logica applicativa vive in Actions del modulo; l’interfaccia amministrativa segue le basi Laraxot/XotBase. Le dipendenze verso altri moduli devono restare esplicite e orientate verso contratti stabili.

## Integrazione rapida

Il modulo è caricato dall’architettura modulare Laraxot. Per verificarne lo stato:

````bash
cd laravel
php artisan module:list
./vendor/bin/phpstan analyse Modules/Mobile
````

Per i test e le convenzioni operative, consultare la documentazione locale prima di introdurre nuove integrazioni.

## Documentazione

La mappa tecnica è in [docs/](./docs/).

- [Story BMAD del modulo](./docs/stories/)
- [Regole del progetto](../../../docs/wiki/)
- [README del progetto](../../README.md)

## Qualità e manutenzione

Le modifiche devono mantenere `declare(strict_types=1);` nel codice PHP, rispettare PHPStan configurato dal progetto e aggiornare la documentazione tecnica quando cambiano contratti, dipendenze o flussi. Le story BMAD restano accanto al codice del modulo per conservare ownership e contesto.

---

**Modulo** `mobile` · **Laraxot** · **FixCity Platform**
