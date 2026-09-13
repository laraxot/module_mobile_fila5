# Mobile

Modulo NativePHP Mobile per il ristorante.

## Obiettivo
Permettere al cameriere di prendere ordini dal proprio smartphone/tablet con:
- accesso rapido e autenticazione;
- selezione tavolo/cliente;
- presa ordine anche offline;
- sync automatico quando la connessione ritorna;
- invio ordine in cucina;
- supporto a fotocamera/QR/barcode e notifiche push.

## Dipendenze
- NativePHP Mobile

Mobile espone il port `Modules\\Mobile\\Contracts\\RestaurantGateway` e
consuma esclusivamente i DTO in `Modules\\Mobile\\Data`. L'adapter concreto
è registrato dal modulo applicativo che possiede il dominio ristorante.
Questo mantiene la direzione delle dipendenze: Restaurant può integrare
Mobile, mentre Mobile resta riutilizzabile senza Restaurant.

## Comandi
```bash
php artisan module:enable Mobile
php artisan module:migrate Mobile
php artisan mobile:sync
```

## Documentazione
Vedi `docs/stories/` per le BMAD stories e `docs/wiki/` per l'integrazione tecnica.
# module_mobile_fila5
