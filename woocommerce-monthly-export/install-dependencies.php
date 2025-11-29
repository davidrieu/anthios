<?php
/**
 * Script d'installation automatique des dépendances
 *
 * Ce script télécharge et installe PhpSpreadsheet sans nécessiter Composer
 *
 * @package WC_Monthly_Export
 */

// Charger WordPress si pas déjà chargé
if (!defined('ABSPATH')) {
    // Chercher wp-load.php en remontant les répertoires
    $wp_load = null;
    $dir = dirname(__FILE__);

    // Remonter jusqu'à 5 niveaux pour trouver wp-load.php
    for ($i = 0; $i < 5; $i++) {
        $dir = dirname($dir);
        if (file_exists($dir . '/wp-load.php')) {
            $wp_load = $dir . '/wp-load.php';
            break;
        }
    }

    if ($wp_load === null) {
        die('Erreur: Impossible de trouver WordPress. Ce script doit être exécuté depuis un plugin WordPress.');
    }

    require_once $wp_load;
}

// Vérifier les permissions admin
if (!current_user_can('manage_options')) {
    wp_die('Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
}

// Définir le répertoire du plugin
if (!defined('WC_MONTHLY_EXPORT_PLUGIN_DIR')) {
    define('WC_MONTHLY_EXPORT_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Installation des dépendances - WooCommerce Monthly Export</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f0f0f1;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1d2327;
            margin-top: 0;
        }
        .step {
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #2271b1;
            background: #f6f7f7;
        }
        .success {
            border-left-color: #00a32a;
            background: #f0f6fc;
        }
        .error {
            border-left-color: #d63638;
            background: #fcf0f1;
        }
        .warning {
            border-left-color: #dba617;
            background: #fcf9e8;
        }
        code {
            background: #2271b1;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: Consolas, Monaco, monospace;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #2271b1;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .button:hover {
            background: #135e96;
        }
        .button-large {
            padding: 12px 30px;
            font-size: 16px;
        }
        pre {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Installation des dépendances</h1>

        <?php
        $vendor_dir = WC_MONTHLY_EXPORT_PLUGIN_DIR . 'vendor';
        $autoload_file = $vendor_dir . '/autoload.php';

        // Vérifier si les dépendances sont déjà installées
        if (file_exists($autoload_file)) {
            ?>
            <div class="step success">
                <h3>✅ Dépendances déjà installées</h3>
                <p>Les dépendances PHP (PhpSpreadsheet) sont déjà installées et fonctionnelles.</p>
                <p><a href="<?php echo admin_url('admin.php?page=wc-monthly-export'); ?>" class="button">Accéder à l'export mensuel</a></p>
            </div>
            <?php
        } else {
            // Vérifier si Composer est disponible
            exec('composer --version 2>&1', $output, $return_code);
            $composer_available = ($return_code === 0);

            if (isset($_POST['install_dependencies'])) {
                ?>
                <div class="step">
                    <h3>Installation en cours...</h3>
                    <?php

                    flush();
                    ob_flush();

                    $success = false;

                    if ($composer_available) {
                        // Méthode 1: Installer avec Composer
                        echo '<p>📦 Exécution de Composer...</p>';
                        flush();
                        $install_dir = WC_MONTHLY_EXPORT_PLUGIN_DIR;
                        $command = "cd " . escapeshellarg($install_dir) . " && composer install --no-dev --optimize-autoloader 2>&1";
                        exec($command, $install_output, $install_code);

                        if ($install_code === 0 && file_exists($autoload_file)) {
                            $success = true;
                        }
                    }

                    if (!$success) {
                        // Méthode 2: Téléchargement direct depuis GitHub
                        echo '<p>📥 Téléchargement de PhpSpreadsheet depuis GitHub...</p>';
                        flush();

                        // URL du fichier ZIP de PhpSpreadsheet
                        $phpspreadsheet_version = '1.29.0';
                        $zip_url = "https://github.com/PHPOffice/PhpSpreadsheet/archive/refs/tags/{$phpspreadsheet_version}.zip";
                        $zip_file = WC_MONTHLY_EXPORT_PLUGIN_DIR . 'phpspreadsheet.zip';

                        // Télécharger le fichier
                        $zip_content = file_get_contents($zip_url);

                        if ($zip_content !== false) {
                            file_put_contents($zip_file, $zip_content);
                            echo '<p>✅ Téléchargement terminé</p>';
                            flush();

                            // Extraire le ZIP
                            echo '<p>📂 Extraction des fichiers...</p>';
                            flush();

                            $zip = new ZipArchive;
                            if ($zip->open($zip_file) === TRUE) {
                                // Créer le dossier vendor s'il n'existe pas
                                if (!file_exists($vendor_dir)) {
                                    mkdir($vendor_dir, 0755, true);
                                }

                                // Créer les dossiers nécessaires
                                $phpoffice_dir = $vendor_dir . '/phpoffice';
                                $spreadsheet_dir = $phpoffice_dir . '/phpspreadsheet';

                                if (!file_exists($phpoffice_dir)) {
                                    mkdir($phpoffice_dir, 0755, true);
                                }

                                // Extraire dans un dossier temporaire
                                $temp_dir = WC_MONTHLY_EXPORT_PLUGIN_DIR . 'temp_extract';
                                $zip->extractTo($temp_dir);
                                $zip->close();

                                // Déplacer les fichiers au bon endroit
                                $extracted_dir = $temp_dir . "/PhpSpreadsheet-{$phpspreadsheet_version}";
                                if (file_exists($extracted_dir)) {
                                    rename($extracted_dir, $spreadsheet_dir);
                                }

                                // Nettoyer
                                unlink($zip_file);
                                if (file_exists($temp_dir)) {
                                    rmdir($temp_dir);
                                }

                                // Créer l'autoload.php
                                $autoload_content = <<<'PHP'
<?php
// Autoloader pour PhpSpreadsheet
spl_autoload_register(function ($class) {
    // PhpSpreadsheet classes
    if (strpos($class, 'PhpOffice\\PhpSpreadsheet\\') === 0) {
        $classFile = __DIR__ . '/phpoffice/phpspreadsheet/src/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($classFile)) {
            require_once $classFile;
            return;
        }
    }

    // Autres dépendances possibles
    $dependencies = [
        'Psr\\SimpleCache\\' => __DIR__ . '/psr/simple-cache/src/',
        'Psr\\Http\\Message\\' => __DIR__ . '/psr/http-message/src/',
        'Psr\\Http\\Client\\' => __DIR__ . '/psr/http-client/src/',
    ];

    foreach ($dependencies as $prefix => $baseDir) {
        if (strpos($class, $prefix) === 0) {
            $relativeClass = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});
PHP;
                                file_put_contents($autoload_file, $autoload_content);

                                echo '<p>✅ Extraction terminée</p>';
                                flush();

                                if (file_exists($autoload_file)) {
                                    $success = true;
                                }
                            } else {
                                echo '<p>❌ Impossible d\'extraire le fichier ZIP</p>';
                            }
                        } else {
                            echo '<p>❌ Échec du téléchargement</p>';
                        }
                    }

                    if ($success) {
                        ?>
                        <div class="step success">
                            <h3>✅ Installation réussie !</h3>
                            <p>Les dépendances ont été installées avec succès.</p>
                            <p><strong>Prochaines étapes :</strong></p>
                            <ol>
                                <li>Fermer cet onglet</li>
                                <li>Retourner dans l'administration WordPress</li>
                                <li>Rafraîchir la page (F5)</li>
                                <li>Le message d'erreur devrait avoir disparu</li>
                                <li>Aller dans WooCommerce > Export Mensuel</li>
                            </ol>
                            <p><a href="<?php echo admin_url('plugins.php'); ?>" class="button button-large">Retour aux Extensions</a></p>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="step error">
                            <h3>❌ Erreur lors de l'installation</h3>
                            <p>L'installation automatique a échoué.</p>
                            <p>Veuillez essayer une installation manuelle ou contactez le support.</p>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            } else {
                // Afficher les options d'installation
                ?>
                <div class="step warning">
                    <h3>⚠️ Dépendances manquantes</h3>
                    <p>Les dépendances PHP (PhpSpreadsheet) ne sont pas installées.</p>
                    <p>Le plugin ne peut pas fonctionner sans ces dépendances.</p>
                </div>

                <h2>Option 1 : Installation automatique ✅ (Recommandée)</h2>

                <div class="step">
                    <?php if ($composer_available): ?>
                        <p>✅ Composer est détecté sur votre serveur. Cliquez sur le bouton ci-dessous pour installer automatiquement les dépendances.</p>
                    <?php else: ?>
                        <p>⚠️ Composer n'est pas disponible, mais ce n'est pas grave !</p>
                        <p><strong>Le script va télécharger PhpSpreadsheet directement depuis Internet et l'installer automatiquement.</strong></p>
                    <?php endif; ?>
                    <form method="post" action="">
                        <button type="submit" name="install_dependencies" class="button button-large">
                            🚀 Installer les dépendances automatiquement
                        </button>
                    </form>
                    <p style="margin-top: 10px; font-size: 12px; color: #666;">
                        <em>Cela peut prendre 30 secondes à 1 minute...</em>
                    </p>
                </div>

                <h2>Option 2 : Installation manuelle via SSH/Terminal</h2>
                <div class="step">
                    <p>Si vous avez accès SSH à votre serveur :</p>
                    <pre>cd <?php echo esc_html(WC_MONTHLY_EXPORT_PLUGIN_DIR); ?>
composer install --no-dev --optimize-autoloader</pre>
                    <p>Ensuite, désactivez et réactivez le plugin dans WordPress.</p>
                </div>

                <h2>Option 3 : Installation manuelle via FTP</h2>
                <div class="step">
                    <p>Si vous avez un ordinateur avec Composer installé :</p>
                    <ol>
                        <li>Télécharger le plugin sur votre ordinateur</li>
                        <li>Ouvrir un terminal dans le dossier du plugin</li>
                        <li>Exécuter : <code>composer install --no-dev</code></li>
                        <li>Uploader le dossier <code>vendor/</code> via FTP dans :<br>
                            <code><?php echo esc_html(WC_MONTHLY_EXPORT_PLUGIN_DIR); ?></code></li>
                        <li>Désactiver et réactiver le plugin</li>
                    </ol>
                </div>

                <h2>Option 4 : Télécharger la version complète</h2>
                <div class="step">
                    <p>Téléchargez une version du plugin avec les dépendances pré-installées :</p>
                    <p><a href="https://github.com/davidrieu/anthios/releases" class="button" target="_blank">📦 Télécharger sur GitHub</a></p>
                    <p><small>(Remplacez le dossier actuel du plugin par la version téléchargée)</small></p>
                </div>
                <?php
            }
        }
        ?>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #dcdcde;">

        <p><a href="<?php echo admin_url('plugins.php'); ?>">← Retour aux extensions</a></p>
    </div>
</body>
</html>
