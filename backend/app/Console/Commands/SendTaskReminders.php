<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Mail\TaskReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendTaskReminders extends Command
{
    // Le nom de la commande à taper dans le terminal
    protected $signature = 'tasks:send-reminders';
    protected $description = 'Envoie un e-mail pour les tâches dont la date d\'échéance approche à moins de 48h';

    public function handle()
    {
        $maintenant = Carbon::now();
        $dansDeuxJours = Carbon::now()->addDays(2);

        // 1. On cherche les tâches pas finies arrivant à échéance dans les prochaines 48h
        $tasksProches = Task::where('status', '!=', 'done')
            ->whereBetween('due_date', [$maintenant, $dansDeuxJours])
            ->with(['project.users']) // On récupère le projet et ses membres
            ->get();

        if ($tasksProches->isEmpty()) {
            $this->info('Aucune tâche à échéance proche aujourd\'hui.');
            return;
        }

        foreach ($tasksProches as $task) {
            // 2. On boucle sur tous les membres du projet (Owners et Members) pour les notifier par e-mail
            foreach ($task->project->users as $user) {
                Mail::to($user->email)->queue(new TaskReminderMail($task));
            }
        }

        $this->info($tasksProches->count() . ' rappels de tâches envoyés avec succès.');
    }
}