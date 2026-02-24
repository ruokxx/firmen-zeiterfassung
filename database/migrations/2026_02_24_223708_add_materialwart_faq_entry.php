<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('faqs')->insert([
            'question' => 'Was ist ein Materialwart und welche Aufgaben hat diese Rolle?',
            'answer' => 'Der <strong>Materialwart</strong> ist berechtigt, den Materialbestand (Lager) des Unternehmens zu verwalten. 

<ul>
<li><strong>Materialverwaltung:</strong> Im neuen Admin-Tab "Materialverwaltung" können Materialwarte den Katalog pflegen, neue Artikel anlegen und löschen, sowie E-Mail Warnschwellen definieren.</li>
<li><strong>Bestandskorrektur:</strong> Materialwarte können Artikel "Einbuchen" oder "Entnehmen", wodurch die Bestandslogik korrigiert wird und entsprechende Transaktionsstatistiken generiert werden.</li>
<li><strong>Zuteilung:</strong> Die Rolle des Materialwarts kann nur von einem Chef oder Administrator über die Nutzerverwaltung vergeben werden. Wenn Sie diese Rechte benötigen, wenden Sie sich bitte an Ihren Vorgesetzten.</li>
</ul>

Diese Rechte existieren parallel zu Ihrer regulären Aufgabe (z.B. als Geselle).',
            'is_active' => true,
            'order' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('faqs')->where('question', 'Was ist ein Materialwart und welche Aufgaben hat diese Rolle?')->delete();
    }
};
