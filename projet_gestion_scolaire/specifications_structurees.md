# Spécifications du site web de gestion scolaire

## 1. Module de Gestion Financière

### Encaissements
- **Enregistrement des paiements**
  - Champs requis : Date, élève, montant, motif, mode de paiement (Cash/ADRA)
  - Recherche avancée par matricule ou nom

- **Synthèse des Encaissements**
  - Totaux par période : journalier, hebdomadaire, mensuel
  - Taux de paiement par classe ou section

- **Export Personnalisable**
  - Colonnes à inclure : date, élève, montant, statut (payé/impayé)
  - Format PDF professionnel avec en-tête personnalisé

### Décaissements
- **Gestion des Ordres de Paiement**
  - Création simplifiée : bénéficiaire, montant, motif
  - Upload de justificatifs (factures ou reçus scannés)

- **Impression Optimisée**
  - Format A4 (2 ordres par page)
  - Option de signature digitale pour validation électronique

- **Historique des Décaissements**
  - Filtres par bénéficiaire ou montant
  - Export Excel pour comptabilité interne

### Fonctionnalités Supplémentaires
- Intégration avec le module Élèves pour les paiements
- Lien avec le Calendrier pour les échéances
- Backup automatique des données financières (sauvegarde quotidienne dans le cloud)
- Interface utilisateur simplifiée avec tutoriels intégrés

## 2. Module de Gestion des Matières et Examens

### Gestion des Matières
- **Création des matières**
  - Champs requis : nom complet, abréviation, coefficient
  - Fonctionnalités : modification/suppression, tri par ordre alphabétique

### Création des Examens
- **Paramétrage**
  - Type (Trimestre 1, Semestre 1, Examen Final)
  - Période de validité (dates de début/fin)
  - Classes concernées
  - Verrouillage pour empêcher la modification des notes après validation

## 3. Module de Saisie et Calcul des Notes

### Saisie des Notes
- **Interface tableau dynamique**
  - Colonnes : Élève, DS1 (/20), DS2 (/20), Examen (/20)
  - Fonctionnalités : auto-calcul du total pondéré, validation par lot
  - Import/Export : format Excel standardisé, PDF avec mise en page adaptée

### Calculs Automatiques
- **Pour chaque élève**
  - Total = (DS1 + DS2 + Examen)
  - Total pondéré = Total × Coefficient
  - Moyenne générale = Σ(Totaux pondérés) / Σ(Coefficients)
- **Classement**
  - Rang dans la classe
  - Moyenne de la classe

## 4. Module de Génération des Bulletins

### Génération des Bulletins
- **Format A5 Portrait**
  - En-tête : Logo de l'école, année scolaire
  - Corps : détail matière par matière, synthèse (moyenne, rang)
  - Pied : Signature du directeur
- **Options**
  - Impression individuelle/collective
  - Export PDF groupé

### Journal des Notes
- **Consultation**
  - Filtres par classe/examen
  - Recherche par nom d'élève
- **Export**
  - Excel complet (tous les détails)
  - PDF synthétique

### Workflow Typique
1. Admin crée les matières
2. Enseignant paramètre l'examen
3. Saisie des notes (manuelle ou import)
4. Validation → calculs automatiques
5. Génération/impression des bulletins

## 5. Fonctionnalités Avancées

### Archive Numérique des Bulletins
- **Objectif** : Conserver une trace historique sécurisée de tous les bulletins
- **Stockage**
  - Dossier cloud dédié avec sous-dossiers par année/classe
  - Automatisation de l'envoi des PDF via script Python

### Analyse Statistique des Résultats
- **Métriques à Calculer**
  - Moyennes par matière/classe
  - Taux de réussite (>10/20)
  - Évolution des performances (trimestre à trimestre)
- **Visualisation**
  - Tableau de bord avec graphiques (barres pour comparaisons, courbes pour évolutions)
  - Top 5 des matières avec meilleurs/moins bons résultats
- **Export** : Rapports PDF mensuels pour l'équipe pédagogique

## 6. Modèle de Bulletin Scolaire Détaillé

### Structure du Bulletin
- **En-tête**
  - Logo de l'établissement
  - Nom de l'école, année scolaire, trimestre
  - Informations élève (nom, prénom, matricule, classe, effectif)
- **Corps du Bulletin**
  - Structure des colonnes : Matière (Coeff), DS1, DS2, Examen, Moyenne, Total Pondéré, Remarques
  - Légende explicative des calculs
- **Pied de Page**
  - Calculs finaux (somme des totaux pondérés, somme des coefficients, moyenne générale)
  - Moyenne de la classe, rang
  - Espace signatures (professeur principal, directrice, cachet)

### Améliorations Recommandées
- Visualisation graphique (courbe de progression, comparaison avec moyenne de classe)
- Espace personnalisé (appréciation générale, objectifs)
- Précision (2 décimales pour les moyennes)
- Sécurité (filigrane numérique, chiffrement des archives)
- Scripts Python pour calculs certifiés et génération PDF accessible
