# ✅ PASCOMA - Structure Finale (Version 3)

## 🎯 Modifications appliquées

Le tableau a été simplifié pour revenir à une **colonne unique "Classe"**, tout en conservant le tri et la numérotation demandés.

### 1. Structure des colonnes (7 colonnes)

| Colonne | Contenu |
|---------|---------|
| **N°** | Numéro séquentiel |
| **Nom et Prénoms** | Nom complet de l'élève |
| **Date de naissance** | Format JJ/MM/AAAA |
| **Classe** | Nom de la classe (MS, GS, etc.) |
| **N° Attestation** | Format 1F, 2F... / 1G, 2G... |
| **Sexe** | F ou G |
| **Somme** | 200 Ar |

### 2. Logique de Tri

Les élèves sont triés :
1. **Par Classe** : MS → GS → CP1 → CP2 → CE → CM1 → CM2 → 6ème → 5ème → 4ème → 3ème
2. **Par Nom** : Ordre alphabétique A-Z (sans distinction de sexe)

### 3. Numérotation Attestation

La numérotation suit l'ordre de la liste triée :
- 1F, 2F, 3F... pour les filles
- 1G, 2G, 3G... pour les garçons

### 4. Affichage Classe

La colonne "Classe" affiche **uniquement le nom de la classe** (ex: "MS"), sans la section (ex: pas de "MS A").

## 📊 Exemple de résultat

| N° | Nom | DoB | Classe | N° Att | Sexe | Somme |
|----|-----|-----|--------|--------|------|-------|
| 1 | ANDRIA Lisa | 12/05/15 | **MS** | 1F | F | 200 Ar |
| 2 | RAJAONA Toky | 15/03/15 | **MS** | 1G | G | 200 Ar |
| 3 | RAKOTO Mialy | 10/08/15 | **MS** | 2F | F | 200 Ar |
| 4 | ANDRIAM Rivo | 18/07/14 | **GS** | 2G | G | 200 Ar |

## 📂 Fichiers mis à jour

1. **`resources/views/pages/support_team/pascoma/index.blade.php`**
   - Retour à une colonne `<th>Classe</th>` unique.
   - Affichage de `$class_only` dans la cellule.

2. **`app/Exports/PascomaExport.php`**
   - Retour à une colonne `Classe` unique.
   - Suppression des colonnes MS, GS, etc.

---
**Date de modification** : 2025-12-01  
**Version** : 3.0 - Colonne Classe unique
