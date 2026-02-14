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

3.  **Umgebungsvariablen konfigurieren:**
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
