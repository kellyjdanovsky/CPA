# Module PASCOMA - Documentation

## Aperçu
Le module PASCOMA a été créé pour gérer les attestations d'assurance des élèves inscrits pour l'année scolaire en cours.

## Fonctionnalités

### 1. Tableau PASCOMA
- **URL d'accès** : `/pascoma`
- **Route** : `pascoma.index`
- **Accès** : Menu latéral → Élèves → PASCOMA

### 2. Colonnes du tableau
Le tableau affiche les informations suivantes pour chaque élève :

| Colonne | Description |
|---------|-------------|
| N° | Numéro séquentiel |
| Nom et Prénoms de l'élève | Nom complet de l'élève |
| Date de naissance | Date de naissance |
| Classe | Classe seulement (sans section) |
| N° Attestation d'Assurance | Numéro formaté par sexe (1F, 1G, 2F, 2G, etc.) |
| Sexe | Masculin ou Féminin avec icône |
| Somme payée | Montant fixe de 200 Ar |

### 3. Système de numérotation des attestations
- **Filles** : Numérotées 1F, 2F, 3F, etc.
- **Garçons** : Numérotés 1G, 2G, 3G, etc.
- Les élèves sont triés par sexe (Filles d'abord) puis par nom

### 4. Fonctionnalités supplémentaires
- **Impression** : Bouton pour imprimer le tableau
- **Export Excel** : Export complet vers Excel
- **Export PDF** : Génération PDF via DataTables
- **Statistiques** : Affichage du total filles et garçons
- **DataTables** : Recherche, tri et pagination intégrés

## Fichiers créés

### Contrôleur
- `app/Http/Controllers/SupportTeam/PascomaController.php`

### Vue
- `resources/views/pages/support_team/pascoma/index.blade.php`

### Export
- `app/Exports/PascomaExport.php`

### Routes
Ajouté dans `routes/web.php` :
```php
Route::group(['prefix' => 'pascoma'], function(){
    Route::get('/', 'PascomaController@index')->name('pascoma.index');
    Route::get('export', 'PascomaController@export')->name('pascoma.export');
});
```

### Menu
Ajouté dans `resources/views/partials/menu.blade.php` sous la section Élèves

## Améliorations possibles

1. **Filtrage** : Ajouter des filtres par classe, sexe, etc.
2. **Personnalisation** : Permettre la modification du montant par élève
3. **Historique** : Archiver les attestations par année scolaire
4. **Validation** : Marquer les attestations comme validées/payées
5. **Impression individuelle** : Générer des attestations individuelles à imprimer

## Notes techniques

- Le module utilise le repository `StudentRepo` pour récupérer les données
- Les élèves sont filtrés par session en cours (`current_session`)
- Seuls les élèves non diplômés (`grad = 0`) sont affichés
- La numérotation est calculée dynamiquement à chaque affichage
- Le design est responsive et adapté à l'impression

## Support

Pour toute question ou modification, consultez les fichiers mentionnés ci-dessus.

---
*Documentation créée le : 2025-12-01*
