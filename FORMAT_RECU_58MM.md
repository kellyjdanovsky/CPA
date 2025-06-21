# Format de Reçu Thermique 58mm - ADRA & TEAM 3

## 📋 Spécifications du Format

### **Dimensions**
- **Largeur** : 58mm (imprimante thermique standard)
- **Largeur utile** : 54mm (avec marges de 2mm de chaque côté)
- **Hauteur** : Variable selon le nombre de paiements
- **Police** : Courier New (monospace) pour alignement parfait

### **Structure du Reçu**

```
REÇU DE PAIEMENT
----------------------
Nom : Jean Rabe
Classe : 6e A
Année : 2024-2025
Statut : ADRA
Code : ADRA-001

-- Paiements --
Écolage Avril : 30 000 Ar
Écolage Mai   : 30 000 Ar
Total         : 60 000 Ar
Pris en charge : 45 000 Ar
Reste à payer  : 15 000 Ar

Montant en lettres :
Quarante-cinq mille ariary

Méthode : ADRA (25% cash)
----------------------
Date : 10/06/2025
Signature : ___________
```

## 🎯 Sections du Reçu

### **1. En-tête**
- Titre centré : "REÇU DE PAIEMENT"
- Séparateur : 22 tirets (----------------------)

### **2. Informations Étudiant**
- **Nom** : Nom complet de l'étudiant
- **Classe** : Classe avec section (ex: 6e A)
- **Année** : Année scolaire (2024-2025)
- **Statut** : ADRA ou TEAM3
- **Code** : Code de référence du paiement

### **3. Section Paiements**
- Titre centré : "-- Paiements --"
- Liste des paiements avec format : `Nom : Montant Ar`
- **Total** : Somme de tous les paiements
- **Pris en charge** : Montant payé (75% pour ADRA, 100% pour TEAM3)
- **Reste à payer** : Solde restant (25% pour ADRA, 0 pour TEAM3)

### **4. Montant en Lettres**
- Titre : "Montant en lettres :"
- Montant écrit en français (ex: "Quarante-cinq mille ariary")

### **5. Pied de Page**
- **Méthode** : Type de paiement avec indication cash pour ADRA
- Séparateur : 22 tirets
- **Date** : Date d'émission du reçu
- **Signature** : Ligne pour signature manuelle

## 💰 Logique de Calcul

### **ADRA (75% pris en charge)**
```
Total des paiements : 60 000 Ar
Pris en charge (75%) : 45 000 Ar
Reste à payer (25%)  : 15 000 Ar
Méthode : ADRA (25% cash)
```

### **TEAM 3 (100% pris en charge)**
```
Total des paiements : 60 000 Ar
Pris en charge (100%) : 60 000 Ar
Reste à payer (0%)   : 0 Ar
Méthode : TEAM3
```

## 🖨️ Paramètres d'Impression

### **CSS pour Imprimante Thermique**
```css
@page {
    size: 58mm auto;
    margin: 0;
}

body {
    font-family: 'Courier New', monospace;
    width: 58mm;
    font-size: 9pt;
    font-weight: bold;
    line-height: 1.1;
}
```

### **Optimisations**
- **Police monospace** pour alignement parfait
- **Taille 9pt** pour lisibilité optimale
- **Marges 2mm** pour éviter la coupure
- **Auto-print** JavaScript pour impression automatique

## 📝 Exemples de Formats

### **Exemple ADRA**
```
REÇU DE PAIEMENT
----------------------
Nom : Marie Dupont
Classe : 3e B
Année : 2024-2025
Statut : ADRA
Code : ADRA-002

-- Paiements --
Inscription    : 25 000 Ar
Écolage Mars   : 35 000 Ar
Total          : 60 000 Ar
Pris en charge : 45 000 Ar
Reste à payer  : 15 000 Ar

Montant en lettres :
Quarante-cinq mille ariary

Méthode : ADRA (25% cash)
----------------------
Date : 20/06/2024
Signature : ___________
```

### **Exemple TEAM 3**
```
REÇU DE PAIEMENT
----------------------
Nom : Paul Martin
Classe : 1re C
Année : 2024-2025
Statut : TEAM3
Code : TEAM3-001

-- Paiements --
Écolage Juin   : 40 000 Ar
Fournitures    : 20 000 Ar
Total          : 60 000 Ar
Pris en charge : 60 000 Ar
Reste à payer  : 0 Ar

Montant en lettres :
Soixante mille ariary

Méthode : TEAM3
----------------------
Date : 20/06/2024
Signature : ___________
```

## 🔧 Implémentation Technique

### **Fichiers Modifiés**
1. `adra_team3_thermal_receipt.blade.php` - Template individuel
2. `adra_team3_batch_receipts.blade.php` - Template par lot
3. `PaymentController.php` - Logique de génération

### **Fonctionnalités**
- **Impression individuelle** : Un reçu par étudiant
- **Impression par lot** : Plusieurs reçus en une fois
- **Auto-print** : Impression automatique à l'ouverture
- **Format responsive** : S'adapte au contenu

### **Intégration**
- **Journal des paiements** : Enregistrement automatique
- **Base de données** : Mise à jour des statuts de paiement
- **Codes de référence** : Génération et édition

## 📱 Compatibilité

### **Imprimantes Supportées**
- Imprimantes thermiques 58mm standard
- Protocole ESC/POS
- Connexion USB, Série, ou Réseau

### **Navigateurs**
- Chrome/Chromium (recommandé)
- Firefox
- Edge
- Safari (avec limitations)

## 🚀 Utilisation

1. **Accéder** à l'interface ADRA & TEAM 3
2. **Sélectionner** les étudiants et paiements
3. **Cliquer** sur "Imprimer" (individuel ou lot)
4. **Le reçu s'ouvre** dans une nouvelle fenêtre
5. **Impression automatique** ou manuelle (Ctrl+P)

---

**Version** : 2.0  
**Date** : 20/06/2024  
**Format** : 58mm Thermique Optimisé  
**Statut** : Production Ready
