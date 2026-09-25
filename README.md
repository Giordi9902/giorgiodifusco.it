# giorgiodifusco.it

Sito personale e piattaforma per la gestione di lezioni private, scritta in PHP puro con un micro-framework MVC custom, nessuna dipendenza Composer.

## Funzionalità

- **Sito pubblico**: homepage, blog con filtri per area/argomento, ricerca, paginazione, sitemap XML e metadati SEO / Open Graph / JSON-LD.
- **Area studente**: lezioni programmate e svolte, pacchetti di ore con minuti residui, materiali didattici, pagamenti e saldo da versare.
- **Area admin (LMS)**: gestione studenti, lezioni (in presenza / online), pacchetti, pagamenti con riconciliazione automatica, materiali (file, link, testo) e note.
- **CMS blog**: editor visuale TinyMCE (HTML ripulito lato server con whitelist; i vecchi articoli in Markdown restano supportati), immagini in evidenza, campi SEO, gestione di aree e argomenti.
- **Insights blog** (dashboard admin): letture giornaliere, articoli più letti, sorgenti di traffico, statistiche per area e controlli SEO/editoriali. Le visite sono contatori aggregati per giorno, senza IP né cookie.

## Stack

- PHP 8.1+ (PDO MySQL), MySQL 8 / MariaDB
- Apache con `mod_rewrite`
- Tailwind CSS, JavaScript

## Struttura

```
app/
  Controllers/   controller HTTP (pagine + API JSON)
  Models/        accesso ai dati via PDO (prepared statements)
  Views/         template PHP
core/            Router, Controller base, Database, loader .env
config/          configurazione letta dall'ambiente
public/          front controller (index.php), asset statici, upload
schemas/         schema SQL
bin/             script CLI
```

Tutte le richieste passano da `public/index.php`, che registra le rotte e delega al `Router`.

## Setup locale

```bash
cp .env.example .env              # compila le credenziali del DB
mysql -u root -p -e "CREATE DATABASE lms CHARACTER SET utf8mb4"
mysql -u root -p lms < schemas/schema.sql
php bin/create-admin.php "Nome Cognome" admin@example.com
```

Le modifiche successive allo schema stanno in `schemas/migrations/` (già incluse in `schema.sql` per le installazioni nuove): sul DB esistente vanno eseguite a mano, una volta, in ordine di data.

Con Apache, la document root è la cartella del progetto (il `.htaccess` in radice instrada tutto verso `public/index.php`). In sviluppo si può usare anche il server integrato:

```bash
php -S localhost:8000 public/index.php
```

## CI/CD

La pipeline GitHub Actions (`.github/workflows/ci-cd.yml`) gira su ogni push e pull request:

1. **Lint**: sintassi di tutti i file PHP su PHP 8.1 e 8.3, e verifica che ogni rotta punti a un metodo esistente (`bin/check-routes.php`).
2. **Smoke test**: avvia il server integrato senza database e controlla codici HTTP di pagine pubbliche, 404 e file che non devono essere esposti.
3. **Scansione segreti** con gitleaks su tutta la history.
4. **Deploy** (solo `main`, dopo che i job precedenti sono passati): sincronizzazione via FTPS dei soli file modificati. `.env` e `public/uploads/` sul server non vengono toccati.

Il deploy si può lanciare anche a mano da *Actions → CI/CD → Run workflow*, con l'opzione *dry run* per vedere cosa verrebbe caricato.

Configurazione in *Settings → Environments → production*:

| Tipo | Nome | Esempio |
|---|---|---|
| Secret | `FTP_SERVER` | host del certificato TLS (su Aruba `ftplnxNN.aruba.it`, non `ftp.<dominio>`) |
| Secret | `FTP_USERNAME` | |
| Secret | `FTP_PASSWORD` | |
| Variable (opzionale) | `FTP_SERVER_DIR` | `./` (default), su Aruba `www.<dominio>/`; deve finire con `/` |
| Variable (opzionale) | `FTP_PROTOCOL` | `ftps` (default) o `ftp` |
| Variable (opzionale) | `FTP_PORT` | `21` (default) |

Il file `.env` di produzione va caricato una sola volta a mano sul server: la pipeline non lo gestisce.

## Sicurezza

- Credenziali solo in `.env` (escluso da git); `.htaccess` nega l'accesso a dotfile e cartelle interne (`app/`, `core/`, `config/`, `schemas/`, `bin/`).
- Query sempre parametrizzate (PDO, `EMULATE_PREPARES` disattivato).
- Token CSRF su tutte le richieste che modificano dati, sessione rigenerata al login, cookie `HttpOnly` + `SameSite=Lax`.
- Password con `password_hash()`.
- Upload con whitelist di estensioni, nomi file randomizzati ed esecuzione di script disabilitata in `public/uploads/`.
- In produzione (`APP_DEBUG=false`) gli errori vengono loggati e all'utente arriva una pagina generica.

Per evitare di committare segreti per errore, attiva l'hook incluso:

```bash
git config core.hooksPath .githooks
```
