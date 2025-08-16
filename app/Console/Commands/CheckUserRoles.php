<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-user-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Affiche la liste des utilisateurs et leurs rôles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->info('Aucun utilisateur trouvé dans la base de données.');
            return;
        }
        
        $this->info('Liste des utilisateurs et leurs rôles :');
        $this->info('----------------------------------------');
        
        foreach ($users as $user) {
            $this->line(sprintf(
                'ID: %d | Nom: %s | Email: %s | Rôle: %s',
                $user->id,
                $user->name,
                $user->email,
                $user->role ?? 'aucun'
            ));
        }
    }
}
