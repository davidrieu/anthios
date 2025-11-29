<?php
/**
 * Template de la page d'administration
 *
 * @package WC_Monthly_Export
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

// Récupérer les années disponibles
$current_year = date('Y');
$years = range($current_year, $current_year - 5);

// Récupérer le mois actuel
$current_month = date('m');

// Liste des mois
$months = array(
    '01' => __('Janvier', 'wc-monthly-export'),
    '02' => __('Février', 'wc-monthly-export'),
    '03' => __('Mars', 'wc-monthly-export'),
    '04' => __('Avril', 'wc-monthly-export'),
    '05' => __('Mai', 'wc-monthly-export'),
    '06' => __('Juin', 'wc-monthly-export'),
    '07' => __('Juillet', 'wc-monthly-export'),
    '08' => __('Août', 'wc-monthly-export'),
    '09' => __('Septembre', 'wc-monthly-export'),
    '10' => __('Octobre', 'wc-monthly-export'),
    '11' => __('Novembre', 'wc-monthly-export'),
    '12' => __('Décembre', 'wc-monthly-export'),
);
?>

<div class="wrap wc-monthly-export-wrap">
    <h1><?php _e('Export Comptable Mensuel', 'wc-monthly-export'); ?></h1>

    <div class="wc-monthly-export-container">
        <div class="wc-export-card">
            <h2><?php _e('Générer un export Excel', 'wc-monthly-export'); ?></h2>
            <p class="description">
                <?php _e('Générez un export comptable mensuel au format Excel compatible avec votre comptabilité.', 'wc-monthly-export'); ?>
            </p>

            <form method="post" action="" class="wc-export-form">
                <?php wp_nonce_field('wc_monthly_export_action', 'wc_monthly_export_nonce'); ?>

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="export_month"><?php _e('Mois', 'wc-monthly-export'); ?></label>
                            </th>
                            <td>
                                <select name="export_month" id="export_month" class="regular-text">
                                    <?php foreach ($months as $value => $label) : ?>
                                        <option value="<?php echo esc_attr($value); ?>" <?php selected($value, $current_month); ?>>
                                            <?php echo esc_html($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description">
                                    <?php _e('Sélectionnez le mois à exporter', 'wc-monthly-export'); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="export_year"><?php _e('Année', 'wc-monthly-export'); ?></label>
                            </th>
                            <td>
                                <select name="export_year" id="export_year" class="regular-text">
                                    <?php foreach ($years as $year) : ?>
                                        <option value="<?php echo esc_attr($year); ?>" <?php selected($year, $current_year); ?>>
                                            <?php echo esc_html($year); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description">
                                    <?php _e('Sélectionnez l\'année à exporter', 'wc-monthly-export'); ?>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary button-hero">
                        <span class="dashicons dashicons-download"></span>
                        <?php _e('Générer l\'export Excel', 'wc-monthly-export'); ?>
                    </button>
                </p>
            </form>
        </div>

        <div class="wc-export-info">
            <h3><?php _e('À propos de l\'export', 'wc-monthly-export'); ?></h3>

            <div class="wc-info-section">
                <h4><?php _e('Contenu de l\'export', 'wc-monthly-export'); ?></h4>
                <p><?php _e('L\'export Excel contient deux feuilles :', 'wc-monthly-export'); ?></p>
                <ul>
                    <li><strong><?php _e('Commandes validées', 'wc-monthly-export'); ?></strong> :
                        <?php _e('Toutes les commandes avec paiement confirmé', 'wc-monthly-export'); ?>
                    </li>
                    <li><strong><?php _e('Non validées', 'wc-monthly-export'); ?></strong> :
                        <?php _e('Commandes en attente de paiement ou en attente', 'wc-monthly-export'); ?>
                    </li>
                </ul>
            </div>

            <div class="wc-info-section">
                <h4><?php _e('Colonnes exportées', 'wc-monthly-export'); ?></h4>
                <ul>
                    <li><?php _e('Informations de commande (ID, référence, date, statut)', 'wc-monthly-export'); ?></li>
                    <li><?php _e('Informations client (nom, type, société)', 'wc-monthly-export'); ?></li>
                    <li><?php _e('Montants TTC et HT (produits, port, réductions)', 'wc-monthly-export'); ?></li>
                    <li><?php _e('Répartition par taux de TVA (20%, 10%, 5.5%, 0%)', 'wc-monthly-export'); ?></li>
                    <li><?php _e('Mode de paiement', 'wc-monthly-export'); ?></li>
                    <li><?php _e('Indicateurs (nouveau client, badge succès)', 'wc-monthly-export'); ?></li>
                </ul>
            </div>

            <div class="wc-info-section">
                <h4><?php _e('Utilisation', 'wc-monthly-export'); ?></h4>
                <p><?php _e('Cet export vous permet de :', 'wc-monthly-export'); ?></p>
                <ul>
                    <li><?php _e('Saisir vos ventes en comptabilité', 'wc-monthly-export'); ?></li>
                    <li><?php _e('Calculer la TVA à reverser', 'wc-monthly-export'); ?></li>
                    <li><?php _e('Suivre votre chiffre d\'affaires mensuel', 'wc-monthly-export'); ?></li>
                    <li><?php _e('Analyser les performances commerciales', 'wc-monthly-export'); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.wc-monthly-export-wrap {
    max-width: 1200px;
}

.wc-monthly-export-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 20px;
}

.wc-export-card,
.wc-export-info {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.wc-export-card h2 {
    margin-top: 0;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.wc-export-form .form-table {
    margin-top: 20px;
}

.wc-export-form .button-hero {
    padding: 10px 20px;
    height: auto;
    font-size: 16px;
}

.wc-export-form .button-hero .dashicons {
    font-size: 20px;
    vertical-align: middle;
    margin-right: 5px;
}

.wc-export-info h3 {
    margin-top: 0;
    border-bottom: 2px solid #2271b1;
    padding-bottom: 10px;
    color: #2271b1;
}

.wc-info-section {
    margin-bottom: 20px;
}

.wc-info-section h4 {
    margin-bottom: 10px;
    color: #50575e;
}

.wc-info-section ul {
    margin-left: 20px;
}

.wc-info-section ul li {
    margin-bottom: 5px;
}

@media screen and (max-width: 960px) {
    .wc-monthly-export-container {
        grid-template-columns: 1fr;
    }
}
</style>
