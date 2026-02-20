#!/bin/bash

# ==============================================================================
# Laravel Cronjob & Queue Worker Einrichtung für Ubuntu/Debian VPS
# ==============================================================================
# Dieses Skript richtet den Laravel Scheduler und einen Supervisor-Prozess
# für die Queue-Worker ein, was für den Versand der Erinnerungs-E-Mails
# notwendig ist.

# === BITTE ANPASSEN ===
# Pfad zu deinem Laravel-Projekt auf dem VPS (z.B. /var/www/html/work-time-tracker)
PROJECT_PATH="/pfad/zu/deinem/laravel/projekt"

# Der Benutzer, unter dem der Webserver/PHP läuft (z.B. www-data oder ubuntu)
WEB_USER="www-data"
# ======================

echo "Starte Einrichtung für Laravel-Projekt in $PROJECT_PATH als Benutzer $WEB_USER..."

# 1. Cronjob für den Laravel Scheduler einrichten
echo "1. Richte Cronjob für den Laravel Scheduler ein..."
# Überprüfen, ob der Cronjob bereits existiert
CRON_CMD="* * * * * cd $PROJECT_PATH && php artisan schedule:run >> /dev/null 2>&1"
(crontab -u $WEB_USER -l 2>/dev/null | grep -F "$CRON_CMD") || (crontab -u $WEB_USER -l 2>/dev/null; echo "$CRON_CMD") | crontab -u $WEB_USER -
echo "Cronjob erfolgreich hinzugefügt."

# 2. Supervisor installieren (falls nicht vorhanden)
echo "2. Installiere Supervisor..."
sudo apt-get update
sudo apt-get install -y supervisor

# 3. Supervisor Konfiguration für Laravel Queue Worker erstellen
echo "3. Erstelle Supervisor-Konfigurationsdatei..."
SUPERVISOR_CONF="/etc/supervisor/conf.d/laravel-arbeiter.conf"

sudo tee $SUPERVISOR_CONF > /dev/null <<EOF
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php $PROJECT_PATH/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=$WEB_USER
numprocs=1
redirect_stderr=true
stdout_logfile=$PROJECT_PATH/storage/logs/worker.log
stopwaitsecs=3600
EOF

# 4. Supervisor aktualisieren und Worker starten
echo "4. Lade Supervisor neu und starte den Worker..."
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*

echo ""
echo "=============================================================================="
echo "✔ Einrichtung abgeschlossen!"
echo "Der Cronjob (für 18:00 Uhr) und der Queue-Worker (für E-Mails) laufen nun im Hintergrund."
echo "=============================================================================="
