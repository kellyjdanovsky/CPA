<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingFieldsToDecaissementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Créer la table si elle n'existe pas
        if (!Schema::hasTable('decaissements')) {
            Schema::create('decaissements', function (Blueprint $table) {
                $table->id();
                $table->string('beneficiaire')->nullable();
                $table->decimal('montant', 15, 2)->nullable();
                $table->string('motif')->nullable();
                $table->enum('mode_paiement', ['Espèces', 'Virement', 'Chèque', 'Mobile Money'])->default('Espèces');
                $table->string('reference')->nullable();
                $table->date('date_paiement')->nullable();
                $table->string('justificatif')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('year')->nullable();
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users');
            });
            return;
        }

        // Ajouter les colonnes manquantes si la table existe déjà
        Schema::table('decaissements', function (Blueprint $table) {
            if (!Schema::hasColumn('decaissements', 'coordonnees')) {
                $table->text('coordonnees')->nullable()->after('beneficiaire');
            }

            if (!Schema::hasColumn('decaissements', 'details_bancaires')) {
                $table->text('details_bancaires')->nullable()->after('coordonnees');
            }

            if (!Schema::hasColumn('decaissements', 'montant_lettres')) {
                $table->string('montant_lettres')->nullable()->after('montant');
            }

            if (!Schema::hasColumn('decaissements', 'projet_rubrique')) {
                $table->string('projet_rubrique')->nullable()->after('motif');
            }

            if (!Schema::hasColumn('decaissements', 'reference')) {
                $table->string('reference')->nullable()->after('mode_paiement');
            }

            if (!Schema::hasColumn('decaissements', 'date_paiement')) {
                $table->date('date_paiement')->nullable()->after('reference');
            }

            if (!Schema::hasColumn('decaissements', 'justificatif')) {
                $table->string('justificatif')->nullable()->after('date_paiement');
            }

            if (!Schema::hasColumn('decaissements', 'observations')) {
                $table->text('observations')->nullable()->after('justificatif');
            }

            // Ajouter la colonne mode_paiement si elle n'existe pas
            if (!Schema::hasColumn('decaissements', 'mode_paiement')) {
                $table->enum('mode_paiement', ['Espèces', 'Virement', 'Chèque', 'Mobile Money'])->default('Espèces')->after('motif');
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
        // Si la table n'existe pas, ne rien faire
        if (!Schema::hasTable('decaissements')) {
            return;
        }

        Schema::table('decaissements', function (Blueprint $table) {
            $columns = [
                'coordonnees',
                'details_bancaires',
                'montant_lettres',
                'projet_rubrique',
                'reference',
                'date_paiement',
                'justificatif',
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
