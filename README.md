# ⏱️ Firmen Zeiterfassung

🚀 Ein modernes, sicheres und benutzerfreundliches Zeiterfassungssystem für Firmen, entwickelt mit **Laravel** und **Tailwind CSS**. 

Perfekt geeignet für kleine bis mittelständische Unternehmen, um Arbeitszeiten, Pausen, Urlaub und Krankheitstage übersichtlich zu verwalten. 

---

## ✨ Features im Überblick

### 📝 Zeiterfassung & Berichte
*   **Arbeitszeiterfassung:** Einfaches Starten und Stoppen der Arbeitszeit, inkl. Pausenerfassung.
*   **Monatsübersicht:** Detaillierte Ansicht der Arbeitsstunden pro Monat mit automatischer Berechnung der Soll/Ist-Wochenarbeitszeit.
*   **PDF-Export:** Generierung von professionellen PDF-Berichten (auf Wunsch mit Firmenlogo).
*   **Berichtversand:** Mitarbeiter können ihre Monatsberichte direkt per E-Mail an den Vorgesetzten senden.

### 👥 Benutzer & Team (Admin)
*   **Dashboard & Statistiken:** Übersicht über alle Mitarbeiter, Urlaubstage und die Jahresübersicht.
*   **Freigabeprozess:** Neue Registrierungen müssen durch einen Admin genehmigt werden (inklusive E-Mail-News).
*   **Rollenverwaltung:** Chef, Admin, Geselle, Azubi, Mitarbeiter – mit jeweils angepassten Funktionen.
*   **Urlaubsverwaltung:** Automatische Berechnung von Resturlaub und Krankentagen.

### 📄 Dokumentenverwaltung & Material
*   **Dokumente verteilen:** Admins können Dateien gezielt an bestimmte Mitarbeiter ausspielen.
*   **Status-Tracking:** Rückmeldungen anfordern (Mitarbeiter können z.B. unterschriebene Dokumente hochladen). 
*   **Materialbestellungen:** Ein übersichtliches System für Teammitglieder, fehlendes Material auf Baustellen direkt per Klick zu ordern.

### ⚙️ Konfigurierbar & Modern
*   **Einstellungen:** Dashboard-Nachrichten, Standard-Arbeitszeiten, Webhooks für Discord und eigene Logos direkt im Admin-Panel einstellbar.
*   **Responsive:** Optimiert für alle Endgeräte (Smartphone, Tablet, PC).
*   **Theme:** Modernes "Industrial" Dark-Theme (Grau/Orange/Blau).

---

## 🛠️ Installation: Windows (Lokale Entwicklung mit Laragon)

Für die lokale Entwicklung auf Windows wird [Laragon](https://laragon.org/download/) wärmstens empfohlen, da es PHP, MySQL, Composer und Node.js optimal vereint.

1. **Voraussetzungen:** Installiere Laragon (Full) und starte die Dienste ("Start All").
2. **Terminal öffnen:** Öffne das Laragon Terminal.
3. **Repository klonen:**
   ```bash
   cd C:\laragon\www
   git clone https://github.com/ruokxx/firmen-zeiterfassung.git
   cd firmen-zeiterfassung
   ```
4. **Abhängigkeiten installieren:**
   ```bash
   composer install
   npm install
   npm run build
   ```
5. **Umgebung konfigurieren:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
6. **Datenbank verbinden:**
   Öffne Laragon -> Database (HeidiSQL) und erstelle eine neue Datenbank z.B. namens `zeiterfassung`.
   Öffne die Datei `.env` in deinem Code-Editor und passe die Datenbankzugangsdaten an:
   ```env
   DB_DATABASE=zeiterfassung
   DB_USERNAME=root
   DB_PASSWORD=
   ```
7. **Datenbank migrieren und Standard-Account erstellen:**
   ```bash
   php artisan migrate --seed
   ```
8. **Fertig!**
   Die App ist nun in Laragon erreichbar unter: `http://firmen-zeiterfassung.test` (sofern Laragon Auto-Virtual-Hosts an hat) oder über das Terminal mit `php artisan serve`.

---

## 🌍 Installation: Ubuntu Server (Produktion)

Folgende Schritte begleiten dich durch die Installation auf einem echten Linux Server (z.B. Ubuntu 22.04 oder 24.04). Vorausgesetzt wird ein installierter **LEMP-Stack** (Linux, Nginx, MySQL/MariaDB, PHP > 8.1) und `composer` sowie `npm`.

### 1. Repository klonen
```bash
cd /var/www/
sudo git clone https://github.com/ruokxx/firmen-zeiterfassung.git
cd firmen-zeiterfassung
```

### 2. Abhängigkeiten installieren
```bash
sudo composer install --optimize-autoloader --no-dev
sudo npm install
sudo npm run build
```

### 3. Dateirechte setzen (WICHTIG!)
Der Webserver (meist `www-data` bei Nginx/Apache) braucht unbedingt Lese- und Schreibrechte auf wichtige Ordner:
```bash
sudo chown -R www-data:www-data /var/www/firmen-zeiterfassung
sudo chmod -R 775 storage bootstrap/cache
```

### 4. Umgebung konfigurieren (.env)
```bash
cp .env.example .env
php artisan key:generate
```
Passe in der `.env` Datei deine Datenbankeinstellungen und die App-URL an:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://deine-domain.de

DB_DATABASE=deine_datenbank
DB_USERNAME=dein_db_user
DB_PASSWORD=dein_db_passwort
```

### 5. Datenbank migrieren
```bash
php artisan migrate --seed --force
```
*(Das `--seed` erstellt den Standard-Admin-Account, siehe unten)*

### 6. Caches generieren (für maximale Performance)
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. Nginx Konfiguration (Beispiel)
Damit Nginx das Laravel Projekt lädt, erstelle eine Config: `sudo nano /etc/nginx/sites-available/zeiterfassung`
```nginx
server {
    listen 80;
    server_name deine-domain.de;
    root /var/www/firmen-zeiterfassung/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; # Passe die PHP Version an!
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
Anschließend Seite aktivieren und Nginx neu laden:
```bash
sudo ln -s /etc/nginx/sites-available/zeiterfassung /etc/nginx/sites-enabled/
sudo systemctl reload nginx
```
*(Empfehlung: Installiere ein SSL Zertifikat via `sudo certbot --nginx`)*

---

## 🔒 Erster Login (Standard-Zugang)

Nach erfolgreicher Migration (`migrate --seed`) wurde ein Standard-Account angelegt. Mit diesem kannst du dich initial einloggen und andere Kollegen verwalten.

*   **E-Mail:** `admin@admin.de`
*   **Passwort:** `password`

**Wichtig:** Beim ersten Login wirst du aus Sicherheitsgründen *zwingend* dazu aufgefordert, die E-Mail-Adresse und das Passwort sofort in deine eigenen Daten zu ändern!

---

## 💡 Fehlerbehebung / Troubleshooting

*   **Fehler 500 oder weiße Seite nach Installation:** 
    Höchstwahrscheinlich stimmen die Schreibrechte nicht. Siehe Installation Ubuntu Schritt 3 (`chown` / `chmod` für `storage` Ordner).
*   **CSS / Design fehlt (alles weiß/blau ohne Formatierung):**
    Die Vite Builds fehlen. Führe im Projektordner `npm run build` aus.
*   **"SQLSTATE[HY000] [1045] Access denied for user":**
    Falsche Datenbank-Zugangsdaten in der `.env`. Korrigiere sie und leere den Cache mit `php artisan optimize:clear`.

❤️ Entwickelt für maximale Übersicht und Fairness im Handwerk & Büro.

