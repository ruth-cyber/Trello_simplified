<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // 1. Récupérer les statistiques pour le Tableau de Bord
    public function dashboard(Request $request)
    {
        $user = $request->user();

        // Récupération des projets de l'utilisateur connecté
        $totalProjects = $user->projects()->count();

        // Récupération du compte des tâches via la relation
        $tasksQuery = \App\Models\Task::whereIn('project_id', $user->projects()->pluck('id'));
        
        $totalTasks = $tasksQuery->count();
        $todoTasks = (clone $tasksQuery)->where('status', 'todo')->count();
        $inProgressTasks = (clone $tasksQuery)->where('status', 'in_progress')->count();
        $doneTasks = (clone $tasksQuery)->where('status', 'done')->count();

        return response()->json([
            'total_projects' => $totalProjects,
            'total_tasks' => $totalTasks,
            'todo_tasks' => $todoTasks,
            'in_progress_tasks' => $inProgressTasks,
            'done_tasks' => $doneTasks,
        ], 200);
    }

    // 2. Liste des projets
    public function index(Request $request)
    {
$projects = $request->user()->projects;

    return response()->json($projects);    }

    // 3. Créer un projet
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Project::create([
            'title' => $fields['title'],
            'description' => $fields['description'] ?? null,
        ]);

        $request->user()->projects()->attach($project->id, ['role' => 'owner']);

        return response()->json([
            'message' => 'Projet créé avec succès.',
            'project' => $project
        ], 201);
    }

    // 4. Détails d'un projet
 public function show(Request $request, $id)
{
    $project = $request->user()->projects()->findOrFail($id);

    return response()->json($project, 200);
}

    // 5. Modifier un projet
    public function update(Request $request, $id)
    {
        $project = $request->user()->projects()->findOrFail($id);

        $fields = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($fields);

        return response()->json($project, 200);
    }

    // 6. Supprimer un projet (avec contrainte du cahier des charges)
   public function destroy(Request $request, $id)
{
    $project = Project::findOrFail($id);
    $this->authorize('delete', $project); // Bloque si pas owner

    $project->delete();
    return response()->json(['message' => 'Projet supprimé avec succès.']);
}
}