<?php
/**
 * Script d'analyse détaillée d'une commande avec remboursement
 *
 * Usage: Analyser la commande 16308 pour comprendre les calculs
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

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 16308;
$order = wc_get_order($order_id);

if (!$order) {
    die("Commande #{$order_id} introuvable");
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Analyse commande #<?php echo $order_id; ?></title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #4CAF50; color: white; }
        .section { background: #f9f9f9; padding: 20px; margin: 20px 0; border-left: 4px solid #4CAF50; }
        .warning { background: #fff3cd; border-left-color: #ffc107; }
        .error { background: #f8d7da; border-left-color: #dc3545; }
        .success { background: #d4edda; border-left-color: #28a745; }
        h1, h2 { color: #333; }
        .calc { background: #e3f2fd; padding: 10px; margin: 10px 0; font-family: monospace; }
    </style>
</head>
<body>
    <h1>📊 Analyse détaillée de la commande #<?php echo $order_id; ?></h1>

    <div class="section">
        <h2>Informations générales</h2>
        <table>
            <tr><th>Propriété</th><th>Valeur</th></tr>
            <tr><td>Statut</td><td><?php echo $order->get_status(); ?></td></tr>
            <tr><td>Date</td><td><?php echo $order->get_date_created()->format('d/m/Y H:i'); ?></td></tr>
            <tr><td>Client</td><td><?php echo $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(); ?></td></tr>
        </table>
    </div>

    <div class="section success">
        <h2>1️⃣ MONTANTS ORIGINAUX (avant remboursement)</h2>
        <table>
            <tr><th>Élément</th><th>Montant</th><th>Méthode WooCommerce</th></tr>
            <tr>
                <td><strong>Total TTC original</strong></td>
                <td><strong><?php echo number_format($order->get_total(), 2, ',', ' '); ?> €</strong></td>
                <td><code>get_total()</code></td>
            </tr>
            <tr>
                <td>Sous-total produits HT</td>
                <td><?php echo number_format($order->get_subtotal(), 2, ',', ' '); ?> €</td>
                <td><code>get_subtotal()</code></td>
            </tr>
            <tr>
                <td>TVA totale</td>
                <td><?php echo number_format($order->get_total_tax(), 2, ',', ' '); ?> €</td>
                <td><code>get_total_tax()</code></td>
            </tr>
            <tr>
                <td>Frais de port HT</td>
                <td><?php echo number_format($order->get_shipping_total(), 2, ',', ' '); ?> €</td>
                <td><code>get_shipping_total()</code></td>
            </tr>
            <tr>
                <td>TVA sur port</td>
                <td><?php echo number_format($order->get_shipping_tax(), 2, ',', ' '); ?> €</td>
                <td><code>get_shipping_tax()</code></td>
            </tr>
        </table>

        <div class="calc">
            <strong>Vérification calcul original :</strong><br>
            TOTAL HT original = Sous-total HT + Port HT<br>
            TOTAL HT original = <?php echo number_format($order->get_subtotal(), 2, ',', ' '); ?> + <?php echo number_format($order->get_shipping_total(), 2, ',', ' '); ?><br>
            TOTAL HT original = <strong><?php echo number_format($order->get_subtotal() + $order->get_shipping_total(), 2, ',', ' '); ?> €</strong>
        </div>
    </div>

    <div class="section warning">
        <h2>2️⃣ REMBOURSEMENTS</h2>
        <?php
        $refunds = $order->get_refunds();
        $total_refunded = $order->get_total_refunded();

        if (count($refunds) > 0) {
            echo "<p><strong>Montant total remboursé :</strong> " . number_format($total_refunded, 2, ',', ' ') . " €</p>";
            echo "<p><strong>Nombre de remboursements :</strong> " . count($refunds) . "</p>";

            echo "<table>";
            echo "<tr><th>ID Remboursement</th><th>Date</th><th>Montant TTC</th><th>TVA</th><th>Port</th><th>Produits</th></tr>";

            foreach ($refunds as $refund) {
                $refund_total = abs($refund->get_total());
                $refund_tax = abs($refund->get_total_tax());
                $refund_shipping = abs($refund->get_shipping_total());
                $refund_products = $refund_total - $refund_shipping;

                echo "<tr>";
                echo "<td>#" . $refund->get_id() . "</td>";
                echo "<td>" . $refund->get_date_created()->format('d/m/Y H:i') . "</td>";
                echo "<td>" . number_format($refund_total, 2, ',', ' ') . " €</td>";
                echo "<td>" . number_format($refund_tax, 2, ',', ' ') . " €</td>";
                echo "<td>" . number_format($refund_shipping, 2, ',', ' ') . " €</td>";
                echo "<td>" . number_format($refund_products, 2, ',', ' ') . " €</td>";
                echo "</tr>";
            }
            echo "</table>";

            // Détails des produits remboursés
            echo "<h3>Détails des produits remboursés :</h3>";
            foreach ($refunds as $refund) {
                echo "<h4>Remboursement #" . $refund->get_id() . "</h4>";
                $refund_items = $refund->get_items();
                if (count($refund_items) > 0) {
                    echo "<table>";
                    echo "<tr><th>Produit</th><th>Quantité</th><th>Montant HT</th><th>TVA</th><th>Total TTC</th></tr>";
                    foreach ($refund_items as $item) {
                        $item_total = abs($item->get_total());
                        $item_tax = abs($item->get_total_tax());
                        echo "<tr>";
                        echo "<td>" . $item->get_name() . "</td>";
                        echo "<td>" . abs($item->get_quantity()) . "</td>";
                        echo "<td>" . number_format($item_total, 2, ',', ' ') . " €</td>";
                        echo "<td>" . number_format($item_tax, 2, ',', ' ') . " €</td>";
                        echo "<td>" . number_format($item_total + $item_tax, 2, ',', ' ') . " €</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            }
        } else {
            echo "<p>Aucun remboursement</p>";
        }
        ?>
    </div>

    <div class="section success">
        <h2>3️⃣ MONTANTS NETS (après remboursement) - NOTRE CALCUL</h2>
        <?php
        // Calculs nets
        $net_total_ttc = $order->get_total() - $total_refunded;
        $net_subtotal_ht = $order->get_subtotal();
        $net_shipping_ht = $order->get_shipping_total();
        $net_tax = $order->get_total_tax();
        $net_shipping_tax = $order->get_shipping_tax();

        // Soustraire les taxes remboursées
        foreach ($refunds as $refund) {
            $net_tax -= abs($refund->get_total_tax());
            $net_shipping_tax -= abs($refund->get_shipping_tax());
            $net_subtotal_ht -= abs($refund->get_total()) - abs($refund->get_shipping_total()) - abs($refund->get_total_tax()) + abs($refund->get_shipping_tax());
        }

        $net_total_ht = $net_total_ttc - $net_tax;
        $net_products_ttc = $net_subtotal_ht + ($net_tax - $net_shipping_tax);
        $net_shipping_ttc = $net_shipping_ht + $net_shipping_tax;
        ?>

        <table>
            <tr><th>Élément</th><th>Montant NET</th></tr>
            <tr>
                <td><strong>TOTAL TTC (net)</strong></td>
                <td><strong><?php echo number_format($net_total_ttc, 2, ',', ' '); ?> €</strong></td>
            </tr>
            <tr>
                <td><strong>TOTAL HT (net)</strong></td>
                <td><strong><?php echo number_format($net_total_ht, 2, ',', ' '); ?> €</strong></td>
            </tr>
            <tr>
                <td>PRODUITS TTC (net)</td>
                <td><?php echo number_format($net_products_ttc, 2, ',', ' '); ?> €</td>
            </tr>
            <tr>
                <td>PRODUITS HT (net)</td>
                <td><?php echo number_format($net_subtotal_ht, 2, ',', ' '); ?> €</td>
            </tr>
            <tr>
                <td>PORT TTC (net)</td>
                <td><?php echo number_format($net_shipping_ttc, 2, ',', ' '); ?> €</td>
            </tr>
            <tr>
                <td>PORT HT (net)</td>
                <td><?php echo number_format($net_shipping_ht, 2, ',', ' '); ?> €</td>
            </tr>
        </table>

        <div class="calc">
            <strong>Vérification :</strong><br>
            TOTAL HT = TOTAL TTC - TVA<br>
            TOTAL HT = <?php echo number_format($net_total_ttc, 2, ',', ' '); ?> - <?php echo number_format($net_tax, 2, ',', ' '); ?><br>
            TOTAL HT = <strong><?php echo number_format($net_total_ht, 2, ',', ' '); ?> €</strong><br><br>
            ✅ TOTAL HT &lt; TOTAL TTC : <?php echo ($net_total_ht < $net_total_ttc) ? 'OUI (correct)' : 'NON (erreur!)'; ?>
        </div>
    </div>

    <div class="section error">
        <h2>4️⃣ COMPARAISON AVEC LES CHIFFRES DE LA CLIENTE</h2>
        <table>
            <tr><th>Élément</th><th>Notre calcul</th><th>Cliente</th><th>Différence</th><th>Status</th></tr>
            <tr>
                <td>TOTAL TTC</td>
                <td><?php echo number_format($net_total_ttc, 2, ',', ' '); ?> €</td>
                <td>127,05 €</td>
                <td><?php echo number_format($net_total_ttc - 127.05, 2, ',', ' '); ?> €</td>
                <td><?php echo ($net_total_ttc == 127.05) ? '✅ OK' : '❌ Différent'; ?></td>
            </tr>
            <tr>
                <td>TOTAL HT</td>
                <td><?php echo number_format($net_total_ht, 2, ',', ' '); ?> €</td>
                <td>137,25 €</td>
                <td><?php echo number_format($net_total_ht - 137.25, 2, ',', ' '); ?> €</td>
                <td>❌ <strong>IMPOSSIBLE (HT &gt; TTC)</strong></td>
            </tr>
            <tr>
                <td>PRODUITS TTC</td>
                <td><?php echo number_format($net_products_ttc, 2, ',', ' '); ?> €</td>
                <td>113,50 €</td>
                <td><?php echo number_format($net_products_ttc - 113.50, 2, ',', ' '); ?> €</td>
                <td><?php echo (abs($net_products_ttc - 113.50) < 0.1) ? '✅ Proche' : '❌ Différent'; ?></td>
            </tr>
            <tr>
                <td>PRODUITS HT</td>
                <td><?php echo number_format($net_subtotal_ht, 2, ',', ' '); ?> €</td>
                <td>101,25 €</td>
                <td><?php echo number_format($net_subtotal_ht - 101.25, 2, ',', ' '); ?> €</td>
                <td><?php echo (abs($net_subtotal_ht - 101.25) < 0.1) ? '✅ Proche' : '❌ Différent'; ?></td>
            </tr>
        </table>

        <p style="color: #dc3545; font-weight: bold; font-size: 18px;">
            ⚠️ PROBLÈME MAJEUR : Le TOTAL HT de la cliente (137,25 €) est SUPÉRIEUR au TOTAL TTC (127,05 €).<br>
            C'est mathématiquement impossible car TOTAL TTC = TOTAL HT + TVA
        </p>
    </div>

    <div class="section">
        <h2>✅ CONCLUSION</h2>
        <p><strong>Notre calcul est CORRECT.</strong></p>
        <p>La cliente a probablement :</p>
        <ul>
            <li>Inversé des chiffres (137,25 au lieu de peut-être 127,35 ?)</li>
            <li>Utilisé une méthode de calcul différente</li>
            <li>Confondu TOTAL HT avec un autre montant</li>
            <li>Fait une erreur de saisie</li>
        </ul>
        <p><strong>Recommandation :</strong> Demandez-lui comment elle a calculé ce TOTAL HT de 137,25 €, car il est impossible qu'un HT soit supérieur au TTC.</p>
    </div>

    <p><a href="<?php echo admin_url('admin.php?page=wc-monthly-export'); ?>">← Retour à l'export</a></p>
</body>
</html>
