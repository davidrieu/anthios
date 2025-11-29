# 🆘 CORRECTION ERREUR CRITIQUE

## Votre site affiche une erreur critique ?

**Pas de panique !** Voici comment résoudre le problème en 2 minutes.

---

## 🔴 Problème

Le plugin a provoqué une erreur critique car les dépendances PHP (PhpSpreadsheet) n'étaient pas installées avant l'activation.

---

## ✅ SOLUTION IMMÉDIATE (Restaurer votre site)

### Étape 1 : Désactiver le plugin

**Option A : Via l'interface WordPress (si accessible)**
1. Se connecter à l'administration WordPress
2. Aller dans **Extensions**
3. Désactiver **WooCommerce Monthly Export**
4. ✅ Votre site devrait maintenant fonctionner

**Option B : Via FTP (si le site est inaccessible)**
1. Se connecter en FTP à votre serveur
2. Aller dans `/wp-content/plugins/`
3. Renommer le dossier `woocommerce-monthly-export` en `woocommerce-monthly-export-DESACTIVE`
4. ✅ Votre site devrait maintenant fonctionner

**Option C : Via SSH/Terminal**
```bash
cd /chemin/vers/wordpress/wp-content/plugins/
mv woocommerce-monthly-export woocommerce-monthly-export-DESACTIVE
```

---

## 🔧 INSTALLER LE PLUGIN CORRECTEMENT

### Méthode 1 : Installation automatique (RECOMMANDÉE)

1. **Télécharger la version corrigée** depuis GitHub
   ```bash
   cd wp-content/plugins/
   git pull origin claude/analyze-accounting-export-01QvXq9rEgAq8SNqSKBEK9S4
   ```

2. **Accéder au script d'installation**
   - Ouvrir votre navigateur
   - Aller sur : `https://votre-site.com/wp-content/plugins/woocommerce-monthly-export/install-dependencies.php`
   - Cliquer sur **"Installer les dépendances automatiquement"**
   - Attendre la fin de l'installation

3. **Activer le plugin**
   - Aller dans **Extensions**
   - Activer **WooCommerce Monthly Export**
   - ✅ Le plugin fonctionne !

---

### Méthode 2 : Via SSH/Terminal (si vous avez accès)

```bash
# 1. Aller dans le dossier du plugin
cd /chemin/vers/wordpress/wp-content/plugins/woocommerce-monthly-export

# 2. Installer les dépendances avec Composer
composer install --no-dev --optimize-autoloader

# 3. Vérifier que le dossier vendor existe
ls -la vendor/

# 4. Renommer le dossier si vous aviez désactivé le plugin
cd ..
mv woocommerce-monthly-export-DESACTIVE woocommerce-monthly-export
```

Ensuite, dans WordPress :
- Aller dans **Extensions**
- Activer **WooCommerce Monthly Export**

---

### Méthode 3 : Télécharger la version avec dépendances pré-installées

1. **Télécharger le plugin complet**
   - Visitez : https://github.com/davidrieu/anthios/releases
   - Téléchargez la dernière version avec `vendor/` inclus

2. **Remplacer le plugin**
   - Supprimer le dossier actuel via FTP
   - Uploader la nouvelle version
   - Activer dans WordPress

---

### Méthode 4 : Installation manuelle locale puis upload FTP

Si vous avez Composer sur votre ordinateur :

```bash
# Sur votre ordinateur
cd Downloads/
git clone https://github.com/davidrieu/anthios.git
cd anthios/woocommerce-monthly-export
composer install --no-dev --optimize-autoloader

# Le dossier vendor/ est maintenant créé
```

Ensuite via FTP :
1. Uploader tout le dossier `woocommerce-monthly-export/` (avec vendor/)
2. Le placer dans `/wp-content/plugins/`
3. Activer le plugin dans WordPress

---

## 🎯 Ce qui a été corrigé

La nouvelle version (v1.0.1) inclut :

✅ **Vérification des dépendances** avant le chargement
✅ **Message d'erreur clair** au lieu d'une erreur fatale
✅ **Script d'installation automatique** accessible en un clic
✅ **Pas de crash** si les dépendances manquent

---

## 📝 Vérifier que tout fonctionne

Après installation :

1. **Aller dans Extensions**
   - Vérifier qu'il n'y a pas de message d'erreur rouge

2. **Tester le menu**
   - Aller dans **WooCommerce > Export Mensuel**
   - La page doit s'afficher correctement

3. **Générer un export test**
   - Sélectionner le mois en cours
   - Cliquer sur "Générer l'export Excel"
   - Le fichier doit se télécharger

---

## 🔍 Vérifier si les dépendances sont installées

Via SSH/Terminal :
```bash
cd wp-content/plugins/woocommerce-monthly-export/
ls -la vendor/
```

Vous devez voir :
```
drwxr-xr-x  vendor/
drwxr-xr-x  vendor/autoload.php
drwxr-xr-x  vendor/phpoffice/
drwxr-xr-x  vendor/composer/
```

Si `vendor/` n'existe pas ou est vide → Les dépendances ne sont pas installées.

---

## ❓ Questions fréquentes

### "Je n'ai pas accès SSH/Terminal"

→ Utilisez la **Méthode 1** (script automatique) ou la **Méthode 4** (installation locale puis FTP)

### "Composer n'est pas installé sur mon serveur"

→ Utilisez la **Méthode 1** (le script détecte si Composer est disponible) ou la **Méthode 3** (télécharger la version complète)

### "L'erreur persiste après installation"

→ Vérifiez que :
1. Le dossier `vendor/` existe bien
2. Le fichier `vendor/autoload.php` existe
3. Vous avez désactivé puis réactivé le plugin

### "Comment éviter ce problème à l'avenir ?"

→ Toujours exécuter `composer install` AVANT d'activer un plugin qui utilise Composer

---

## 📞 Besoin d'aide ?

Si le problème persiste :

1. Ouvrez une issue GitHub : https://github.com/davidrieu/anthios/issues
2. Incluez :
   - Version de PHP (`php -v`)
   - Version de WordPress
   - Message d'erreur exact
   - Méthode d'installation tentée

---

## ✅ Checklist finale

- [ ] Le site fonctionne à nouveau (plus d'erreur critique)
- [ ] Les dépendances sont installées (dossier `vendor/` existe)
- [ ] Le plugin est activé sans message d'erreur
- [ ] Le menu "Export Mensuel" apparaît dans WooCommerce
- [ ] Un export test fonctionne correctement

---

**Toutes nos excuses pour ce désagrément !**

La version corrigée empêche maintenant ce type d'erreur et vous guide automatiquement vers l'installation des dépendances.

*Version du guide : 1.1 - 2024*
