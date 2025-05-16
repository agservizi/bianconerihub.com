-- Aggiungiamo la colonna is_admin alla tabella users se non esiste
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_admin TINYINT(1) DEFAULT 0;
