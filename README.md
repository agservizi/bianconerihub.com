# BiancoNeriHub

Social network dedicato ai tifosi della Juventus

## Requisiti di sistema

- PHP 7.4 o superiore
- MySQL 5.7 o superiore
- Estensione PHP MySQLi
- Estensione PHP GD (per la gestione delle immagini)

## Installazione

1. Clona il repository nella tua directory web
2. Crea un database MySQL
3. Copia il file `.env.example` in `.env`
4. Modifica il file `.env` con le tue credenziali del database e altre configurazioni
5. Importa il file di database iniziale `database.sql` (se disponibile)
6. Assicurati che le directory `uploads/profile_pics` e `uploads/posts` siano scrivibili dal server web

### Configurazione dell'ambiente

Il file `.env` contiene tutte le configurazioni sensibili dell'applicazione:

```
DB_HOST=localhost       # Host del database
DB_USER=root            # Nome utente del database
DB_PASS=password        # Password del database
DB_NAME=bianconerihub   # Nome del database

APP_ENV=development     # Ambiente (development o production)
```

## Utilizzo in sviluppo

Per avviare un server di sviluppo locale:

```
php -S localhost:8000
```

## Crediti

BiancoNeriHub © 2023-2025 - Tutti i diritti riservati
