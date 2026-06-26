<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; color: #334155; background-color: #f8fafc; padding: 20px; }
        .card { background: white; padding: 24px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; max-w: 500px; margin: 0 auto; }
        h2 { color: #4f46e5; margin-top: 0; }
        .badge { background: #fef3c7; color: #d97706; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Rappel d'échéance de tâche</h2>
        <p>Bonjour,</p>
        <p>La date limite pour réaliser la tâche suivante approche à grands pas :</p>
        
        <div style="background: #f1f5f9; padding: 15px; border-radius: 8px; margin: 15px 0;">
            <strong>Projet :</strong> {{ $task->project->title }}<br>
            <strong>Tâche :</strong> {{ $task->title }}<br>
            <strong>Priorité :</strong> <span class="badge">{{ strtoupper($task->priority) }}</span><br>
            <strong>Date limite :</strong> {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
        </div>

        <p>Bon courage pour la finalisation de vos objectifs !</p>
        <p style="font-size: 12px; color: #94a3b8;">— L'équipe Trello Simplified</p>
    </div>
</body>
</html>