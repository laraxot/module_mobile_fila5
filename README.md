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
- `Modules/Restaurant`
- NativePHP Mobile

## Comandi
```bash
php artisan module:enable Mobile
php artisan module:migrate Mobile
php artisan mobile:sync
```

## Documentazione
Vedi `docs/stories/` per le BMAD stories e `docs/wiki/` per l'integrazione tecnica.
# module_mobile_fila5
