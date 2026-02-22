<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Wie trage ich meinen Urlaub ein?',
                'answer' => 'Gehen Sie in die Monatsansicht des entsprechenden Monats. Klicken Sie bei den gewünschten Tagen auf den Button <strong>"U"</strong>.<br><br>Der Tag wird grün markiert und als Urlaubstag berechnet (entspricht standardmäßig 8 Arbeitsstunden). Dies wird automatisch von Ihrem jährlichen Urlaubskontingent abgezogen.',
                'order' => 10,
                'is_active' => true,
            ],
            [
                'question' => 'Was bedeutet der "8" Button?',
                'answer' => 'Der "8" Button ist für Tage gedacht, an denen eine pauschale Arbeitszeit von 8 Stunden erfasst werden soll, ohne eine spezifische Baustelle anzugeben.<br><br>Typische Anwendungsfälle sind:<br><ul><li>Berufsschultage</li><li>Fortbildungen</li><li>Übertragung in den nächsten Monat</li></ul>Diese Tage werden in der Übersicht schwarz markiert.',
                'order' => 20,
                'is_active' => true,
            ],
            [
                'question' => 'Wie markiere ich Krankheitstage?',
                'answer' => 'In der Monatsansicht können Sie für einen Tag den Button <strong>"K"</strong> drücken. Der Tag wird rot hervorgehoben und als Krankheitstag (8 Stunden) gewertet.',
                'order' => 30,
                'is_active' => true,
            ],
            [
                'question' => 'Wie kann ich einen fehlerhaften Eintrag löschen oder korrigieren?',
                'answer' => 'In der Monatsansicht finden Sie neben jedem Tag, für den bereits Einträge existieren, ein <strong>Mülleimer-Symbol</strong>.<br><br>Klicken Sie darauf, um alle erfassten Zeiten und Status (Krank, Urlaub etc.) für diesen Tag restlos zu entfernen. Danach können Sie die korrekten Zeiten neu eintragen.',
                'order' => 40,
                'is_active' => true,
            ],
            [
                'question' => 'Wie sehe ich, wie viele Urlaubstage mir noch zustehen?',
                'answer' => 'Ihre verbleibenden Urlaubstage können Sie aus jedem generierten <strong>monatlichen PDF-Bericht</strong> ablesen. Dort wird Ihr jährlicher Anspruch, die bereits genommenen Tage sowie der Resturlaub aufgeführt.',
                'order' => 50,
                'is_active' => true,
            ],
            [
                'question' => 'Ich habe mein Passwort vergessen. Was nun?',
                'answer' => 'Momentan müssen Sie sich an Ihren zuständigen Administrator (häufig den Chef oder Systemverwalter) wenden, damit dieser Ihr Passwort zurücksetzt.<br><br>Wenn Sie noch eingeloggt sind und lediglich ein neues Passwort setzen wollen, können Sie dies jederzeit in der Oberfläche unter <strong>Profil -> Passwort ändern</strong> tun.',
                'order' => 60,
                'is_active' => true,
            ],
            [
                'question' => 'Wie lade ich Dokumente wie Krankmeldungen hoch?',
                'answer' => 'Sobald Ihnen der Administrator eine Dokumenten-Anforderung (z. B. "Bitte Arbeitsunfähigkeitsbescheinigung einreichen") zugewiesen hat, taucht diese in Ihrem <strong>Profil</strong> auf.<br><br>Dort können Sie die entsprechende Datei (PDF, Bild) auswählen und hochladen. Der Status ändert sich dann auf "Erledigt" und der Administrator wird benachrichtigt.',
                'order' => 70,
                'is_active' => true,
            ],
        ];

        DB::table('faqs')->insert($faqs);
    }
}
