<?php
/**
 * Script d'installation simplifié - Version qui marche vraiment !
 */

// Charger WordPress
if (!defined('ABSPATH')) {
    $wp_load = null;
    $dir = dirname(__FILE__);
    for ($i = 0; $i < 5; $i++) {
        $dir = dirname($dir);
        if (file_exists($dir . '/wp-load.php')) {
            $wp_load = $dir . '/wp-load.php';
            break;
        }
    }
    if ($wp_load === null) {
        die('Erreur: Impossible de trouver WordPress.');
    }
    require_once $wp_load;
}

if (!current_user_can('manage_options')) {
    wp_die('Permissions insuffisantes.');
}

$plugin_dir = dirname(__FILE__);
$vendor_dir = $plugin_dir . '/vendor';
$autoload_file = $vendor_dir . '/autoload.php';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Installation simplifiée</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 10px 0; }
        .error { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 10px 0; }
        .info { background: #d1ecf1; border-left: 4px solid #0c5460; padding: 15px; margin: 10px 0; }
        .btn { display: inline-block; padding: 12px 24px; background: #007cba; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #005a87; }
        pre { background: #272822; color: #f8f8f2; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .step { margin: 15px 0; padding: 10px; background: #f9f9f9; border-left: 3px solid #007cba; }
    </style>
</head>
<body>
    <div class="box">
        <h1>📦 Installation simplifiée de PhpSpreadsheet</h1>

        <?php
        if (isset($_POST['install_now'])) {
            echo '<h2>Installation en cours...</h2>';

            set_time_limit(300); // 5 minutes max

            // Étape 1 : Créer le dossier vendor
            echo '<div class="step">Étape 1 : Création du dossier vendor...</div>';
            if (!file_exists($vendor_dir)) {
                if (mkdir($vendor_dir, 0755, true)) {
                    echo '<div class="success">✅ Dossier vendor créé</div>';
                } else {
                    echo '<div class="error">❌ Impossible de créer le dossier vendor</div>';
                    echo '<p>Vérifiez les permissions du serveur.</p>';
                    exit;
                }
            } else {
                echo '<div class="success">✅ Dossier vendor existe déjà</div>';
            }

            // Étape 2 : Télécharger PhpSpreadsheet
            echo '<div class="step">Étape 2 : Téléchargement de PhpSpreadsheet...</div>';
            flush();

            $zip_url = 'https://github.com/PHPOffice/PhpSpreadsheet/archive/refs/tags/1.29.0.zip';
            $zip_file = $plugin_dir . '/temp.zip';

            $context = stream_context_create([
                'http' => [
                    'follow_location' => true,
                    'max_redirects' => 5,
                    'timeout' => 60,
                    'user_agent' => 'WordPress-Plugin'
                ]
            ]);

            $zip_data = @file_get_contents($zip_url, false, $context);

            if ($zip_data === false) {
                echo '<div class="error">❌ Échec du téléchargement</div>';
                echo '<p>Votre serveur ne peut pas télécharger depuis GitHub.</p>';
                echo '<p><strong>Solution :</strong> Téléchargez manuellement le plugin avec vendor inclus.</p>';
                exit;
            }

            file_put_contents($zip_file, $zip_data);
            echo '<div class="success">✅ Téléchargement terminé (' . number_format(strlen($zip_data) / 1024 / 1024, 2) . ' MB)</div>';
            flush();

            // Étape 3 : Extraire le ZIP
            echo '<div class="step">Étape 3 : Extraction...</div>';
            flush();

            if (!class_exists('ZipArchive')) {
                echo '<div class="error">❌ ZipArchive non disponible sur ce serveur</div>';
                unlink($zip_file);
                exit;
            }

            $zip = new ZipArchive;
            if ($zip->open($zip_file) === TRUE) {
                $temp_extract = $plugin_dir . '/temp_extract';
                if (!file_exists($temp_extract)) {
                    mkdir($temp_extract, 0755, true);
                }

                $zip->extractTo($temp_extract);
                $zip->close();

                echo '<div class="success">✅ Extraction terminée</div>';
                flush();

                // Étape 4 : Déplacer les fichiers
                echo '<div class="step">Étape 4 : Installation des fichiers...</div>';
                flush();

                $extracted_folder = $temp_extract . '/PhpSpreadsheet-1.29.0';
                $destination = $vendor_dir . '/phpoffice/phpspreadsheet';

                if (!file_exists($vendor_dir . '/phpoffice')) {
                    mkdir($vendor_dir . '/phpoffice', 0755, true);
                }

                if (file_exists($extracted_folder)) {
                    // Fonction récursive pour copier un dossier
                    function copy_dir($src, $dst) {
                        $dir = opendir($src);
                        @mkdir($dst);
                        while (false !== ($file = readdir($dir))) {
                            if (($file != '.') && ($file != '..')) {
                                if (is_dir($src . '/' . $file)) {
                                    copy_dir($src . '/' . $file, $dst . '/' . $file);
                                } else {
                                    copy($src . '/' . $file, $dst . '/' . $file);
                                }
                            }
                        }
                        closedir($dir);
                    }

                    copy_dir($extracted_folder, $destination);
                    echo '<div class="success">✅ Fichiers installés</div>';
                } else {
                    echo '<div class="error">❌ Dossier extrait introuvable</div>';
                }

                // Nettoyage
                echo '<div class="step">Étape 5 : Nettoyage...</div>';

                function delete_dir($dir) {
                    if (!file_exists($dir)) return true;
                    if (!is_dir($dir)) return unlink($dir);
                    foreach (scandir($dir) as $item) {
                        if ($item == '.' || $item == '..') continue;
                        if (!delete_dir($dir . DIRECTORY_SEPARATOR . $item)) return false;
                    }
                    return rmdir($dir);
                }

                delete_dir($temp_extract);
                unlink($zip_file);

                echo '<div class="success">✅ Nettoyage terminé</div>';

                // Étape 6 : Créer l'autoloader
                echo '<div class="step">Étape 6 : Création de l\'autoloader...</div>';

                $autoload_code = <<<'AUTOLOAD'
<?php
// Autoloader pour PhpSpreadsheet
spl_autoload_register(function ($class) {
    if (strpos($class, 'PhpOffice\\PhpSpreadsheet\\') === 0) {
        $file = __DIR__ . '/phpoffice/phpspreadsheet/src/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
AUTOLOAD;

                file_put_contents($autoload_file, $autoload_code);

                if (file_exists($autoload_file)) {
                    echo '<div class="success">✅ Autoloader créé</div>';
                    echo '<div class="success"><h2>🎉 Installation terminée avec succès !</h2></div>';
                    echo '<div class="info">';
                    echo '<h3>Prochaines étapes :</h3>';
                    echo '<ol>';
                    echo '<li>Fermez cet onglet</li>';
                    echo '<li>Retournez dans WordPress</li>';
                    echo '<li>Rafraîchissez la page (F5)</li>';
                    echo '<li>Le message d\'erreur devrait avoir disparu</li>';
                    echo '<li>Testez l\'export : WooCommerce > Export Mensuel</li>';
                    echo '</ol>';
                    echo '</div>';
                    echo '<p><a href="' . admin_url('admin.php?page=wc-monthly-export') . '" class="btn">Aller à l\'export mensuel</a></p>';
                } else {
                    echo '<div class="error">❌ Impossible de créer l\'autoloader</div>';
                }

            } else {
                echo '<div class="error">❌ Impossible d\'ouvrir le fichier ZIP</div>';
                unlink($zip_file);
            }

        } else {
            // Afficher le formulaire
            ?>

            <div class="info">
                <h3>ℹ️ À propos de cette installation</h3>
                <p>Ce script va :</p>
                <ol>
                    <li>Télécharger PhpSpreadsheet depuis GitHub</li>
                    <li>L'installer dans le dossier <code>vendor/</code></li>
                    <li>Créer l'autoloader nécessaire</li>
                </ol>
                <p><strong>Durée estimée :</strong> 1-2 minutes</p>
            </div>

            <form method="post">
                <button type="submit" name="install_now" class="btn">
                    🚀 Lancer l'installation maintenant
                </button>
            </form>

            <div class="info" style="margin-top: 30px;">
                <h3>📊 État actuel</h3>
                <p><strong>Dossier plugin :</strong> <code><?php echo esc_html($plugin_dir); ?></code></p>
                <p><strong>Dossier vendor :</strong> <?php echo file_exists($vendor_dir) ? '✅ Existe' : '❌ N\'existe pas'; ?></p>
                <p><strong>Autoload.php :</strong> <?php echo file_exists($autoload_file) ? '✅ Existe' : '❌ N\'existe pas'; ?></p>
            </div>

            <?php
        }
        ?>
    </div>
</body>
</html>
