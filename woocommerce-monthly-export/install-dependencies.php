<?php
/**
 * Script d'installation automatique des dépendances
 *
 * Ce script télécharge et installe PhpSpreadsheet sans nécessiter Composer
 *
 * @package WC_Monthly_Export
 */

// Sécurité : Empêcher l'accès direct
if (!defined('ABSPATH')) {
    // Si accès direct, vérifier qu'on est dans WordPress
    if (!file_exists('../../wp-load.php')) {
        die('Accès direct non autorisé');
    }
    require_once '../../wp-load.php';
}

// Vérifier les permissions admin
if (!current_user_can('manage_options')) {
    wp_die('Vous n\'avez pas les permissions nécessaires.');
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

                    if ($composer_available) {
                        // Installer avec Composer
                        echo '<p>Exécution de Composer...</p>';
                        $install_dir = WC_MONTHLY_EXPORT_PLUGIN_DIR;
                        $command = "cd " . escapeshellarg($install_dir) . " && composer install --no-dev --optimize-autoloader 2>&1";
                        exec($command, $install_output, $install_code);

                        if ($install_code === 0 && file_exists($autoload_file)) {
                            ?>
                            <div class="step success">
                                <h3>✅ Installation réussie !</h3>
                                <p>Les dépendances ont été installées avec succès.</p>
                                <p><strong>Prochaines étapes :</strong></p>
                                <ol>
                                    <li>Aller dans Extensions et désactiver le plugin "WooCommerce Monthly Export"</li>
                                    <li>Réactiver le plugin</li>
                                    <li>Aller dans WooCommerce > Export Mensuel</li>
                                </ol>
                                <p><a href="<?php echo admin_url('plugins.php'); ?>" class="button button-large">Aller aux Extensions</a></p>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="step error">
                                <h3>❌ Erreur lors de l'installation</h3>
                                <p>L'installation avec Composer a échoué.</p>
                                <pre><?php echo esc_html(implode("\n", $install_output)); ?></pre>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="step error">
                            <h3>❌ Composer non disponible</h3>
                            <p>Composer n'est pas installé sur ce serveur.</p>
                            <p>Veuillez suivre la méthode manuelle ci-dessous.</p>
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

                <h2>Option 1 : Installation automatique <?php echo $composer_available ? '✅ (Recommandée)' : '❌ (Non disponible)'; ?></h2>

                <?php if ($composer_available): ?>
                    <div class="step">
                        <p>Composer est détecté sur votre serveur. Cliquez sur le bouton ci-dessous pour installer automatiquement les dépendances.</p>
                        <form method="post" action="">
                            <button type="submit" name="install_dependencies" class="button button-large">
                                🚀 Installer les dépendances automatiquement
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="step error">
                        <p>Composer n'est pas disponible sur ce serveur.</p>
                        <p>Veuillez utiliser une des méthodes manuelles ci-dessous.</p>
                    </div>
                <?php endif; ?>

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
