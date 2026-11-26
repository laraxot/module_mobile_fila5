# Mobile — NativePHP per applicazioni Laravel

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-%5E8.4-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![NativePHP](https://img.shields.io/badge/NativePHP-mobile-111827.svg)](https://nativephp.com/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Mobile fornisce una base NativePHP riusabile per applicazioni iOS e Android.
Il modulo non conosce il dominio, il prodotto, il tenant o le schermate
dell'applicazione host: integrazioni, navigazione, dati e policy arrivano da
contratti e adapter del progetto che lo installa.

## Responsabilità

- bootstrap e integrazione NativePHP;
- contratti per adapter e servizi mobile;
- convenzioni condivise per shell, asset e build;
- supporto ai flussi autenticati senza duplicare la logica di dominio.

Le feature verticali, le schermate di business, i feed e le policy restano nei
moduli owner dell'applicazione ospitante.

## Installazione

~~~bash
composer require nativephp/mobile
php artisan native:install
php artisan native:debug
~~~

La firma release, i provider push, i certificati e la pubblicazione sugli
store richiedono credenziali e ambienti configurati dall'host.

## Architettura

| Area | Responsabilità |
|---|---|
| app/Actions | operazioni riusabili e orchestrazione |
| app/Contracts | punti di integrazione dell'host |
| app/Models | modelli propri del modulo, quando necessari |
| docs/ | contratti, ADR e guide operative |

## Documentazione e collaborazione

- [documentazione](docs/)
- [issue tracker](https://github.com/laraxot/module_mobile_fila5/issues)
- [pull request](https://github.com/laraxot/module_mobile_fila5/pulls)
- [Actions](https://github.com/laraxot/module_mobile_fila5/actions)
- [Security](https://github.com/laraxot/module_mobile_fila5/security)

Apri una issue con contesto, versione NativePHP, piattaforma e riproduzione
minima. Le modifiche devono includere test o una motivazione verificabile
quando il comportamento dipende dall'ambiente mobile.

## Licenza

Distribuito con licenza [MIT](LICENSE).
