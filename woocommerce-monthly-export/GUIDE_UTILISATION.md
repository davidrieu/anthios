# Guide d'utilisation - WooCommerce Monthly Export

## 🎯 Comment générer un export mensuel

### Accès rapide

1. Connectez-vous à votre administration WordPress
2. Dans le menu de gauche, cliquez sur **WooCommerce**
3. Cliquez sur **Export Mensuel**

### Générer un export

1. **Sélectionner le mois** que vous souhaitez exporter
   - Par défaut, le mois en cours est sélectionné

2. **Sélectionner l'année**
   - Par défaut, l'année en cours est sélectionnée
   - Vous pouvez remonter jusqu'à 5 ans en arrière

3. **Cliquer sur le bouton "Générer l'export Excel"**
   - Le fichier se télécharge automatiquement
   - Le téléchargement démarre immédiatement

### Nom du fichier

Le fichier généré aura ce format :
```
WooCommerce_MM_YYYY.xlsx
```

Exemples :
- `WooCommerce_12_2024.xlsx` pour décembre 2024
- `WooCommerce_01_2025.xlsx` pour janvier 2025

## 📊 Contenu de l'export

### Deux feuilles Excel

Le fichier Excel contient **2 feuilles** :

#### 1. Commandes validées
Contient toutes les commandes avec les statuts :
- ✅ En cours (Processing)
- ✅ Terminée (Completed)
- ✅ En attente (On-hold)

**Utilisation** : Ces commandes sont à saisir en comptabilité

#### 2. Non validé
Contient les commandes avec les statuts :
- ⏳ En attente de paiement (Pending)
- ❌ Échouée (Failed)
- 🔄 Remboursée (Refunded)
- ⛔ Annulée (Cancelled)

**Utilisation** : Ces commandes sont à suivre et ne doivent PAS être comptabilisées

## 💡 Utilisation pour la comptabilité

### Étape 1 : Ouvrir le fichier

Ouvrez le fichier avec :
- Microsoft Excel
- LibreOffice Calc
- Google Sheets
- Numbers (Mac)

### Étape 2 : Vérifier les données

Sur la feuille **"Commandes validées"** :

1. **Vérifier le nombre de commandes**
   - Compter les lignes (hors en-tête)

2. **Vérifier le CA total**
   - Somme de la colonne **TOTAL TTC**
   - Somme de la colonne **TOTAL HT**

### Étape 3 : Calculer la TVA

Pour votre déclaration de TVA :

| Taux | Colonne | Calcul TVA |
|------|---------|------------|
| 20% | HT 20 | Somme × 0.20 |
| 10% | HT 10 | Somme × 0.10 |
| 5.5% | HT 5.5 | Somme × 0.055 |

**Exemple** :
```
HT 20 = 2 259,98 €  →  TVA = 2 259,98 × 0.20 = 451,99 €
HT 10 = 671,64 €    →  TVA = 671,64 × 0.10 = 67,16 €
HT 5.5 = 28,34 €    →  TVA = 28,34 × 0.055 = 1,56 €

Total TVA à reverser = 520,71 €
```

### Étape 4 : Saisir en comptabilité

Dans votre logiciel comptable, saisir :
- **Ventes TTC** : Colonne TOTAL TTC
- **Ventes HT par taux** : Colonnes HT 20, HT 10, HT 5.5
- **TVA collectée** : Calculs ci-dessus
- **Frais de port** : Colonnes PORT TTC et PORT HT

## 📋 Colonnes importantes

### Pour la compta

| Colonne | Usage |
|---------|-------|
| date_cde | Date de facturation |
| reference | Numéro de facture |
| TOTAL TTC | Montant à encaisser |
| TOTAL HT | Base pour compta HT |
| HT 20 / HT 10 / HT 5.5 | Répartition TVA |

### Pour l'analyse

| Colonne | Usage |
|---------|-------|
| customer | Nom du client |
| SOCIETE | Clients professionnels (B2B) |
| new | Nouveaux clients (1) vs anciens (0) |
| payment | Mode de paiement reçu |

## 🔍 Filtrer et analyser les données

### Dans Excel / Calc

1. **Activer les filtres**
   - Sélectionner la ligne d'en-tête
   - Cliquer sur "Données > Filtrer"

2. **Filtrer par statut**
   - Cliquer sur la flèche de la colonne STATUT
   - Cocher/décocher les statuts souhaités

3. **Filtrer par mode de paiement**
   - Utiliser la colonne "payment"
   - Exemple : afficher uniquement les CB

4. **Filtrer les nouveaux clients**
   - Colonne "new" = 1 → Nouveaux clients
   - Colonne "new" = 0 → Clients fidèles

### Créer un tableau croisé dynamique

Pour analyser vos ventes :
1. Sélectionner toutes les données
2. Insertion > Tableau croisé dynamique
3. Exemples d'analyses :
   - CA par mode de paiement
   - CA par statut
   - Nombre de commandes par jour

## ⚠️ Points d'attention

### Commandes à vérifier

Sur la feuille **"Non validé"** :
- Relancer les clients en attente de paiement
- Vérifier les commandes échouées
- Ne PAS comptabiliser ces commandes

### Cohérence des données

Vérifier que :
```
TOTAL HT ≈ PRODUITS HT + PORT HT - REDUCTION HT
TOTAL TTC ≈ TOTAL HT + TVA
```

Si écart important :
- Vérifier la configuration TVA dans WooCommerce
- Vérifier les taux de TVA des produits

## 🆘 Questions fréquentes

### Le montant HT ne correspond pas au TTC

**Cause** : Problème de configuration TVA dans WooCommerce

**Solution** :
1. WooCommerce > Réglages > Taxe
2. Vérifier que les taux de TVA sont corrects
3. Refaire l'export

### Certaines commandes n'apparaissent pas

**Cause** : Mauvais mois/année sélectionné

**Solution** :
- Vérifier la date de création de la commande
- Exporter le bon mois

### Les colonnes HT 20/10/5.5 sont toutes à 0

**Cause** : Pas de TVA configurée dans WooCommerce

**Solution** :
1. Activer la TVA dans WooCommerce
2. Configurer les taux par défaut
3. Les futures commandes auront la TVA

## 📞 Besoin d'aide ?

1. Consulter le [README complet](README.md)
2. Vérifier les [instructions d'installation](INSTALL.md)
3. Ouvrir une [issue sur GitHub](https://github.com/davidrieu/anthios/issues)

---

**Bon export ! 📊**
