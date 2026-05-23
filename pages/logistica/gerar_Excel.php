<?php
session_start();
require_once __DIR__ . "/../conf/define.php";
require_once __DIR__ . "/../conf/functions.php";
require_once __DIR__ . '/../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_SESSION['EXCEL']) || !is_array($_SESSION['EXCEL']['lista']) ) {
   throw new Exception("Nenhum dado encontrado para gerar planilha Excel");
}

/* ==========================
   CONFIGURAÇÕES DE ESTILO
========================== */
$styleCab = [
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => 'center'],
];

$styleReg = [
    'alignment' => ['horizontal' => 'left'],
];

/* ==========================
   CRIA PLANILHA
========================== */
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

/* ==========================
   CABEÇALHO
========================== */
$cabecalho = array_keys(reset($_SESSION['EXCEL']['lista']));
$sheet->fromArray($cabecalho, null, 'A1');

$ultimaColuna = decodeExcelColuna(count($cabecalho));
$sheet->getStyle("A1:{$ultimaColuna}1")->applyFromArray($styleCab);
$sheet->freezePane('A2');

/* ==========================
   DADOS
========================== */
$dados = [];
foreach ($_SESSION['EXCEL']['lista'] as $linha) {
    $dados[] = array_values($linha);
}

$sheet->fromArray($dados, null, 'A2');
$sheet->getStyle("A2:{$ultimaColuna}" . (count($dados) + 1))
      ->applyFromArray($styleReg);

/* ==========================
   AJUSTE DE COLUNAS
========================== */
foreach (range('A', $ultimaColuna) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

/* ==========================
   NOME DO ARQUIVO
========================== */
$descricao = $_SESSION['EXCEL']['nome'] ?? 'relatorio';
$filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $descricao) . '.xlsx';

/* ==========================
   OUTPUT
========================== */
if (ob_get_length()) {
    ob_end_clean();
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;