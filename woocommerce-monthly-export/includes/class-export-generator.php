<?php
/**
 * Classe pour générer les fichiers Excel
 *
 * @package WC_Monthly_Export
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

// Charger PhpSpreadsheet si disponible
if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
    require_once WC_MONTHLY_EXPORT_PLUGIN_DIR . 'vendor/autoload.php';
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * Class WC_Monthly_Export_Generator
 */
class WC_Monthly_Export_Generator {

    /**
     * Colonnes de l'export (identiques à Prestashop)
     */
    private $columns = array(
        'id_order',
        'reference',
        'date_cde',
        'STATUT',
        'customer',
        'Type_Client',
        'SOCIETE',
        'TOTAL TTC',
        'TOTAL HT',
        'PRODUITS TTC',
        'PRODUITS HT',
        'HT 20',
        'HT 10',
        'HT 5.5',
        'HT 0',
        'PORT TTC',
        'PORT HT',
        'REDUCTION TTC',
        'REDUCTION HT',
        'payment',
        'new',
        'badge_success',
    );

    /**
     * Générer l'export Excel
     *
     * @param int $month
     * @param int $year
     * @return string Chemin du fichier généré
     * @throws Exception
     */
    public function generate_export($month, $year) {
        // Extraire les données
        $extractor = new WC_Monthly_Export_Data_Extractor();
        $data = $extractor->get_orders_by_month($month, $year);

        // Créer le fichier Excel
        $spreadsheet = new Spreadsheet();

        // Supprimer la feuille par défaut
        $spreadsheet->removeSheetByIndex(0);

        // Créer les deux feuilles
        $this->create_sheet($spreadsheet, 'Commandes validées', $data['validated'], true);
        $this->create_sheet($spreadsheet, 'Non validé', $data['pending'], false);

        // Définir la première feuille comme active
        $spreadsheet->setActiveSheetIndex(0);

        // Générer le nom du fichier
        $month_name = $this->get_month_name($month);
        $filename = sprintf('WooCommerce_%s_%d.xlsx', $month_name, $year);
        $filepath = sys_get_temp_dir() . '/' . $filename;

        // Sauvegarder le fichier
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);

        return $filepath;
    }

    /**
     * Créer une feuille dans le classeur
     *
     * @param Spreadsheet $spreadsheet
     * @param string $title
     * @param array $data
     * @param bool $first_sheet
     */
    private function create_sheet($spreadsheet, $title, $data, $first_sheet) {
        // Créer ou récupérer la feuille
        if ($first_sheet) {
            $sheet = $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($spreadsheet->getActiveSheetIndex());
        } else {
            $sheet = $spreadsheet->createSheet();
        }

        $sheet->setTitle($title);

        // Écrire les en-têtes
        $col = 'A';
        foreach ($this->columns as $column) {
            $sheet->setCellValue($col . '1', $column);
            $col++;
        }

        // Styler les en-têtes
        $this->style_header($sheet, count($this->columns));

        // Écrire les données
        if (!empty($data)) {
            $row = 2;
            foreach ($data as $order_data) {
                $col = 'A';
                foreach ($this->columns as $column) {
                    $value = isset($order_data[$column]) ? $order_data[$column] : '';
                    $sheet->setCellValue($col . $row, $value);
                    $col++;
                }
                $row++;
            }

            // Ajuster la largeur des colonnes
            $this->auto_size_columns($sheet, count($this->columns));
        }
    }

    /**
     * Styler la ligne d'en-tête
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param int $column_count
     */
    private function style_header($sheet, $column_count) {
        $last_column = $this->get_column_letter($column_count);
        $range = 'A1:' . $last_column . '1';

        // Couleur de fond
        $sheet->getStyle($range)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF4472C4');

        // Texte en blanc et gras
        $sheet->getStyle($range)->getFont()
            ->setBold(true)
            ->getColor()->setARGB('FFFFFFFF');

        // Centrer le texte
        $sheet->getStyle($range)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Bordures
        $sheet->getStyle($range)->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
    }

    /**
     * Ajuster automatiquement la largeur des colonnes
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @param int $column_count
     */
    private function auto_size_columns($sheet, $column_count) {
        for ($i = 1; $i <= $column_count; $i++) {
            $column = $this->get_column_letter($i);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    /**
     * Obtenir la lettre de colonne à partir d'un index
     *
     * @param int $index Index de la colonne (1-based)
     * @return string Lettre de colonne (A, B, C, ..., AA, AB, etc.)
     */
    private function get_column_letter($index) {
        $letter = '';
        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intval($index / 26);
        }
        return $letter;
    }

    /**
     * Obtenir le nom du mois en français
     *
     * @param int $month
     * @return string
     */
    private function get_month_name($month) {
        $months = array(
            1 => '01',
            2 => '02',
            3 => '03',
            4 => '04',
            5 => '05',
            6 => '06',
            7 => '07',
            8 => '08',
            9 => '09',
            10 => '10',
            11 => '11',
            12 => '12',
        );

        return isset($months[$month]) ? $months[$month] : '00';
    }
}
