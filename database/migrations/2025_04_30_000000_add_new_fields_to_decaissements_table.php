<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToDecaissementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('decaissements', function (Blueprint $table) {
            // Ajout des nouveaux champs pour le système amélioré de gestion des décaissements
            if (!Schema::hasColumn('decaissements', 'montant_lettres')) {
                $table->string('montant_lettres')->nullable()->after('montant');
            }
            
            if (!Schema::hasColumn('decaissements', 'coordonnees')) {
                $table->text('coordonnees')->nullable()->after('beneficiaire');
            }
            
            if (!Schema::hasColumn('decaissements', 'projet_rubrique')) {
                $table->string('projet_rubrique')->nullable()->after('reference');
            }
            
            if (!Schema::hasColumn('decaissements', 'justificatif_present')) {
                $table->boolean('justificatif_present')->default(false)->after('piece');
            }
            
            if (!Schema::hasColumn('decaissements', 'observations')) {
                $table->text('observations')->nullable()->after('details_bancaires');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('decaissements', function (Blueprint $table) {
            // Suppression des champs ajoutés
            $columns = [
                'montant_lettres',
                'coordonnees',
                'projet_rubrique',
                'justificatif_present',
                'observations'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('decaissements', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}