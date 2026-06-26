<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{use AuthorizesRequests;
    // 1. Récupérer toutes les tâches de l'utilisateur connecté
    public function index(Request $request)
    {
        $tasks = Task::whereHas('project', function ($query) use ($request) {
            $query->whereHas('users', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            });
        })->get();

        return response()->json($tasks, 200);
    }

    // 2. Récupérer toutes les tâches d'un projet spécifique
    public function getProjectTasks(Request $request, $projectId)
    {
        // On vérifie d'abord que le projet appartient bien à l'utilisateur connecté
        $project = $request->user()->projects()->findOrFail($projectId);
        
        return response()->json($project->tasks, 200);
    }

    // 3. Créer une tâche dans un projet
    public function store(Request $request, $projectId)
    {
        $project = $request->user()->projects()->findOrFail($projectId);
        $this->authorize('manageTasks', $project); // Bloque les simples 'viewers'

        $fields = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        // Par défaut, une nouvelle tâche créée est "todo"
        $fields['status'] = 'todo';

        $task = $project->tasks()->create($fields);

        return response()->json($task, 201);
    }

    // 4. Mettre à jour le statut ou les infos d'une tâche (Utile pour le Drag & Drop du Kanban)
    public function update(Request $request, $id)
    {
        // CORRECTION : On passe par whereHas('users') car la colonne user_id est dans la table pivot
        $task = Task::whereHas('project', function ($query) use ($request) {
            $query->whereHas('users', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            });
        })->findOrFail($id);

        $fields = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:todo,in_progress,done',
            'priority' => 'sometimes|required|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $task->update($fields);

        return response()->json($task, 200);
    }

    // 5. Supprimer une tâche
    public function destroy(Request $request, $id)
    {
        // CORRECTION : Idem ici pour la suppression sécurisée
        $task = Task::whereHas('project', function ($query) use ($request) {
            $query->whereHas('users', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            });
        })->findOrFail($id);

        $task->delete();

        return response()->json(['message' => 'Tâche supprimée avec succès.'], 200);
    }
}