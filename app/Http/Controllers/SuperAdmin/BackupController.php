<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Helpers\Qs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    protected $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    /**
     * Liste des sauvegardes disponibles et état de la base de données
     */
    public function index()
    {
        $files = File::files($this->backupPath);
        $backups = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'sql') {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => round($file->getSize() / 1024 / 1024, 2) . ' Mo (' . round($file->getSize() / 1024, 0) . ' Ko)',
                    'raw_size' => $file->getSize(),
                    'created_at' => date('d/m/Y H:i:s', $file->getMTime()),
                    'timestamp' => $file->getMTime()
                ];
            }
        }

        // Trier par date décroissante (plus récent d'abord)
        usort($backups, function ($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        // Statistiques de la base locale
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $tablesCount = count($tables);

        $dbSizeQuery = DB::select("
            SELECT table_schema, 
            ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb 
            FROM information_schema.tables 
            WHERE table_schema = ? 
            GROUP BY table_schema
        ", [$dbName]);

        $dbSizeMb = !empty($dbSizeQuery) ? $dbSizeQuery[0]->size_mb : 0;

        return view('pages.super_admin.backups', compact('backups', 'tablesCount', 'dbSizeMb', 'dbName'));
    }

    /**
     * Créer une sauvegarde complète SQL locale en 1 clic
     */
    public function create()
    {
        try {
            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            $fileName = 'backup_CPA_' . date('Y_m_d_H_i_s') . '.sql';
            $filePath = $this->backupPath . '/' . $fileName;

            // Génération du dump SQL complet en PHP pur (100% autonome et compatible hors-ligne)
            $pdo = DB::connection()->getPdo();
            $tables = [];
            $result = $pdo->query('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');

            while ($row = $result->fetch(\PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }

            $sqlDump = "-- --------------------------------------------------------\n";
            $sqlDump .= "-- Sauvegarde Locale CPA (Collège Privé Adventiste)\n";
            $sqlDump .= "-- Date : " . date('d/m/Y H:i:s') . "\n";
            $sqlDump .= "-- Base de données : " . $dbName . "\n";
            $sqlDump .= "-- --------------------------------------------------------\n\n";
            $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $sqlDump .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
            $sqlDump .= "SET time_zone = '+00:00';\n\n";

            foreach ($tables as $table) {
                // Structure de la table
                $createTableQuery = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_NUM);
                $sqlDump .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $sqlDump .= $createTableQuery[1] . ";\n\n";

                // Données de la table
                $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
                if (count($rows) > 0) {
                    $columnNames = array_keys($rows[0]);
                    $fields = implode('`, `', $columnNames);

                    $sqlDump .= "INSERT INTO `{$table}` (`{$fields}`) VALUES\n";
                    $valueRows = [];

                    foreach ($rows as $row) {
                        $values = [];
                        foreach ($row as $val) {
                            if (is_null($val)) {
                                $values[] = 'NULL';
                            } else {
                                $values[] = $pdo->quote($val);
                            }
                        }
                        $valueRows[] = "(" . implode(', ', $values) . ")";
                    }

                    $sqlDump .= implode(",\n", $valueRows) . ";\n\n";
                }
            }

            $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

            File::put($filePath, $sqlDump);

            return back()->with('flash_success', 'Sauvegarde créée avec succès : ' . $fileName);
        } catch (\Exception $e) {
            return back()->with('flash_danger', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    /**
     * Télécharger un fichier de sauvegarde
     */
    public function download($fileName)
    {
        $filePath = $this->backupPath . '/' . basename($fileName);

        if (File::exists($filePath)) {
            return response()->download($filePath);
        }

        return back()->with('flash_danger', 'Fichier introuvable.');
    }

    /**
     * Supprimer une sauvegarde locale
     */
    public function delete($fileName)
    {
        $filePath = $this->backupPath . '/' . basename($fileName);

        if (File::exists($filePath)) {
            File::delete($filePath);
            return back()->with('flash_success', 'Sauvegarde supprimée avec succès.');
        }

        return back()->with('flash_danger', 'Fichier introuvable.');
    }

    /**
     * Nettoyer les caches Laravel pour optimiser le serveur local
     */
    public function cleanCache()
    {
        try {
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            return back()->with('flash_success', 'Caches et fichiers temporaires nettoyés avec succès. Le système est optimisé.');
        } catch (\Exception $e) {
            return back()->with('flash_danger', 'Erreur : ' . $e->getMessage());
        }
    }
}
