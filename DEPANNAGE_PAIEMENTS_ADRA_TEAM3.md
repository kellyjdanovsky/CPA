# Dépannage - Paiements ne s'affichent pas

## 🚨 Problème : Les paiements ne se chargent pas quand je sélectionne une classe

### **Étapes de Diagnostic**

#### **1. Vérifier la Console du Navigateur (F12)**

1. **Ouvrir la console** : Appuyer sur F12 → Onglet Console
2. **Sélectionner une classe** et observer les messages
3. **Rechercher ces logs** :
   ```
   Loading payments for class: 1
   Payments response: {success: true, payments: [...]}
   ```

#### **2. Vérifier la Réponse AJAX**

1. **Onglet Network** (F12 → Network)
2. **Sélectionner une classe**
3. **Chercher la requête** : `get-payments?class_id=1`
4. **Cliquer dessus** et vérifier la réponse

**Réponse attendue :**
```json
{
  "success": true,
  "payments": [
    {
      "id": 1,
      "title": "Écolage Janvier",
      "amount": 50000
    }
  ],
  "debug": {
    "class_id": "1",
    "year": "2024",
    "total_count": 1
  }
}
```

#### **3. Tester l'URL Directement**

Aller sur : `http://votre-site.com/payments/adra-team3/get-payments?class_id=1`

**Si erreur 404** : Les routes ne sont pas définies
**Si erreur 500** : Problème dans le contrôleur
**Si JSON vide** : Pas de paiements dans la base

### **Solutions par Type d'Erreur**

#### **Erreur 1 : Routes non trouvées (404)**

**Vérification :**
```bash
php artisan route:list | grep adra
```

**Solution :**
1. Vérifier que les routes sont dans `routes/web.php`
2. Exécuter : `php artisan route:cache`
3. Ou : `php artisan route:clear`

#### **Erreur 2 : Méthode non trouvée (500)**

**Vérification :**
- Le contrôleur `PaymentController` contient `getClassPayments()`

**Solution :**
1. Vérifier que la méthode existe
2. Vérifier les imports en haut du contrôleur
3. Redémarrer le serveur

#### **Erreur 3 : Pas de paiements (JSON vide)**

**Vérification base de données :**
```sql
SELECT * FROM payments WHERE my_class_id = 1 AND year = '2024';
SELECT * FROM payments WHERE my_class_id IS NULL AND year = '2024';
```

**Solution :**
1. Créer des paiements pour la classe
2. Vérifier l'année courante dans le système
3. Vérifier les champs `my_class_id` et `year`

#### **Erreur 4 : JavaScript ne fonctionne pas**

**Vérification :**
1. Console → Taper : `loadClassPayments()`
2. Vérifier les erreurs JavaScript

**Solution :**
1. Vérifier que jQuery est chargé
2. Vérifier que Select2 est chargé
3. Actualiser la page (F5)

### **Tests de Validation**

#### **Test 1 : Base de Données**
```bash
php artisan tinker
>>> App\Models\Payment::where('my_class_id', 1)->get()
>>> App\Models\Payment::whereNull('my_class_id')->get()
```

#### **Test 2 : Contrôleur**
Ajouter dans `getClassPayments()` :
```php
dd($classPayments, $generalPayments, $allPayments);
```

#### **Test 3 : JavaScript**
Ajouter dans la console :
```javascript
$('#class_selector').val(1);
loadClassPayments();
```

### **Vérifications Spécifiques**

#### **1. Structure de la Table `payments`**
```sql
DESCRIBE payments;
```

**Colonnes requises :**
- `id` (int)
- `title` (varchar)
- `amount` (decimal)
- `my_class_id` (int, nullable)
- `year` (varchar)

#### **2. Données de Test**
```sql
INSERT INTO payments (title, amount, my_class_id, year) VALUES
('Écolage Janvier', 50000, 1, '2024'),
('Inscription', 25000, NULL, '2024');
```

#### **3. Permissions Utilisateur**
Vérifier que l'utilisateur connecté a :
- Accès aux paiements
- Droits `teamAccount`
- Session active

### **Solutions Rapides**

#### **Solution 1 : Redémarrage Complet**
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan serve
```

#### **Solution 2 : Vérification des Assets**
1. Vérifier que jQuery est chargé
2. Vérifier que Select2 est chargé
3. Actualiser la page sans cache (Ctrl+F5)

#### **Solution 3 : Mode Debug**
Dans `.env` :
```
APP_DEBUG=true
LOG_LEVEL=debug
```

### **Logs à Vérifier**

#### **1. Logs Laravel**
```bash
tail -f storage/logs/laravel.log
```

Rechercher :
- `Getting payments for class`
- `Found payments`
- `Error loading payments`

#### **2. Logs Serveur Web**
- Apache : `/var/log/apache2/error.log`
- Nginx : `/var/log/nginx/error.log`

### **Contact Support**

Si le problème persiste, fournir :

1. **URL testée** : `/payments/adra-team3/get-payments?class_id=1`
2. **Réponse obtenue** : Copier le JSON complet
3. **Logs d'erreur** : Dernières lignes de `laravel.log`
4. **Console navigateur** : Capture d'écran des erreurs
5. **Version PHP/Laravel** : `php artisan --version`

### **Checklist de Validation**

- [ ] Routes définies et accessibles
- [ ] Méthodes du contrôleur présentes
- [ ] Base de données contient des paiements
- [ ] JavaScript fonctionne sans erreur
- [ ] AJAX retourne des données valides
- [ ] Select2 est initialisé correctement
- [ ] Permissions utilisateur OK
- [ ] Logs ne montrent pas d'erreurs

---

**Dernière mise à jour** : 20/06/2024  
**Version** : 1.0  
**Statut** : Guide de Dépannage Complet
