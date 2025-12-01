<?php
/**
 * Classe pour extraire les données des commandes WooCommerce
 *
 * @package WC_Monthly_Export
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WC_Monthly_Export_Data_Extractor
 */
class WC_Monthly_Export_Data_Extractor {

    /**
     * Récupérer les commandes pour un mois donné
     *
     * @param int $month Numéro du mois (1-12)
     * @param int $year Année
     * @return array Tableau associatif avec 'validated' et 'pending'
     */
    public function get_orders_by_month($month, $year) {
        // Dates de début et fin du mois
        $start_date = sprintf('%04d-%02d-01 00:00:00', $year, $month);
        $end_date = date('Y-m-t 23:59:59', strtotime($start_date));

        // Récupérer TOUTES les commandes du mois (tous types et tous statuts)
        $args = array(
            'limit' => -1,
            'type' => 'shop_order', // Uniquement les commandes (pas les remboursements enfants)
            'date_created' => $start_date . '...' . $end_date,
            'status' => 'any', // IMPORTANT : tous les statuts
            'orderby' => 'date',
            'order' => 'ASC',
        );

        $orders = wc_get_orders($args);

        // Séparer les commandes validées et non validées
        $validated = array();
        $pending = array();

        foreach ($orders as $order) {
            // Double vérification : s'assurer que c'est bien une commande
            if (!$order instanceof WC_Order) {
                continue;
            }

            $order_data = $this->extract_order_data($order);

            if ($this->is_validated_order($order)) {
                $validated[] = $order_data;
            } else {
                $pending[] = $order_data;
            }
        }

        return array(
            'validated' => $validated,
            'pending' => $pending,
        );
    }

    /**
     * Vérifier si une commande est validée
     *
     * @param WC_Order $order
     * @return bool
     */
    private function is_validated_order($order) {
        // Statuts considérés comme "validés" (à comptabiliser)
        $validated_statuses = array(
            'processing',    // En cours de traitement
            'completed',     // Terminée
            'on-hold',       // En attente
            'refunded',      // Remboursée (à comptabiliser aussi !)
        );

        // Statuts "non validés" (en attente de paiement ou annulées)
        $pending_statuses = array(
            'pending',       // En attente de paiement
            'failed',        // Échouée
            'cancelled',     // Annulée
            'trash',         // Corbeille
        );

        $status = $order->get_status();

        // Si le statut est dans les validés, retourner true
        if (in_array($status, $validated_statuses)) {
            return true;
        }

        // Si le statut est dans les non validés, retourner false
        if (in_array($status, $pending_statuses)) {
            return false;
        }

        // Pour tout autre statut personnalisé, considérer comme validé par défaut
        return true;
    }

    /**
     * Extraire les données d'une commande
     *
     * @param WC_Order $order
     * @return array
     */
    private function extract_order_data($order) {
        // Informations de base
        $order_id = $order->get_id();
        $order_number = $order->get_order_number();
        $date_created = $order->get_date_created();

        // Client
        $customer_name = $this->get_customer_name($order);
        $company = $order->get_billing_company();
        $is_new_customer = $this->is_new_customer($order);

        // Statut (avec gestion des remboursements)
        $status = $this->get_order_status_label($order);

        // Vérifier si la commande a des remboursements
        $total_refunded = floatval($order->get_total_refunded());
        $has_refund = $total_refunded > 0;

        // Montants AVANT remboursement
        $total_ttc_original = floatval($order->get_total());
        $total_tax_original = floatval($order->get_total_tax());
        $subtotal_original = floatval($order->get_subtotal());
        $shipping_total_original = floatval($order->get_shipping_total());
        $shipping_tax_original = floatval($order->get_shipping_tax());

        // Si remboursement, ajuster le statut et les montants
        if ($has_refund) {
            // Modifier le statut pour indiquer le remboursement
            $is_fully_refunded = ($total_refunded >= $total_ttc_original);
            if ($is_fully_refunded) {
                $status .= ' (Remboursée)';
            } else {
                $status .= ' (Partiellement remboursée)';
            }

            // Calculer les montants remboursés par catégorie
            $refund_data = $this->get_refund_breakdown($order);

            // Montants APRÈS remboursement (ce qui reste à comptabiliser)
            $total_ttc = $total_ttc_original - $total_refunded;
            $total_tax = $total_tax_original - $refund_data['tax_refunded'];
            $subtotal = $subtotal_original - $refund_data['products_refunded'];
            $shipping_total = $shipping_total_original - $refund_data['shipping_refunded'];
            $shipping_tax = $shipping_tax_original - $refund_data['shipping_tax_refunded'];
        } else {
            // Pas de remboursement, utiliser les montants originaux
            $total_ttc = $total_ttc_original;
            $total_tax = $total_tax_original;
            $subtotal = $subtotal_original;
            $shipping_total = $shipping_total_original;
            $shipping_tax = $shipping_tax_original;
        }

        // Calculer HT à partir des TTC
        $total_ht = $total_ttc - $total_tax;
        $products_ttc = $subtotal + ($total_tax - $shipping_tax);
        $products_ht = $subtotal;
        $shipping_ttc = $shipping_total + $shipping_tax;
        $shipping_ht = $shipping_total;

        // Réductions
        $discount_ttc = floatval($order->get_total_discount());
        $discount_ht = $discount_ttc / 1.20; // Approximation

        // Répartition par taux de TVA (en tenant compte des remboursements)
        if ($has_refund) {
            $tax_breakdown = $this->get_tax_breakdown_with_refunds($order, $refund_data);
        } else {
            $tax_breakdown = $this->get_tax_breakdown($order);
        }

        // Mode de paiement
        $payment_method = $this->get_payment_method_label($order);

        // Badge success (commande payée et validée)
        $badge_success = in_array($order->get_status(), array('processing', 'completed')) ? 1 : 0;

        return array(
            'id_order' => $order_id,
            'reference' => $order_number,
            'date_cde' => $date_created ? $date_created->format('Y-m-d') : '',
            'STATUT' => $status,
            'customer' => $customer_name,
            'Type_Client' => 1, // Toujours 1 pour WooCommerce
            'SOCIETE' => !empty($company) ? $company : '',
            'TOTAL TTC' => round($total_ttc, 2),
            'TOTAL HT' => round($total_ht, 2),
            'PRODUITS TTC' => round($products_ttc, 2),
            'PRODUITS HT' => round($products_ht, 2),
            'HT 20' => round($tax_breakdown['20'], 2),
            'HT 10' => round($tax_breakdown['10'], 2),
            'HT 5.5' => round($tax_breakdown['5.5'], 2),
            'HT 0' => round($tax_breakdown['0'], 2),
            'PORT TTC' => round($shipping_ttc, 2),
            'PORT HT' => round($shipping_ht, 2),
            'REDUCTION TTC' => round($discount_ttc, 2),
            'REDUCTION HT' => round($discount_ht, 2),
            'payment' => $payment_method,
            'new' => $is_new_customer ? 1 : 0,
            'badge_success' => $badge_success,
        );
    }

    /**
     * Obtenir le nom du client
     *
     * @param WC_Order $order
     * @return string
     */
    private function get_customer_name($order) {
        $first_name = $order->get_billing_first_name();
        $last_name = $order->get_billing_last_name();

        if (!empty($first_name) || !empty($last_name)) {
            return trim($first_name . ' ' . $last_name);
        }

        return $order->get_billing_email();
    }

    /**
     * Vérifier si c'est un nouveau client
     *
     * @param WC_Order $order
     * @return bool
     */
    private function is_new_customer($order) {
        $customer_id = $order->get_customer_id();

        if (!$customer_id) {
            return true; // Invité considéré comme nouveau
        }

        // Compter le nombre de commandes du client
        $customer_orders = wc_get_orders(array(
            'customer_id' => $customer_id,
            'limit' => 2,
            'return' => 'ids',
        ));

        return count($customer_orders) <= 1;
    }

    /**
     * Obtenir le label du statut de commande
     *
     * @param WC_Order $order
     * @return string
     */
    private function get_order_status_label($order) {
        $status = $order->get_status();
        $statuses = wc_get_order_statuses();
        $status_key = 'wc-' . $status;

        if (isset($statuses[$status_key])) {
            return $statuses[$status_key];
        }

        return ucfirst($status);
    }

    /**
     * Obtenir le label du mode de paiement
     *
     * @param WC_Order $order
     * @return string
     */
    private function get_payment_method_label($order) {
        $payment_method = $order->get_payment_method();

        // Normaliser pour ne retourner que "Stripe" ou "Paypal"
        // Vérifier si c'est PayPal (différentes variantes possibles)
        if (stripos($payment_method, 'paypal') !== false || stripos($payment_method, 'ppec') !== false) {
            return 'Paypal';
        }

        // Vérifier si c'est Stripe
        if (stripos($payment_method, 'stripe') !== false) {
            return 'Stripe';
        }

        // Fallback : retourner la méthode telle quelle si ce n'est ni Stripe ni PayPal
        $payment_method_title = $order->get_payment_method_title();
        if (!empty($payment_method_title)) {
            return $payment_method_title;
        }

        return $payment_method ? $payment_method : 'Non spécifié';
    }

    /**
     * Obtenir la répartition des montants HT par taux de TVA (PRODUITS UNIQUEMENT, sans port)
     *
     * @param WC_Order $order
     * @return array
     */
    private function get_tax_breakdown($order) {
        $breakdown = array(
            '20' => 0,
            '10' => 0,
            '5.5' => 0,
            '0' => 0,
        );

        // Récupérer les taxes de la commande
        $tax_items = $order->get_items('tax');

        foreach ($tax_items as $tax_item) {
            $rate_id = $tax_item->get_rate_id();
            $tax_rate = WC_Tax::_get_tax_rate($rate_id);

            if ($tax_rate) {
                $rate_percent = floatval($tax_rate['tax_rate']);

                // IMPORTANT : Uniquement la TVA sur les PRODUITS (pas sur le port)
                $tax_amount = floatval($tax_item->get_tax_total());

                if ($tax_amount > 0) {
                    // Calculer la base HT à partir de la TVA
                    $base_ht = ($tax_amount / $rate_percent) * 100;

                    // Classifier par taux
                    if ($rate_percent >= 19 && $rate_percent <= 21) {
                        $breakdown['20'] += $base_ht;
                    } elseif ($rate_percent >= 9 && $rate_percent <= 11) {
                        $breakdown['10'] += $base_ht;
                    } elseif ($rate_percent >= 5 && $rate_percent <= 6) {
                        $breakdown['5.5'] += $base_ht;
                    }
                }
            }
        }

        // Si pas de TVA détectée, mettre tout dans la TVA 20% par défaut
        $total_ht_from_taxes = array_sum($breakdown);
        $order_subtotal = floatval($order->get_subtotal());

        if ($total_ht_from_taxes == 0 && $order_subtotal > 0) {
            // Vérifier si la commande a de la TVA
            if ($order->get_total_tax() > 0) {
                $breakdown['20'] = $order_subtotal;
            } else {
                $breakdown['0'] = $order_subtotal;
            }
        }

        return $breakdown;
    }

    /**
     * Obtenir le détail des montants remboursés
     *
     * @param WC_Order $order
     * @return array
     */
    private function get_refund_breakdown($order) {
        $refund_data = array(
            'products_refunded' => 0,
            'shipping_refunded' => 0,
            'tax_refunded' => 0,
            'shipping_tax_refunded' => 0,
            'tax_breakdown' => array('20' => 0, '10' => 0, '5.5' => 0, '0' => 0),
        );

        // Récupérer tous les remboursements de cette commande
        $refunds = $order->get_refunds();

        foreach ($refunds as $refund) {
            // Montant HT remboursé pour les produits (exclure shipping ET taxes)
            // Formula: total_refund - shipping - all_taxes + shipping_tax = products_ht_only
            $refund_data['products_refunded'] += abs(floatval($refund->get_total())) - abs(floatval($refund->get_shipping_total())) - abs(floatval($refund->get_total_tax())) + abs(floatval($refund->get_shipping_tax()));

            // Frais de port HT remboursés
            $refund_data['shipping_refunded'] += abs(floatval($refund->get_shipping_total()));

            // Taxes remboursées
            $refund_data['tax_refunded'] += abs(floatval($refund->get_total_tax()));
            $refund_data['shipping_tax_refunded'] += abs(floatval($refund->get_shipping_tax()));

            // Répartition des taxes remboursées par taux
            $refund_tax_items = $refund->get_items('tax');
            foreach ($refund_tax_items as $tax_item) {
                $rate_id = $tax_item->get_rate_id();
                $tax_rate = WC_Tax::_get_tax_rate($rate_id);

                if ($tax_rate) {
                    $rate_percent = floatval($tax_rate['tax_rate']);
                    $tax_amount = abs(floatval($tax_item->get_tax_total())); // Produits seulement

                    if ($tax_amount > 0) {
                        // Calculer la base HT remboursée
                        $base_ht_refunded = ($tax_amount / $rate_percent) * 100;

                        // Classifier par taux
                        if ($rate_percent >= 19 && $rate_percent <= 21) {
                            $refund_data['tax_breakdown']['20'] += $base_ht_refunded;
                        } elseif ($rate_percent >= 9 && $rate_percent <= 11) {
                            $refund_data['tax_breakdown']['10'] += $base_ht_refunded;
                        } elseif ($rate_percent >= 5 && $rate_percent <= 6) {
                            $refund_data['tax_breakdown']['5.5'] += $base_ht_refunded;
                        }
                    }
                }
            }
        }

        return $refund_data;
    }

    /**
     * Obtenir la répartition par taux de TVA en tenant compte des remboursements
     *
     * @param WC_Order $order
     * @param array $refund_data
     * @return array
     */
    private function get_tax_breakdown_with_refunds($order, $refund_data) {
        // Calculer la répartition normale
        $breakdown = $this->get_tax_breakdown($order);

        // Soustraire les montants remboursés par taux
        $breakdown['20'] -= $refund_data['tax_breakdown']['20'];
        $breakdown['10'] -= $refund_data['tax_breakdown']['10'];
        $breakdown['5.5'] -= $refund_data['tax_breakdown']['5.5'];
        $breakdown['0'] -= $refund_data['tax_breakdown']['0'];

        // S'assurer qu'on n'a pas de montants négatifs
        foreach ($breakdown as $key => $value) {
            if ($value < 0) {
                $breakdown[$key] = 0;
            }
        }

        return $breakdown;
    }
}
