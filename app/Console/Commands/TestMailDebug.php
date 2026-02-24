<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestMailDebug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-mail-debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $user = \App\Models\User::first();
            $mail = new \App\Mail\MaterialReminderMail($user);
            $rendered = $mail->render();
            $this->info("Render success!");
        }
        catch (\Exception $e) {
            $this->error("ERROR: " . $e->getMessage());
            $this->error("FILE: " . $e->getFile() . " LINE: " . $e->getLine());
            $trace = $e->getTrace();
            for ($i = 0; $i < min(5, count($trace)); $i++) {
                $f = $trace[$i];
                $this->error("#$i " . ($f['class'] ?? '') . ($f['type'] ?? '') . ($f['function'] ?? '') . " in " . ($f['file'] ?? 'N/A') . ":" . ($f['line'] ?? 'N/A'));
            }
        }
    }
}
