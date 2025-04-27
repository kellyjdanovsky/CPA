# README - Système de Gestion Scolaire

## Présentation

Ce projet est un système de gestion scolaire développé en Vue.js pour faciliter la gestion administrative et pédagogique d'un établissement scolaire. L'application permet de gérer les finances, les matières, les examens, les notes, les bulletins, et inclut des fonctionnalités d'archivage et d'analyse statistique.

## Contenu du package

- **dist/** : Build de production de l'application
- **documentation_technique.md** : Documentation technique détaillée
- **guide_utilisation.md** : Guide d'utilisation pour les utilisateurs finaux
- **tutoriels.md** : Tutoriels pas à pas pour les fonctionnalités principales

## Installation

### Prérequis

- Serveur web (Apache, Nginx, etc.)
- Navigateur web moderne (Chrome, Firefox, Edge, Safari)

### Déploiement

1. Copiez le contenu du dossier `dist/` sur votre serveur web
2. Configurez votre serveur web pour servir l'application (voir ci-dessous)

#### Configuration Apache

Créez un fichier `.htaccess` dans le dossier racine avec le contenu suivant :

```
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /
  RewriteRule ^index\.html$ - [L]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule . /index.html [L]
</IfModule>
```

#### Configuration Nginx

```
location / {
  try_files $uri $uri/ /index.html;
}
```

## Utilisation locale

Pour tester l'application localement sans serveur web :

1. Ouvrez le fichier `dist/index.html` directement dans votre navigateur
2. Note : certaines fonctionnalités peuvent être limitées en raison des restrictions de sécurité des navigateurs

## Documentation

Consultez les fichiers suivants pour plus d'informations :

- **documentation_technique.md** : Pour les développeurs et administrateurs système
- **guide_utilisation.md** : Pour les utilisateurs finaux
- **tutoriels.md** : Pour apprendre à utiliser les fonctionnalités principales

## Développement futur

Pour continuer le développement de cette application :

1. Clonez le dépôt source
2. Installez les dépendances avec `npm install`
3. Lancez le serveur de développement avec `npm run serve`
4. Construisez une nouvelle version de production avec `npm run build`

## Support

Pour toute question ou problème technique, veuillez contacter :
- Email : support@ecole-exemple.com
- Téléphone : +261 XX XX XX XX
