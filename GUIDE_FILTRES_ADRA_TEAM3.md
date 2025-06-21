# Guide des Filtres et Totaux - Interface ADRA & TEAM 3

## 🎯 Nouvelles Fonctionnalités Ajoutées

### **1. Zone de Filtrage Avancée**

#### **Filtre par Classe**
- **Dropdown dynamique** avec toutes les classes disponibles
- **Rechargement automatique** de la page avec les étudiants de la classe sélectionnée
- **URL mise à jour** pour permettre les liens directs

#### **Filtre par Paiements Créés**
- **Multi-sélection** des paiements spécifiques à la classe
- **Affichage en temps réel** des étudiants ayant ces paiements
- **Montants affichés** pour chaque paiement dans la liste

#### **Filtre par Statut**
- **ADRA (75%)** : Affiche uniquement les étudiants ADRA
- **TEAM 3 (100%)** : Affiche uniquement les étudiants TEAM 3
- **Tous les statuts** : Affiche tous les étudiants

### **2. Cartes de Résumé en Temps Réel**

#### **Étudiants Affichés** (Carte Bleue)
- Nombre total d'étudiants visibles après filtrage
- Mise à jour automatique lors des changements de filtre

#### **Total Sélectionné** (Carte Verte)
- Montant total de tous les paiements sélectionnés
- Calcul automatique basé sur les statuts (ADRA 75%, TEAM3 100%)

#### **ADRA (75%)** (Carte Info)
- Montant total des paiements ADRA sélectionnés
- Affiche uniquement la partie prise en charge (75%)

#### **TEAM 3 (100%)** (Carte Warning)
- Montant total des paiements TEAM 3 sélectionnés
- Affiche le montant complet (100%)

### **3. Résumé de Sélection Détaillé**

#### **Carte de Résumé** (Bordure Verte)
- **Étudiants sélectionnés** : Nombre d'étudiants avec paiements cochés
- **Total à payer** : Somme de tous les montants à payer
- **ADRA (75%)** : Sous-total des paiements ADRA
- **TEAM 3 (100%)** : Sous-total des paiements TEAM 3

#### **Boutons d'Action Intelligents**
- **Imprimer Sélection** : Activé uniquement si des étudiants sont sélectionnés
- **Compteur dynamique** : Affiche le nombre d'étudiants à imprimer
- **Tout sélectionner** : Sélectionne tous les étudiants visibles
- **Tout désélectionner** : Remet à zéro toutes les sélections
- **Export Excel** : Exporte les données filtrées

## 📊 Comment Utiliser les Filtres

### **Étape 1 : Filtrage Initial**
1. **Sélectionnez une classe** dans le premier dropdown
2. **Choisissez les paiements** à afficher (optionnel)
3. **Filtrez par statut** ADRA ou TEAM 3 (optionnel)

### **Étape 2 : Sélection des Étudiants**
1. **Cochez les étudiants** souhaités dans la première colonne
2. **Sélectionnez les paiements** pour chaque étudiant
3. **Observez les totaux** se mettre à jour en temps réel

### **Étape 3 : Vérification des Totaux**
1. **Cartes de résumé** : Vérifiez les montants globaux
2. **Résumé de sélection** : Contrôlez les détails par statut
3. **Bouton d'impression** : Vérifiez le nombre d'étudiants sélectionnés

### **Étape 4 : Actions**
1. **Impression individuelle** : Bouton sur chaque ligne
2. **Impression par lot** : Bouton "Imprimer Sélection"
3. **Export Excel** : Données filtrées et sélectionnées

## 🔢 Logique de Calcul

### **Calculs Automatiques**
```javascript
// Pour chaque étudiant sélectionné
if (status === 'ADRA') {
    amountToPay = totalPayments * 0.75;  // 75% pris en charge
    balance = totalPayments * 0.25;      // 25% reste à payer
} else if (status === 'TEAM3') {
    amountToPay = totalPayments * 1.0;   // 100% pris en charge
    balance = 0;                         // Rien à payer
}
```

### **Totaux Globaux**
- **Total Sélectionné** = Somme de tous les `amountToPay`
- **ADRA Total** = Somme des `amountToPay` pour statut ADRA
- **TEAM3 Total** = Somme des `amountToPay` pour statut TEAM3

## 🎨 Interface Utilisateur

### **Indicateurs Visuels**
- **🏛️ ADRA** : Badge bleu avec icône bâtiment
- **👥 TEAM 3** : Badge vert avec icône groupe
- **Cartes colorées** : Bleu, Vert, Info, Warning
- **Bordures colorées** : Vert pour le résumé de sélection

### **États Interactifs**
- **Boutons désactivés** : Grisés quand aucune sélection
- **Compteurs dynamiques** : Mise à jour en temps réel
- **Animations** : Transitions fluides sur les cartes

### **Responsive Design**
- **Colonnes adaptatives** : 4 colonnes sur desktop, empilées sur mobile
- **Boutons groupés** : Verticaux sur petits écrans
- **Tableaux responsifs** : Défilement horizontal si nécessaire

## 🚀 Fonctionnalités Avancées

### **Filtrage Intelligent**
- **Combinaison de filtres** : Classe + Paiements + Statut
- **Persistance d'URL** : Liens directs vers une classe spécifique
- **Recherche DataTable** : Recherche textuelle dans le tableau

### **Sélection Intelligente**
- **Sélection visible uniquement** : "Tout sélectionner" ne prend que les lignes visibles
- **Auto-sélection des paiements** : Option pour sélectionner tous les paiements d'un étudiant
- **Validation avant impression** : Vérification qu'au moins un paiement est sélectionné

### **Calculs en Temps Réel**
- **Mise à jour instantanée** : Dès qu'une checkbox change
- **Formatage automatique** : Nombres avec séparateurs de milliers
- **Validation des montants** : Vérification de cohérence

## 📱 Utilisation Mobile

### **Adaptations Mobiles**
- **Cartes empilées** : Une par ligne sur petits écrans
- **Boutons plus grands** : Facilité de toucher
- **Texte lisible** : Tailles adaptées aux écrans tactiles

### **Gestes Tactiles**
- **Tap pour sélectionner** : Checkboxes facilement accessibles
- **Swipe horizontal** : Défilement du tableau
- **Pinch to zoom** : Zoom sur les détails

## 🔧 Maintenance et Dépannage

### **Performance**
- **Pagination** : 25 étudiants par page par défaut
- **Calculs optimisés** : Mise à jour uniquement des éléments modifiés
- **Cache des totaux** : Évite les recalculs inutiles

### **Dépannage Courant**
- **Totaux incorrects** : Vérifier les statuts des étudiants
- **Filtres ne fonctionnent pas** : Actualiser la page
- **Impression bloquée** : Vérifier qu'au moins un étudiant est sélectionné

---

**Version** : 2.0  
**Date** : 20/06/2024  
**Fonctionnalités** : Filtres Avancés + Totaux Temps Réel  
**Statut** : Production Ready
