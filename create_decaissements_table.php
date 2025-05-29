<?php

// Script pour créer la table decaissements manuellement
// Exécuter avec: php create_decaissements_table.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

try {
    if (!Schema::hasTable('decaissements')) {
        Schema::create('decaissements', function (Blueprint $table) {
            $table->id();
            $table->date('date_paiement');
            $table->decimal('montant', 15, 2);
            $table->string('montant_lettres')->nullable();
            $table->string('motif');
            $table->text('description')->nullable();
            $table->string('beneficiaire');
            $table->text('coordonnees')->nullable();
            $table->string('methode_paiement')->default('espèces');
            $table->string('reference')->nullable();
            $table->string('piece')->nullable();
            $table->text('details_bancaires')->nullable();
            $table->string('projet_rubrique')->nullable();
            $table->boolean('justificatif_present')->default(false);
            $table->text('observations')->nullable();
            $table->enum('status', ['en_attente', 'approuve', 'rejete'])->default('en_attente');
            $table->unsignedBigInteger('created_by');
            $table->string('year');
            $table->unsignedBigInteger('projet_id')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('projet_id')->references('id')->on('projets')->onDelete('set null');
        });
        
        echo "Table 'decaissements' créée avec succès.\n";
    } else {
        echo "La table 'decaissements' existe déjà.\n";
    }
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}