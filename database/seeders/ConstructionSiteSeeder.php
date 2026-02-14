<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConstructionSiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = [
            'Baustelle A',
            'Baustelle B',
            'Büro',
            'Werkstatt',
            'Kunde XYZ',
        ];

        foreach ($sites as $site) {
            \App\Models\ConstructionSite::create([
                'name' => $site,
                'status' => 'active',
            ]);
        }
    }
}
