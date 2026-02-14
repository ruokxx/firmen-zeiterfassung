# Firmen Zeiterfassung

Ein Zeiterfassungssystem für Firmen, entwickelt mit Laravel.

## Installation

Folge diesen Schritten, um das Projekt auf deinem lokalen Rechner zu installieren:

1.  **Repository klonen:**
    ```bash
    git clone https://github.com/ruokxx/firmen-zeiterfassung.git
    cd firmen-zeiterfassung
    ```

2.  **Abhängigkeiten installieren:**
    ```bash
    composer install
    npm install
    npm run build
    ```

3.  **Berechtigungen setzen (WICHTIG):**
    Stelle sicher, dass der Webserver (z.B. `www-data`) Schreibrechte auf die Ordner `storage` und `bootstrap/cache` hat:
    ```bash
    sudo chown -R www-data:www-data /var/www/firmen-zeiterfassung
    sudo chmod -R 775 storage bootstrap/cache
    ```

4.  **Umgebungsvariablen konfigurieren:**
    Kopiere die `.env.example` zu `.env`:
    ```bash
    cp .env.example .env
    ```
    Öffne die `.env` Datei und konfiguriere deine Datenbankverbindung (z.B. SQLite, MySQL).

4.  **App Key generieren:**
    ```bash
    php artisan key:generate
    ```

5.  **Datenbank migrieren und Seeder ausführen:**
    Dies erstellt die Tabellen und den Standard-Admin-Account.
    ```bash
    php artisan migrate --seed
    ```

### Erster Login

Nach der Installation steht ein Standard-Admin-Account zur Verfügung:

*   **E-Mail:** `admin@admin.de`
*   **Passwort:** `password`

**Wichtig:** Beim ersten Login wirst du aufgefordert, die E-Mail-Adresse und das Passwort aus Sicherheitsgründen zu ändern.

---

## Server starten (Port 1234)

Um den Server standardmäßig auf Port **1234** zu starten, verwende folgenden Befehl:

```bash
php artisan serve --port=1234
```

Die Anwendung ist dann unter `http://localhost:1234` erreichbar.

---

## SSL / HTTPS aktivieren

### Lokale Entwicklung (Windows/Mac/Linux)

Für eine einfache lokale HTTPS-Entwicklung wird empfohlen, Laravel Valet (Mac) oder Laragon (Windows) zu verwenden, die SSL automatisch verwalten.

Wenn du `php artisan serve` nutzt, kannst du kein echtes SSL-Zertifikat direkt einbinden. Du kannst jedoch einen lokalen Proxy wie **Ngrok** oder **Expose** nutzen:

1.  Starte den Server:
    ```bash
    php artisan serve --port=1234
    ```
2.  Starte Ngrok (falls installiert):
    ```bash
    ngrok http 1234
    ```
    Ngrok gibt dir eine sichere `https://...` URL.

### Produktion (Nginx / Apache)

Auf einem Produktionsserver (z.B. Ubuntu mit Nginx) solltest du **Let's Encrypt** verwenden.

**Beispiel Nginx Konfiguration:**

1.  Installiere Certbot:
    ```bash
    sudo apt install certbot python3-certbot-nginx
    ```

2.  Erstelle eine Nginx Konfiguration für deine Domain (z.B. `zeiterfassung.deine-firma.de`) und leite Port 80/443 auf deinen PHP-FPM Socket oder Proxy weiter.

3.  Zertifikat anfordern:
    ```bash
    sudo certbot --nginx -d zeiterfassung.deine-firma.de
    ```

4.  Certbot konfiguriert Nginx automatisch für SSL.

---

## Funktionen

*   **Zeiterfassung:** Mitarbeiter können ihre Arbeitszeiten erfassen.
*   **Monatsberichte:** PDF-Export der Arbeitszeiten.
*   **Admin-Panel:** Verwaltung von Mitarbeitern und Freigabe von neuen Accounts.
*   **Freigabeprozess:** Neue Registrierungen müssen vom Admin freigeschaltet werden.
*   **Mehrsprachigkeit:** Profil und Interface auf Deutsch und Englisch verfügbar.

## Fehlerbehebung (Troubleshooting)

### Fehler: "Permission denied" (storage/logs/...)
Wenn du einen Fehler erhältst, dass Logs nicht geschrieben werden können:
```bash
sudo chown -R www-data:www-data /var/www/firmen-zeiterfassung
sudo chmod -R 775 storage bootstrap/cache
```

### Fehler: "Database Access denied" (SQL 1044/1045)
Wenn der Fehler `Access denied for user ...` erscheint:

1.  **Prüfe die `.env` Datei:**
    Stelle sicher, dass `DB_DATABASE`, `DB_USERNAME` und `DB_PASSWORD` korrekt sind.

2.  **Caches leeren:**
    Nach Änderungen an der `.env` Datei *immer* den Cache leeren:
    ```bash
    php artisan optimize:clear
    ```

3.  **Datenbank-Rechte prüfen:**
    Logge dich in MySQL ein und prüfe die Rechte:
    ```sql
    mysql -u root -p
    -- In MySQL:
    GRANT ALL PRIVILEGES ON firma.* TO 'smartscope_user'@'localhost';
    FLUSH PRIVILEGES;
    ```
    (Ersetze `firma` und `smartscope_user` mit deinen Werten).

### Fehler: 502 Bad Gateway
Dies bedeutet meist, dass Nginx nicht mit PHP kommunizieren kann.
Prüfe in der Nginx-Config (`/etc/nginx/sites-available/firmen-zeiterfassung`), ob die PHP-Version stimmt:
`fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;` (oder php8.1, php8.3 usw.)

### Fehler: "Unknown database" (SQL 1049)
Die Datenbank, die in der `.env` Datei unter `DB_DATABASE` angegeben ist (z.B. `firma`), existiert nicht.

**Lösung:**
1.  Logge dich in MySQL ein:
    ```bash
    mysql -u root -p
    ```
2.  Erstelle die Datenbank:
    ```sql
    CREATE DATABASE firma;
    EXIT;
    ```
    (Ersetze `firma` mit dem Namen aus deiner `.env` Datei).

### Fehler: "fatal: detected dubious ownership" (Git)
Wenn Git sich beschwert, dass der Ordner einem anderen User gehört:
```bash
git config --global --add safe.directory /var/www/firmen-zeiterfassung
```
Danach kannst du `git pull` ausführen.

