<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveUniqueConstraintFromUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Recréer les colonnes sans contrainte d'unicité
        Schema::table('users', function (Blueprint $table) {
            // Renommer les colonnes existantes
            $table->renameColumn('nom_m', 'nom_m_old');
            $table->renameColumn('nom_p', 'nom_p_old');
            $table->renameColumn('prof_p', 'prof_p_old');
            $table->renameColumn('prof_m', 'prof_m_old');
        });

        Schema::table('users', function (Blueprint $table) {
            // Créer de nouvelles colonnes sans contrainte d'unicité
            $table->string('nom_m', 100)->nullable();
            $table->string('nom_p', 100)->nullable();
            $table->string('prof_p', 100)->nullable();
            $table->string('prof_m', 100)->nullable();
        });

        // Copier les données des anciennes colonnes vers les nouvelles
        DB::statement('UPDATE users SET nom_m = nom_m_old, nom_p = nom_p_old, prof_p = prof_p_old, prof_m = prof_m_old');

        Schema::table('users', function (Blueprint $table) {
            // Supprimer les anciennes colonnes
            $table->dropColumn('nom_m_old');
            $table->dropColumn('nom_p_old');
            $table->dropColumn('prof_p_old');
            $table->dropColumn('prof_m_old');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Cette méthode n'est pas implémentée car nous ne voulons pas rétablir les contraintes d'unicité
        // Si nécessaire, vous pouvez ajouter le code pour rétablir les contraintes ici
    }
}
