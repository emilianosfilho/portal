<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . "/../conf/define.php";
require_once __DIR__ . "/../conf/functions.php";
require_once __DIR__ . "/../conf/conectaOracle.php";
require_once __DIR__ . "/function.php"; // Onde estão buscaRelatorioDinamico e outras

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$idRelatorio = isset($dados['IDRELATORIO']) ? (int)$dados['IDRELATORIO'] : 0;

if ($idRelatorio == 0) {
    echo "<script>alert('IDRELATORIO informado é inválido.'); window.history.back();</script>";
    exit;
}

$report['CAB'] = buscaDadosORCRELATORIOS($idRelatorio);

// varDump2($dados); 
// varDump2($report); 

$sql = $report['CAB']['COMANDO_SQL'];

if (is_array($dados["filtro"])) {
  foreach ($dados["filtro"] as $CAMPO => $VALOR) {
    if (str_contains($CAMPO, "DATA")) {
      $sql = str_replace("{".$CAMPO."}", "to_date('".$VALOR."', 'DD/MM/YYYY')", $sql);
    } else if (str_contains($CAMPO, "COD")) {
      $sql = str_replace("{".$CAMPO."}", (int) $VALOR, $sql);
    }
  }
}

$lista = selectOracle($sql);
// varDump2($sql);
// varDump2($lista);
// die();

// Validação de resultado
if (!$lista || empty($lista)) {
    echo "<script>alert('Nenhum registro encontrado para os filtros informados.'); window.history.back();</script>";
    exit;
}

// 4. GERAÇÃO DO SPREADSHEET
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Relatorio');

// Estilos básicos
$styleHeader = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']],
];

// Montagem do Cabeçalho (chaves do primeiro array da lista)
$colunas = array_keys($lista[0]);
$colIdx = 1;
foreach ($colunas as $valor) {
    $sheet->setCellValueByColumnAndRow($colIdx, 1, mb_strtoupper($valor));
    $colIdx++;
}
$ultimaCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($colunas));
$sheet->getStyle("A1:{$ultimaCol}1")->applyFromArray($styleHeader);

// Preenchimento dos Dados
$rowIdx = 2;
foreach ($lista as $linha) {
    $colIdx = 1;
    foreach ($linha as $valor) {
        $sheet->setCellValueByColumnAndRow($colIdx, $rowIdx, $valor);
        $colIdx++;
    }
    $rowIdx++;
}

// Ajuste automático de colunas e congelamento
foreach (range(1, count($colunas)) as $idx) {
    $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx))->setAutoSize(true);
}
$sheet->freezePane('A2');

// 5. Download do Arquivo
$filename = date('dmY_') . preg_replace('/[^a-zA-Z0-9_-]/', '_', $report['CAB']['DESCRICAO']) . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;