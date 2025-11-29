<?php
/**
 * Script de test et débogage pour WooCommerce Monthly Export
 *
 * Accès : votre-site.com/wp-content/plugins/woocommerce-monthly-export/debug-export.php
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

// Vérifier les permissions
if (!current_user_can('manage_options')) {
    wp_die('Vous n\'avez pas les permissions nécessaires.');
}

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('WC_MONTHLY_EXPORT_PLUGIN_DIR', plugin_dir_path(__FILE__));

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Débogage WooCommerce Monthly Export</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: #f0f0f1;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        h1 { color: #1d2327; margin-top: 0; }
        .success { color: #00a32a; background: #f0f6fc; padding: 10px; border-left: 4px solid #00a32a; margin: 10px 0; }
        .error { color: #d63638; background: #fcf0f1; padding: 10px; border-left: 4px solid #d63638; margin: 10px 0; }
        .warning { color: #dba617; background: #fcf9e8; padding: 10px; border-left: 4px solid #dba617; margin: 10px 0; }
        pre { background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .step { background: #f6f7f7; padding: 15px; margin: 15px 0; border-radius: 4px; }
        code { background: #e7e7e7; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Débogage de l'export</h1>

        <h2>Étape 1 : Vérification des dépendances</h2>
        <?php
        $autoload_file = WC_MONTHLY_EXPORT_PLUGIN_DIR . 'vendor/autoload.php';

        if (file_exists($autoload_file)) {
            echo '<div class="success">✅ Le fichier autoload.php existe</div>';
            require_once $autoload_file;
            echo '<div class="success">✅ Autoload chargé avec succès</div>';
        } else {
            echo '<div class="error">❌ Le fichier autoload.php n\'existe pas</div>';
            echo '<p>Chemin recherché : <code>' . esc_html($autoload_file) . '</code></p>';
            exit;
        }
        ?>

        <h2>Étape 2 : Vérification de PhpSpreadsheet</h2>
        <?php
        if (class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            echo '<div class="success">✅ PhpSpreadsheet est chargé</div>';
        } else {
            echo '<div class="error">❌ PhpSpreadsheet n\'est pas disponible</div>';
            exit;
        }
        ?>

        <h2>Étape 3 : Vérification de WooCommerce</h2>
        <?php
        if (class_exists('WooCommerce')) {
            echo '<div class="success">✅ WooCommerce est actif</div>';
        } else {
            echo '<div class="error">❌ WooCommerce n\'est pas actif</div>';
            exit;
        }
        ?>

        <h2>Étape 4 : Chargement des classes du plugin</h2>
        <?php
        try {
            require_once WC_MONTHLY_EXPORT_PLUGIN_DIR . 'includes/class-data-extractor.php';
            echo '<div class="success">✅ class-data-extractor.php chargé</div>';

            require_once WC_MONTHLY_EXPORT_PLUGIN_DIR . 'includes/class-export-generator.php';
            echo '<div class="success">✅ class-export-generator.php chargé</div>';
        } catch (Exception $e) {
            echo '<div class="error">❌ Erreur lors du chargement : ' . esc_html($e->getMessage()) . '</div>';
            echo '<pre>' . esc_html($e->getTraceAsString()) . '</pre>';
            exit;
        }
        ?>

        <h2>Étape 5 : Test d'extraction des données</h2>
        <?php
        try {
            $extractor = new WC_Monthly_Export_Data_Extractor();
            echo '<div class="success">✅ Data extractor créé</div>';

            $month = date('m');
            $year = date('Y');

            echo '<div class="step">Test d\'extraction pour ' . $month . '/' . $year . '</div>';

            $data = $extractor->get_orders_by_month($month, $year);

            echo '<div class="success">✅ Données extraites avec succès</div>';
            echo '<div class="step">';
            echo '<strong>Résultats :</strong><br>';
            echo 'Commandes validées : ' . count($data['validated']) . '<br>';
            echo 'Commandes non validées : ' . count($data['pending']) . '<br>';
            echo '</div>';

            if (count($data['validated']) > 0) {
                echo '<div class="step">';
                echo '<strong>Exemple de première commande :</strong>';
                echo '<pre>' . print_r($data['validated'][0], true) . '</pre>';
                echo '</div>';
            }

        } catch (Exception $e) {
            echo '<div class="error">❌ Erreur lors de l\'extraction : ' . esc_html($e->getMessage()) . '</div>';
            echo '<pre>' . esc_html($e->getTraceAsString()) . '</pre>';
            exit;
        }
        ?>

        <h2>Étape 6 : Test de génération Excel</h2>
        <?php
        try {
            $generator = new WC_Monthly_Export_Generator();
            echo '<div class="success">✅ Export generator créé</div>';

            echo '<div class="step">Génération du fichier Excel...</div>';

            $file_path = $generator->generate_export($month, $year);

            echo '<div class="success">✅ Fichier Excel généré avec succès !</div>';
            echo '<div class="step">';
            echo '<strong>Fichier créé :</strong> ' . esc_html($file_path) . '<br>';
            echo '<strong>Taille :</strong> ' . filesize($file_path) . ' octets<br>';
            echo '</div>';

            // Nettoyer
            if (file_exists($file_path)) {
                unlink($file_path);
                echo '<div class="success">✅ Fichier de test nettoyé</div>';
            }

        } catch (Exception $e) {
            echo '<div class="error">❌ Erreur lors de la génération : ' . esc_html($e->getMessage()) . '</div>';
            echo '<pre>' . esc_html($e->getTraceAsString()) . '</pre>';
            echo '<h3>Détails de l\'erreur :</h3>';
            echo '<pre>';
            echo 'Type : ' . get_class($e) . "\n";
            echo 'Fichier : ' . $e->getFile() . "\n";
            echo 'Ligne : ' . $e->getLine() . "\n";
            echo '</pre>';
            exit;
        }
        ?>

        <h2>✅ Tous les tests sont passés !</h2>
        <div class="success">
            <p>Le plugin fonctionne correctement. Si vous avez une erreur lors de l'export normal, cela peut venir de :</p>
            <ul>
                <li>Un conflit avec un autre plugin</li>
                <li>Un problème de permissions de fichiers</li>
                <li>Un timeout PHP</li>
            </ul>
        </div>

        <p><a href="<?php echo admin_url('admin.php?page=wc-monthly-export'); ?>">← Retour à l'export mensuel</a></p>
    </div>
</body>
</html>
