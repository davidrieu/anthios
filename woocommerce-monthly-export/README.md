# WooCommerce Monthly Export

Plugin WordPress/WooCommerce pour générer des exports comptables mensuels au format Excel, compatibles avec vos exports Prestashop.

## 📋 Description

Ce plugin permet de générer automatiquement des exports Excel de vos commandes WooCommerce, formatés de manière identique à vos exports Prestashop. Idéal pour faciliter votre comptabilité et avoir une cohérence entre vos différentes boutiques.

### Fonctionnalités

- ✅ Export mensuel des commandes au format Excel (.xlsx)
- ✅ Séparation automatique entre commandes validées et non validées
- ✅ 22 colonnes de données identiques à Prestashop
- ✅ Répartition par taux de TVA (20%, 10%, 5.5%, 0%)
- ✅ Calcul automatique des montants HT et TTC
- ✅ Identification des nouveaux clients
- ✅ Détection des clients B2B (avec société)
- ✅ Interface simple et intuitive dans l'admin WooCommerce

## 📊 Colonnes exportées

L'export contient les colonnes suivantes :

| Colonne | Description |
|---------|-------------|
| id_order | ID de la commande |
| reference | Numéro de commande |
| date_cde | Date de la commande |
| STATUT | Statut de la commande |
| customer | Nom du client |
| Type_Client | Type de client (toujours 1) |
| SOCIETE | Nom de la société (B2B) |
| TOTAL TTC | Montant total TTC |
| TOTAL HT | Montant total HT |
| PRODUITS TTC | Montant produits TTC |
| PRODUITS HT | Montant produits HT |
| HT 20 | Base HT à TVA 20% |
| HT 10 | Base HT à TVA 10% |
| HT 5.5 | Base HT à TVA 5.5% |
| HT 0 | Base HT à TVA 0% |
| PORT TTC | Frais de port TTC |
| PORT HT | Frais de port HT |
| REDUCTION TTC | Réductions TTC |
| REDUCTION HT | Réductions HT |
| payment | Mode de paiement |
| new | Nouveau client (0 ou 1) |
| badge_success | Commande validée (0 ou 1) |

## 📦 Installation

### Prérequis

- WordPress 5.8 ou supérieur
- PHP 7.4 ou supérieur
- WooCommerce 5.0 ou supérieur
- Composer (pour installer les dépendances)

### Méthode 1 : Installation manuelle

1. **Télécharger le plugin**
   ```bash
   git clone https://github.com/davidrieu/anthios.git
   cd anthios/woocommerce-monthly-export
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Uploader le plugin**
   - Compresser le dossier `woocommerce-monthly-export` en fichier ZIP
   - Dans WordPress, aller dans **Extensions > Ajouter**
   - Cliquer sur **Téléverser une extension**
   - Sélectionner le fichier ZIP
   - Cliquer sur **Installer maintenant**
   - Activer le plugin

### Méthode 2 : Installation via FTP

1. **Installer les dépendances**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

2. **Uploader via FTP**
   - Uploader le dossier `woocommerce-monthly-export` dans `/wp-content/plugins/`
   - Se connecter à l'administration WordPress
   - Aller dans **Extensions**
   - Activer **WooCommerce Monthly Export**

## 🚀 Utilisation

1. **Accéder à l'interface d'export**
   - Dans l'administration WordPress, aller dans **WooCommerce > Export Mensuel**

2. **Sélectionner la période**
   - Choisir le mois dans le menu déroulant
   - Choisir l'année dans le menu déroulant

3. **Générer l'export**
   - Cliquer sur **Générer l'export Excel**
   - Le fichier est téléchargé automatiquement

4. **Fichier généré**
   - Nom du fichier : `WooCommerce_MM_YYYY.xlsx`
   - Contient 2 feuilles :
     - **Commandes validées** : Toutes les commandes confirmées et payées
     - **Non validé** : Commandes en attente de paiement

## 📈 Utilisation de l'export

### Pour la comptabilité

1. **Ouvrir le fichier Excel**
2. **Feuille "Commandes validées"** :
   - Utiliser les colonnes `HT 20`, `HT 10`, `HT 5.5` pour la déclaration de TVA
   - Vérifier les totaux TTC et HT
   - Reporter les montants dans votre logiciel comptable

3. **Feuille "Non validé"** :
   - Suivre les commandes en attente
   - Relancer les clients si nécessaire

### Calcul de la TVA

Pour calculer la TVA à reverser :
- TVA à 20% = `HT 20` × 0.20
- TVA à 10% = `HT 10` × 0.10
- TVA à 5.5% = `HT 5.5` × 0.055
- **Total TVA** = Somme des trois

## 🔧 Configuration

### Personnalisation des statuts validés

Par défaut, les statuts suivants sont considérés comme "validés" :
- `processing` (En cours)
- `completed` (Terminée)
- `on-hold` (En attente)

Pour modifier cette liste, éditer le fichier `includes/class-data-extractor.php` ligne 48.

### Personnalisation des taux de TVA

Les taux de TVA sont détectés automatiquement depuis WooCommerce. La correspondance se fait ainsi :
- 19-21% → TVA 20%
- 9-11% → TVA 10%
- 5-6% → TVA 5.5%
- 0% → TVA 0%

Pour modifier ces seuils, éditer `includes/class-data-extractor.php` ligne 244.

## 🐛 Dépannage

### Le bouton n'apparaît pas dans WooCommerce

1. Vérifier que WooCommerce est bien activé
2. Vider le cache (si plugin de cache installé)
3. Désactiver puis réactiver le plugin

### Erreur "Class 'PhpOffice\PhpSpreadsheet\Spreadsheet' not found"

Les dépendances ne sont pas installées :
```bash
cd wp-content/plugins/woocommerce-monthly-export
composer install --no-dev
```

### Le fichier ne se télécharge pas

1. Vérifier les permissions du dossier temporaire système
2. Augmenter la limite de mémoire PHP (dans `wp-config.php`) :
   ```php
   define('WP_MEMORY_LIMIT', '256M');
   ```

### Les montants HT ne correspondent pas

Vérifier que vos produits ont bien des taux de TVA configurés dans WooCommerce :
- **WooCommerce > Réglages > Taxe**
- Configurer les classes de TVA

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :
- Signaler des bugs
- Proposer des améliorations
- Soumettre des pull requests

## 📄 Licence

Ce plugin est distribué sous licence GPL v2 ou ultérieure.

## 👤 Auteur

**David Rieu**
- GitHub: [@davidrieu](https://github.com/davidrieu)

## 📝 Changelog

### Version 1.0.0 (2024)
- Version initiale
- Export mensuel au format Excel
- Compatibilité avec format Prestashop
- Séparation commandes validées/non validées
- Répartition par taux de TVA

## 🆘 Support

Pour toute question ou problème :
1. Consulter la [documentation](https://github.com/davidrieu/anthios)
2. Ouvrir une [issue sur GitHub](https://github.com/davidrieu/anthios/issues)

---

*Développé avec ❤️ pour simplifier votre comptabilité WooCommerce*
