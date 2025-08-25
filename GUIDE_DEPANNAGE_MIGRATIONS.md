# Guide de Dépannage - Migrations du Système de Protection contre les Doublons

## ❌ Problème Rencontré

```
SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '115-2024-2025' for key 'student_records.unique_student_session'
```

## 🔧 Solutions

### Solution 1: Migration Sécurisée (Recommandée)

1. **Sauvegardez votre base de données** :
   ```bash
   mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
   ```

2. **Utilisez le script de migration sécurisée** :
   ```bash
   php migrate_duplicate_prevention.php
   ```

3. **Ou exécutez manuellement dans l'ordre** :
   ```bash
   # Étape 1: Nettoyage des doublons existants
   php artisan migrate --path=database/migrations/2025_08_25_000000_cleanup_existing_duplicates_before_constraints.php
   
   # Étape 2: Ajout des contraintes uniques
   php artisan migrate --path=database/migrations/2025_08_25_000001_add_unique_constraints_for_duplicate_prevention.php
   
   # Étape 3: Ajout des colonnes UUID
   php artisan migrate --path=database/migrations/2025_08_25_000002_add_uuid_columns_for_idempotency.php
   ```

### Solution 2: Rollback et Réessai

Si les migrations ont partiellement échoué :

```bash
# Annuler les migrations problématiques
php artisan migrate:rollback --step=1

# Vérifier l'état
php artisan migrate:status

# Réessayer avec la solution 1
```

### Solution 3: Nettoyage Manuel des Doublons

Si vous préférez nettoyer manuellement :

```sql
-- 1. Identifier les doublons d'étudiants
SELECT user_id, session, COUNT(*) as count, GROUP_CONCAT(id) as ids
FROM student_records 
GROUP BY user_id, session 
HAVING COUNT(*) > 1;

-- 2. Supprimer les doublons (garder le plus ancien)
DELETE sr1 FROM student_records sr1
INNER JOIN student_records sr2 
WHERE sr1.user_id = sr2.user_id 
  AND sr1.session = sr2.session 
  AND sr1.id > sr2.id;

-- 3. Répéter pour les autres tables si nécessaire
-- payment_records, marks, receipts, exam_records
```

## 🚨 Situations d'Urgence

### Si la migration échoue complètement

1. **Restaurer la sauvegarde** :
   ```bash
   mysql -u username -p database_name < backup_file.sql
   ```

2. **Vérifier l'intégrité des données** :
   ```bash
   php artisan tinker
   >>> App\Models\StudentRecord::count()
   >>> DB::table('student_records')->select(DB::raw('user_id, session, COUNT(*) as count'))->groupBy('user_id', 'session')->having('count', '>', 1)->get()
   ```

3. **Nettoyer manuellement et réessayer**

### Si vous avez des contraintes de clés étrangères

```sql
-- Désactiver temporairement les vérifications
SET FOREIGN_KEY_CHECKS=0;

-- Effectuer le nettoyage
-- ... votre nettoyage ...

-- Réactiver les vérifications
SET FOREIGN_KEY_CHECKS=1;
```

## 📊 Vérification Post-Migration

Après une migration réussie, vérifiez :

```bash
# 1. État des migrations
php artisan migrate:status

# 2. Test du système
php test_duplicate_prevention.php

# 3. Vérification des contraintes
php artisan tinker
>>> Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes('student_records')
```

## 🔍 Diagnostic des Problèmes

### Vérifier les doublons existants

```sql
-- Étudiants
SELECT user_id, session, COUNT(*) FROM student_records GROUP BY user_id, session HAVING COUNT(*) > 1;

-- Paiements
SELECT student_id, payment_id, year, COUNT(*) FROM payment_records GROUP BY student_id, payment_id, year HAVING COUNT(*) > 1;

-- Notes
SELECT student_id, subject_id, exam_id, year, COUNT(*) FROM marks GROUP BY student_id, subject_id, exam_id, year HAVING COUNT(*) > 1;
```

### Vérifier l'espace disque

```bash
df -h
# Assurez-vous d'avoir suffisamment d'espace pour les opérations
```

### Vérifier les permissions

```bash
# Vérifier les permissions Laravel
ls -la storage/
ls -la bootstrap/cache/
```

## 💡 Conseils de Prévention

1. **Toujours sauvegarder avant les migrations importantes**
2. **Tester sur un environnement de développement d'abord**
3. **Surveiller les logs pendant la migration** :
   ```bash
   tail -f storage/logs/laravel.log
   ```
4. **Maintenir une fenêtre de maintenance appropriée**

## 📞 Support

Si vous rencontrez des problèmes persistants :

1. **Collectez les informations** :
   - Logs d'erreur complets
   - État de la base de données
   - Version de PHP/Laravel
   - Taille de la base de données

2. **Essayez en mode debug** :
   ```bash
   php artisan migrate --verbose
   ```

3. **Vérifiez les ressources système** :
   - Mémoire disponible
   - Timeout de PHP
   - Limites MySQL

## ✅ Checklist de Récupération

- [ ] Sauvegarde restaurée si nécessaire
- [ ] Doublons nettoyés
- [ ] Migrations exécutées avec succès
- [ ] Contraintes uniques en place
- [ ] Système testé
- [ ] Interface admin accessible
- [ ] Logs propres
- [ ] Performance vérifiée

---

**Important** : Ce guide couvre les scénarios les plus courants. Pour des situations complexes ou des bases de données importantes, considérez consulter un expert en base de données.