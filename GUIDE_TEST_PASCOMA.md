# 🧪 Guide de Test du Module PASCOMA

## ✅ Ce qui a été créé

### Fichiers créés
1. ✅ **Contrôleur** : `app/Http/Controllers/SupportTeam/PascomaController.php`
2. ✅ **Vue** : `resources/views/pages/support_team/pascoma/index.blade.php`
3. ✅ **Export** : `app/Exports/PascomaExport.php`
4. ✅ **Routes** : Ajoutées dans `routes/web.php`
5. ✅ **Menu** : Lien ajouté dans `resources/views/partials/menu.blade.php`

### Fonctionnalités implémentées
- ✅ Affichage du tableau PASCOMA avec tous les élèves
- ✅ Numérotation automatique des attestations (1F, 2F... / 1G, 2G...)
- ✅ Tri par sexe (Filles d'abord) puis par nom
- ✅ Affichage classe uniquement (sans section)
- ✅ Somme fixe de 200 Ar par élève
- ✅ Export Excel
- ✅ Impression
- ✅ DataTables (recherche, tri, pagination)
- ✅ Statistiques (total filles/garçons)

## 🚀 Comment tester

### Étape 1 : Vérifier l'accès au menu
1. Connectez-vous à l'application
2. Dans le menu latéral gauche, cherchez **"Élèves"**
3. Sous "Élèves", vous devriez voir un nouveau lien **"PASCOMA"** avec l'icône 📋

### Étape 2 : Accéder à la page PASCOMA
**Option A : Via le menu**
- Cliquez sur : Menu → Élèves → PASCOMA

**Option B : Via l'URL directe**
```
http://localhost/pascoma
```
ou
```
http://votre-domaine/pascoma
```

### Étape 3 : Vérifier le tableau
Une fois sur la page, vous devriez voir :

✅ **En-tête de la page**
- Titre "PASCOMA - Attestations d'Assurance"
- Badge avec l'année scolaire en cours
- Bouton "Imprimer"
- Bouton "Exporter Excel"

✅ **Statistiques**
- Carte "Total Filles" (rose)
- Carte "Total Garçons" (bleu)

✅ **Tableau avec 7 colonnes**
| N° | Nom | Date naiss. | Classe | N° Attestation | Sexe | Somme |
|----|-----|-------------|--------|----------------|------|-------|

✅ **Numérotation des attestations**
- Les filles doivent avoir : 1F, 2F, 3F, etc. (badge rose)
- Les garçons doivent avoir : 1G, 2G, 3G, etc. (badge bleu)

✅ **Fonctionnalités DataTables**
- Barre de recherche
- Sélecteur de nombre d'éléments par page
- Boutons : Copier, Excel, PDF, Imprimer

### Étape 4 : Tester l'export Excel
1. Cliquez sur le bouton **"Exporter Excel"** en haut à droite
2. Un fichier `PASCOMA_[année].xlsx` devrait se télécharger
3. Ouvrez le fichier et vérifiez :
   - ✅ Toutes les colonnes sont présentes
   - ✅ Les données sont correctes
   - ✅ La numérotation est correcte

### Étape 5 : Tester l'impression
1. Cliquez sur le bouton **"Imprimer"**
2. La fenêtre d'impression du navigateur s'ouvre
3. Vérifiez l'aperçu avant impression :
   - ✅ Les boutons et badges sont masqués
   - ✅ Le tableau est bien formaté
   - ✅ Toutes les données sont visibles

## 🔍 Points de vérification détaillés

### Vérification 1 : Ordre des élèves
- [ ] Les filles apparaissent en premier
- [ ] Ensuite les garçons
- [ ] Chaque groupe est trié par nom alphabétiquement

### Vérification 2 : Numérotation
- [ ] La numérotation des filles commence à 1F
- [ ] La numérotation des garçons commence à 1G
- [ ] Chaque sexe a sa propre séquence indépendante

### Vérification 3 : Colonnes
- [ ] N° : Numéro séquentiel correct
- [ ] Nom : Nom complet de l'élève
- [ ] Date de naissance : Format correct
- [ ] Classe : Affiche uniquement la classe (pas la section)
- [ ] N° Attestation : Badge coloré avec numéro correct
- [ ] Sexe : Icône + texte (Masculin/Féminin)
- [ ] Somme : 200 Ar pour tous

### Vérification 4 : Total
- [ ] Ligne de total en bas du tableau
- [ ] Somme totale = (nombre d'élèves × 200 Ar)

## 🐛 Dépannage

### Problème : Page blanche ou erreur 404
**Solution** :
```bash
cd g:/avara/CPA
php artisan cache:clear
php artisan route:clear
php artisan config:clear
```

### Problème : Menu PASCOMA n'apparaît pas
**Solution** :
1. Vérifiez que vous êtes connecté avec un compte ayant les droits "Team SA" ou "Team SAT"
2. Videz le cache du navigateur (Ctrl + Shift + R)

### Problème : Tableau vide
**Solution** :
1. Vérifiez qu'il y a des élèves inscrits pour l'année scolaire en cours
2. Vérifiez la session active dans les paramètres

### Problème : Export Excel ne fonctionne pas
**Solution** :
1. Vérifiez que le package Maatwebsite/Excel est installé
2. Exécutez : `composer require maatwebsite/excel` si nécessaire

## 📊 Données de test

Si vous voulez tester avec des données, le tableau devrait ressembler à ceci :

| N° | Nom | DoB | Classe | N° Att | Sexe | Somme |
|----|-----|-----|--------|--------|------|-------|
| 1 | ANDRIANAIVO Feno | 2010-05-15 | CE1 | **1F** 💗 | Féminin | 200 Ar |
| 2 | RAKOTO Nirina | 2011-03-22 | CP | **2F** 💗 | Féminin | 200 Ar |
| 3 | RASOAMALALA Voah. | 2010-08-10 | CE1 | **3F** 💗 | Féminin | 200 Ar |
| 4 | ANDRIAMAHEFA Rivo | 2010-01-18 | CE2 | **1G** 💙 | Masculin | 200 Ar |
| 5 | RAKOTOMALALA Hery | 2011-07-25 | CP | **2G** 💙 | Masculin | 200 Ar |
| 6 | RAJAONA Toky | 2010-11-30 | CE1 | **3G** 💙 | Masculin | 200 Ar |

## ✨ Aperçu visuel

Pour voir un aperçu du résultat attendu avant de tester avec de vraies données :
1. Ouvrez le fichier `demo_pascoma.html` dans votre navigateur
2. Cela vous donnera une idée du design et de la mise en page

## 📞 Support

Si vous rencontrez des problèmes :
1. Consultez le fichier `PASCOMA_README.md` pour plus de détails
2. Vérifiez les logs Laravel : `storage/logs/laravel.log`
3. Activez le mode debug dans `.env` : `APP_DEBUG=true`

---
**Date de création** : 2025-12-01  
**Version** : 1.0
