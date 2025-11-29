# Guide d'installation rapide

## Installation en 3 étapes

### Étape 1 : Installer les dépendances

```bash
cd woocommerce-monthly-export
composer install --no-dev --optimize-autoloader
```

**Important** : Cette étape est **obligatoire** pour que le plugin fonctionne.

### Étape 2 : Uploader le plugin

**Option A : Via l'interface WordPress**
1. Créer un fichier ZIP du dossier `woocommerce-monthly-export`
2. Aller dans **Extensions > Ajouter > Téléverser une extension**
3. Sélectionner le fichier ZIP
4. Cliquer sur **Installer maintenant**

**Option B : Via FTP**
1. Uploader le dossier `woocommerce-monthly-export` dans `/wp-content/plugins/`
2. Les permissions doivent être 755 pour le dossier et 644 pour les fichiers

### Étape 3 : Activer le plugin

1. Aller dans **Extensions**
2. Chercher **WooCommerce Monthly Export**
3. Cliquer sur **Activer**

## Vérification de l'installation

✅ Le plugin est correctement installé si :
- Aucun message d'erreur n'apparaît
- Un nouveau menu **Export Mensuel** est visible sous **WooCommerce**

## Premier export

1. Aller dans **WooCommerce > Export Mensuel**
2. Sélectionner le mois en cours
3. Cliquer sur **Générer l'export Excel**
4. Le fichier se télécharge automatiquement

## Prérequis système

- ✅ WordPress 5.8+
- ✅ PHP 7.4+
- ✅ WooCommerce 5.0+
- ✅ Extension PHP : `zip`, `xml`, `mbstring`
- ✅ Composer installé (pour l'installation)

## Problèmes courants

### "Class not found"
→ Vous n'avez pas exécuté `composer install`

### "Permission denied"
→ Vérifier les permissions des fichiers (644) et dossiers (755)

### Le menu n'apparaît pas
→ Vérifier que WooCommerce est bien activé

## Désinstallation

1. Désactiver le plugin dans **Extensions**
2. Supprimer le plugin
3. Les données des commandes ne sont pas affectées

---

**Besoin d'aide ?** Consultez le [README.md](README.md) complet ou ouvrez une [issue](https://github.com/davidrieu/anthios/issues).
