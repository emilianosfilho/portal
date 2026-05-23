<?php
/**
 * Script: Exportação de Radar de Vendas para Excel
 * Tecnologia: PHP 8 Procedural / WinThor / PhpSpreadsheet
 */

session_start();

// Aumenta o limite de memória para exportações maiores
ini_set('memory_limit', '512M');

// Importação via Composer
require __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

// 1. Validação da Sessão WinThor
if (!isset($_SESSION['RADAR']) || empty($_SESSION['RADAR'])) {
  $_SESSION['RADAR'] = [];
}

// 2. Filtragem de Itens Selecionados
// Mantendo a lógica procedural de filtragem
$dadosSelecionados = array_filter($_SESSION['RADAR'], function($item) {
    return (isset($item['SELECIONADO']) && 
           ($item['SELECIONADO'] === true || $item['SELECIONADO'] == 'true' || $item['SELECIONADO'] == 1));
});

if (empty($dadosSelecionados)) {
    echo "<script>
            alert('Nenhum item selecionado. Marque os produtos desejados no Radar.');
            window.close();
          </script>";
    exit;
}

// 3. Inicialização do Objeto Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Radar de Vendas');

// 4. Definição e Estilização do Cabeçalho
$headers = ['ORIGEM', 'NUM. ORIGINAL', 'DESCRIÇÃO', 'MARCA', 'VIDE', 'WINT', 'ESTOQUE', 'PREÇO'];
$currentCol = 1; // Índice numérico para facilitar manipulação procedural

foreach ($headers as $index => $title) {
    $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentCol) . '1';
    $sheet->setCellValue($cell, $title);
    
    // Estilização robusta
    $styleArray = [
        'font' => [
            'bold' => true,
            'color' => ['argb' => Color::COLOR_WHITE],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FF444444'],
        ],
    ];
    
    $sheet->getStyle($cell)->applyFromArray($styleArray);
    $currentCol++;
}

// 5. Preenchimento dos Dados com Sanitização
$rowIdx = 2;
foreach ($dadosSelecionados as $item) {
    $sheet->setCellValue('A' . $rowIdx, $item['ORIGEM'] ?? '');
    $sheet->setCellValue('B' . $rowIdx, $item['NUMORIGINAL'] ?? '');
    $sheet->setCellValue('C' . $rowIdx, $item['DESCRICAO'] ?? '');
    $sheet->setCellValue('D' . $rowIdx, $item['MARCA'] ?? '');
    $sheet->setCellValue('E' . $rowIdx, $item['VIDE'] ?? '');
    $sheet->setCellValue('F' . $rowIdx, $item['WINT'] ?? '');
    
    // Tratamento de tipos para WinThor (Oracle numérico)
    $estoque = (float) str_replace(',', '.', ($item['QTDISPONIVEL'] ?? 0));
    $preco   = (float) str_replace(',', '.', ($item['PVENDA'] ?? 0));
    
    $sheet->setCellValue('G' . $rowIdx, $estoque);
    $sheet->setCellValue('H' . $rowIdx, $preco);

    // Formatação monetária (Padrão Contábil Brasileiro)
    $sheet->getStyle('H' . $rowIdx)->getNumberFormat()->setFormatCode('R$ #,##0.00');
    
    $rowIdx++;
}

// Ajuste automático de colunas (A até H)
for ($i = 1; $i <= 8; $i++) {
    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
    $sheet->getColumnDimension($colLetter)->setAutoSize(true);
}

// 6. Preparação do Output (Headers HTTP)
$fileName = 'radar_vendas_' . date('Ymd_Hi') . '.xlsx';

// Limpa qualquer buffer de saída para evitar corrupção do arquivo
if (ob_get_length()) ob_end_clean();

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

// 7. Geração do arquivo
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;