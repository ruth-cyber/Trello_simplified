<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Vérifie si l'utilisateur a accès au projet (owner, member ou viewer).
     */
    private function getProjectRole(User $user, Project $project): ?string
    {
        $member = $project->users()->where('user_id', $user->id)->first();
        return $member ? $member->pivot->role : null;
    }

    public function view(User $user, Project $project): bool
    {
        // Tout membre associé (owner, member, viewer) peut voir le projet
        return !is_null($this->getProjectRole($user, $project));
    }

    public function update(User $user, Project $project): bool
    {
        // Seul le Chef de Projet (owner) peut modifier les infos du projet
        return $this->getProjectRole($user, $project) === 'owner';
    }

    public function delete(User $user, Project $project): bool
    {
        // Seul le Chef de Projet (owner) peut supprimer le projet
        return $this->getProjectRole($user, $project) === 'owner';
    }

    public function manageTasks(User $user, Project $project): bool
    {
        // Seuls les owners et members peuvent manipuler les tâches (pas les viewers)
        return in_array($this->getProjectRole($user, $project), ['owner', 'member']);
    }
}