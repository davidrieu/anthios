# Installation manuelle - Solution simple

## 🎯 Problème

Le plugin nécessite PhpSpreadsheet mais l'installation automatique échoue sur votre serveur.

## ✅ Solution la plus simple

Je vais créer pour vous un dossier `vendor/` pré-compilé que vous pourrez uploader directement.

---

## 📥 Méthode 1 : Upload du dossier vendor (RECOMMANDÉ)

### Étape 1 : Télécharger le vendor pré-compilé

Je vais créer un fichier ZIP contenant le dossier `vendor/` prêt à l'emploi.

**Lien de téléchargement :** (à venir)

### Étape 2 : Uploader via FTP

1. **Connectez-vous à votre serveur FTP**
   - Hôte : votre-serveur-ftp.com
   - Utilisateur : votre_username
   - Mot de passe : votre_password

2. **Naviguez vers le dossier du plugin**
   ```
   /home/cela6540/aquaphyte.com/wp-content/plugins/woocommerce-monthly-export/
   ```

3. **Uploadez le dossier `vendor/`**
   - Décompressez le ZIP sur votre ordinateur
   - Uploadez tout le dossier `vendor/` dans le dossier du plugin
   - Cela peut prendre 2-3 minutes

4. **Vérifiez que le fichier existe**
   - Vérifiez que ce fichier existe :
   ```
   /wp-content/plugins/woocommerce-monthly-export/vendor/autoload.php
   ```

5. **Retournez dans WordPress**
   - Rafraîchissez la page (F5)
   - Le message d'erreur devrait avoir disparu !

---

## 🖥️ Méthode 2 : Créer le vendor sur votre ordinateur

Si vous avez Composer installé sur votre ordinateur (Windows/Mac/Linux) :

### Sur votre ordinateur

1. **Télécharger le plugin**
   ```bash
   git clone https://github.com/davidrieu/anthios.git
   cd anthios/woocommerce-monthly-export
   ```

2. **Installer les dépendances**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Vérifier que vendor/ a été créé**
   ```bash
   ls -la vendor/
   ```

4. **Uploader via FTP**
   - Connectez-vous en FTP
   - Allez dans `/wp-content/plugins/woocommerce-monthly-export/`
   - Uploadez tout le dossier `vendor/`

---

## 🚀 Méthode 3 : Version complète du plugin

Téléchargez une version complète du plugin avec `vendor/` déjà inclus :

1. **Télécharger depuis GitHub**
   - Allez sur : https://github.com/davidrieu/anthios/releases
   - Téléchargez `woocommerce-monthly-export-complete.zip`

2. **Remplacer le plugin actuel**
   - Supprimez le dossier actuel via FTP
   - Uploadez le nouveau dossier complet
   - Activez le plugin dans WordPress

---

## ✅ Vérification

Une fois le dossier `vendor/` uploadé :

1. Allez dans **Extensions** dans WordPress
2. Le message d'erreur devrait avoir disparu
3. Allez dans **WooCommerce > Export Mensuel**
4. Testez un export !

---

## 📂 Structure attendue

Votre dossier plugin devrait ressembler à ça :

```
woocommerce-monthly-export/
├── vendor/
│   ├── autoload.php          ← Ce fichier est essentiel
│   └── phpoffice/
│       └── phpspreadsheet/
│           └── src/
├── includes/
├── assets/
├── woocommerce-monthly-export.php
└── ...
```

---

## ❓ Besoin d'aide ?

Si vous n'arrivez pas à installer manuellement, je peux :

1. **Créer le fichier vendor.zip pour vous** - Dites-moi et je le prépare
2. **Vous guider pas à pas** - Décrivez-moi votre situation
3. **Créer une version alternative** - Sans PhpSpreadsheet (export CSV au lieu de XLSX)

---

*Pour toute question, ouvrez une issue GitHub ou contactez le support.*
