<?php
/**
 * Plugin Name: WooCommerce Monthly Export
 * Plugin URI: https://github.com/davidrieu/anthios
 * Description: Génère des exports comptables mensuels au format Excel pour WooCommerce, similaires aux exports Prestashop
 * Version: 1.0.0
 * Author: David Rieu
 * Author URI: https://github.com/davidrieu
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wc-monthly-export
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.5
 */

// Sécurité : Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

// Définir les constantes du plugin
define('WC_MONTHLY_EXPORT_VERSION', '1.0.0');
define('WC_MONTHLY_EXPORT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WC_MONTHLY_EXPORT_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Vérifier si WooCommerce est actif
 */
function wc_monthly_export_check_woocommerce() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'wc_monthly_export_woocommerce_missing_notice');
        deactivate_plugins(plugin_basename(__FILE__));
        return false;
    }
    return true;
}
add_action('plugins_loaded', 'wc_monthly_export_check_woocommerce');

/**
 * Notice si WooCommerce n'est pas installé
 */
function wc_monthly_export_woocommerce_missing_notice() {
    ?>
    <div class="error">
        <p><?php _e('WooCommerce Monthly Export requiert WooCommerce pour fonctionner. Veuillez installer et activer WooCommerce.', 'wc-monthly-export'); ?></p>
    </div>
    <?php
}

/**
 * Classe principale du plugin
 */
class WC_Monthly_Export {

    private static $instance = null;

    /**
     * Singleton
     */
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructeur
     */
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }

    /**
     * Initialiser les hooks WordPress
     */
    private function init_hooks() {
        // Ajouter le menu dans l'administration
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // Enregistrer les styles et scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Traiter l'export
        add_action('admin_init', array($this, 'process_export'));
    }

    /**
     * Charger les dépendances
     */
    private function load_dependencies() {
        require_once WC_MONTHLY_EXPORT_PLUGIN_DIR . 'includes/class-export-generator.php';
        require_once WC_MONTHLY_EXPORT_PLUGIN_DIR . 'includes/class-data-extractor.php';
    }

    /**
     * Ajouter le menu dans l'administration WooCommerce
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __('Export Mensuel', 'wc-monthly-export'),
            __('Export Mensuel', 'wc-monthly-export'),
            'manage_woocommerce',
            'wc-monthly-export',
            array($this, 'render_admin_page')
        );
    }

    /**
     * Enregistrer les styles et scripts pour l'administration
     */
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'woocommerce_page_wc-monthly-export') {
            return;
        }

        wp_enqueue_style(
            'wc-monthly-export-admin',
            WC_MONTHLY_EXPORT_PLUGIN_URL . 'assets/admin.css',
            array(),
            WC_MONTHLY_EXPORT_VERSION
        );
    }

    /**
     * Afficher la page d'administration
     */
    public function render_admin_page() {
        // Vérifier les permissions
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires pour accéder à cette page.'));
        }

        include WC_MONTHLY_EXPORT_PLUGIN_DIR . 'includes/admin-page.php';
    }

    /**
     * Traiter la demande d'export
     */
    public function process_export() {
        // Vérifier si c'est une demande d'export
        if (!isset($_POST['wc_monthly_export_nonce']) ||
            !wp_verify_nonce($_POST['wc_monthly_export_nonce'], 'wc_monthly_export_action')) {
            return;
        }

        // Vérifier les permissions
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires.'));
        }

        // Récupérer le mois et l'année
        $month = isset($_POST['export_month']) ? intval($_POST['export_month']) : date('m');
        $year = isset($_POST['export_year']) ? intval($_POST['export_year']) : date('Y');

        // Générer l'export
        try {
            $generator = new WC_Monthly_Export_Generator();
            $file_path = $generator->generate_export($month, $year);

            // Télécharger le fichier
            $this->download_file($file_path);

        } catch (Exception $e) {
            wp_die(__('Erreur lors de la génération de l\'export: ', 'wc-monthly-export') . $e->getMessage());
        }
    }

    /**
     * Télécharger le fichier généré
     */
    private function download_file($file_path) {
        if (!file_exists($file_path)) {
            wp_die(__('Le fichier d\'export n\'existe pas.'));
        }

        $filename = basename($file_path);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($file_path));
        header('Cache-Control: max-age=0');

        readfile($file_path);

        // Nettoyer le fichier temporaire
        unlink($file_path);

        exit;
    }
}

// Initialiser le plugin
function wc_monthly_export_init() {
    if (class_exists('WooCommerce')) {
        WC_Monthly_Export::get_instance();
    }
}
add_action('plugins_loaded', 'wc_monthly_export_init');
