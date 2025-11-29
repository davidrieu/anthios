# ANALYSE DE L'EXPORT COMPTABLE PRESTASHOP - DÉCEMBRE 2024

## 📊 VUE D'ENSEMBLE

Cet export comptable Prestashop contient **toutes les commandes du mois de décembre 2024** (du 1er au 24 décembre).

### Structure du fichier
Le fichier Excel contient **2 feuilles** :
- **"request_sql_12 (3)"** : 33 commandes validées/en cours de traitement
- **"Non validé"** : 2 commandes en attente de validation (virements bancaires)

---

## 🎯 OBJECTIF DE CET EXPORT

Cet export permet de :

1. **Suivre le chiffre d'affaires** mensuel
2. **Calculer la TVA collectée** par taux (20%, 10%, 5.5%)
3. **Identifier les commandes à traiter** (statut de livraison)
4. **Analyser les paiements** reçus et en attente
5. **Segmenter les clients** (nouveaux vs. existants, B2C vs. B2B)
6. **Contrôler la comptabilité** avec les détails HT/TTC

---

## 📋 DESCRIPTION DES 22 COLONNES

### Identification de la commande
1. **id_order** : Numéro unique de commande dans Prestashop
2. **reference** : Référence unique générée (ex: GIWSCYPEB)
3. **date_cde** : Date de la commande

### Statut et traitement
4. **STATUT** : État de la commande
   - "En cours de livraison" (20 commandes)
   - "Atelier Kokedama" (2 commandes)
   - "Boutique Saint Germain" (1 commande)
   - "Livré" (1 commande)
   - "En attente du paiement par virement bancaire" (2 commandes non validées)

### Informations client
5. **customer** : Nom du client
6. **Type_Client** : Type de client (1.0 = client standard)
7. **SOCIETE** : Nom de la société (pour les clients B2B)
   - 4 commandes B2B identifiées

### Montants globaux
8. **TOTAL TTC** : Montant total TTC de la commande
9. **TOTAL HT** : Montant total HT de la commande

### Détail produits
10. **PRODUITS TTC** : Montant TTC des produits seuls (sans port)
11. **PRODUITS HT** : Montant HT des produits seuls (sans port)

### Répartition par taux de TVA
12. **HT 20** : Base HT soumise à TVA 20%
13. **HT 10** : Base HT soumise à TVA 10%
14. **HT 5.5** : Base HT soumise à TVA 5.5%
15. **HT 0** : Base HT à TVA 0% (non utilisé dans cet export)

### Frais de port
16. **PORT TTC** : Frais de port TTC
17. **PORT HT** : Frais de port HT

### Réductions commerciales
18. **REDUCTION TTC** : Montant TTC des réductions (aucune dans cet export)
19. **REDUCTION HT** : Montant HT des réductions (aucune dans cet export)

### Paiement et marqueurs
20. **payment** : Mode de paiement
    - "Card via Stripe" (20 commandes)
    - "PayPal ou CB" (4 commandes)
    - "Virement bancaire" (2 commandes en attente)

21. **new** : Indicateur nouveau client
    - 1.0 = nouveau client (20 commandes - 60.6%)
    - 0.0 = client existant (4 commandes - 12.1%)

22. **badge_success** : Indicateur de succès/validation
    - 1.0 = validé/réussi (23 commandes)
    - 0.0 = non validé (1 commande)

---

## 💰 ANALYSE FINANCIÈRE

### Chiffre d'affaires validé
- **CA Total TTC** : 3 585,20 €
- **CA Total HT** : 2 959,96 € (produits) + 219,42 € (port) = 3 179,38 €
- **Nombre de commandes** : 33

### Répartition
- **Produits TTC** : 3 321,90 €
- **Frais de port TTC** : 263,30 €
- **Réductions** : 0,00 € (aucune réduction appliquée)

### TVA à reverser
| Taux TVA | Base HT | TVA collectée |
|----------|---------|---------------|
| 20%      | 671,64 € | 134,33 € |
| 10%      | 2 259,98 € | 226,00 € |
| 5.5%     | 28,34 € | 1,56 € |
| **TOTAL** | **2 959,96 €** | **361,88 €** |

---

## 📈 STATISTIQUES COMMERCIALES

### Panier moyen
- **Panier moyen TTC** : 108,64 €
- **Panier médian TTC** : 60,50 €
- **Panier minimum** : 0,00 € (commande annulée ou gratuite)
- **Panier maximum** : 1 792,60 €

### Répartition clients
- **Nouveaux clients** : 20 (60,6%) → CA : 1 450,29 €
- **Clients existants** : 4 (12,1%) → CA : 342,31 €

### Clients professionnels (B2B)
- **4 commandes B2B** pour un montant de 274,26 € TTC
- Sociétés identifiées :
  - Business Alu Masue : 42,95 €
  - Celeris : 96,20 €
  - ENSAP FORMATION : 60,50 €
  - chez atelier dp architectes : 74,61 €

### Modes de paiement
- **Carte bancaire (Stripe)** : 20 commandes (1 649,80 €)
- **PayPal ou CB** : 4 commandes (142,80 €)
- **Virement bancaire** : 2 commandes en attente (102,00 €)

---

## ⚠️ COMMANDES EN ATTENTE

### 2 commandes non validées (102,00 € TTC)

| Référence | Date | Client | Montant TTC | Mode paiement |
|-----------|------|--------|-------------|---------------|
| FNLRVKCIH | 03/12/2024 | J. Dubois | 74 € | Virement bancaire |
| LBQGLZMDP | 03/12/2024 | V. MARMIER | 28 € | Virement bancaire |

**Action requise** : Vérifier la réception des virements bancaires

---

## 📅 ACTIVITÉ JOURNALIÈRE

| Date | Nb commandes | CA TTC |
|------|--------------|--------|
| 01/12 | 1 | 9,30 € |
| 03/12 | 1 | 29,00 € |
| 07/12 | 1 | 42,95 € |
| **08/12** | **4** | **432,71 €** ⭐ (meilleur jour) |
| 09/12 | 1 | 68,00 € |
| 10/12 | 1 | 108,20 € |
| 11/12 | 1 | 60,50 € |
| 13/12 | 2 | 149,61 € |
| 14/12 | 2 | 140,00 € |
| 16/12 | 2 | 104,50 € |
| 17/12 | 1 | 28,00 € |
| 18/12 | 1 | 60,00 € |
| 20/12 | 2 | 249,83 € |
| 21/12 | 2 | 192,00 € |
| 23/12 | 1 | 80,00 € |
| 24/12 | 1 | 38,00 € |

---

## 🎯 UTILISATION PRATIQUE

### Pour la comptabilité
1. **Saisie des ventes** : Utiliser TOTAL HT par taux de TVA (HT 20, HT 10, HT 5.5)
2. **Déclaration TVA** : Reporter les montants de TVA collectée calculés
3. **Frais de port** : Comptabiliser séparément les 219,42 € HT de frais de port

### Pour le suivi commercial
1. **Relancer** les 2 commandes en attente de virement
2. **Analyser** pourquoi 60,6% sont des nouveaux clients (acquisition efficace ?)
3. **Développer** le segment B2B (seulement 4 commandes)

### Pour la trésorerie
- **Encaissé** : 3 585,20 € TTC
- **À encaisser** : 102,00 € TTC (virements en attente)
- **Total période** : 3 687,20 € TTC

---

## 🔍 POINTS D'ATTENTION

1. **Incohérence détectée** : Le TOTAL HT global (6 764,58 €) ne correspond pas à la somme des montants HT attendus (environ 3 179,38 €). Cela peut indiquer :
   - Un problème d'export
   - Des colonnes mal étiquetées
   - Des données de test mélangées

2. **Badge success** : 23 commandes sur 24 ont le badge success, signifiant probablement :
   - Paiement validé
   - Commande confirmée et traitée

3. **Commandes avec montant 0** : Une commande a un panier minimum de 0 €, à vérifier

---

## 💡 RECOMMANDATIONS

1. **Vérifier l'intégrité des données** : Contrôler la cohérence TOTAL HT vs somme des produits HT + port HT
2. **Suivre les virements** : Relancer les 2 clients pour validation des paiements
3. **Optimiser la conversion B2B** : Seulement 12% des commandes sont B2B
4. **Analyser le pic du 8/12** : 4 commandes ce jour-là, identifier la cause (promo, email marketing ?)

---

*Analyse générée automatiquement à partir du fichier "Prestashop 12-24.xlsx"*
*Période couverte : 01/12/2024 au 24/12/2024 (24 jours)*
