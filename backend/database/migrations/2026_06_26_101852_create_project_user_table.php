<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_user', function (Blueprint $table) {
            $table->id();
            
            // Clés étrangères liées avec suppression en cascade
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Le rôle de l'utilisateur au sein de CE projet spécifique
            $table->enum('role', ['owner', 'member', 'viewer'])->default('member');
            
            $table->timestamps();

            // Index unique pour éviter qu'un utilisateur soit ajouté 2 fois au même projet
            $table->unique(['project_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_user');
    }
};