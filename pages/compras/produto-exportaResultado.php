<?php 
session_start();
$timeout = 10800;//10800 segundos = 180 minutos = 3 horas
ini_set( "session.gc_maxlifetime", $timeout );// Defina o máximo de tempo da sessão
ini_set( "session.cookie_lifetime", $timeout );// Defina a vida útil do cookie da sessão

ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ (E_WARNING|E_NOTICE|E_DEPRECATED));
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
clearstatcache();
require_once("../../pages/conf/define.php");
require_once("../../pages/conf/functions.php");
require_once("../../pages/conf/conectaOracle.php");
require_once("../../pages/compras/function.php");
require_once("../../pages/compras/controller.php");
require_once("../../vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// varDump2($_SESSION['RESULTADO']);

$row = 1; $col = 1;
$sheet->setCellValue(decodeExcelColuna($col++).$row, 'NUMORIGINAL');
$sheet->setCellValue(decodeExcelColuna($col++).$row, 'DESCRICAO');
$sheet->setCellValue(decodeExcelColuna($col++).$row, 'CODMARCA');
$sheet->setCellValue(decodeExcelColuna($col++).$row, 'MARCA');
$sheet->setCellValue(decodeExcelColuna($col++).$row, 'CODFAB');
$sheet->setCellValue(decodeExcelColuna($col++).$row, 'CODFORNEC');
$sheet->setCellValue(decodeExcelColuna($col++).$row, 'FORNECEDOR');
$sheet->setCellValue(decodeExcelColuna($col++).$row, 'NCM');
$sheet->setCellValue(decodeExcelColuna($col++).$row, 'ORIGEM');
$sheet->setCellValue(decodeExcelColuna($col++).$row, 'CODPROD');
$sheet->setCellValue(decodeExcelColuna($col++).$row, 'DV');
$sheet->getStyle("A1:".decodeExcelColuna($col)."1")->getFont()->setBold( true );

foreach ($_SESSION['RESULTADO'] as $key => $value) {
	$row++; $col = 1;
	$sheet->setCellValue(decodeExcelColuna($col++).$row, $value['NUMORIGINAL']);
	$sheet->setCellValue(decodeExcelColuna($col++).$row, $value['DESCRICAO']);
	$sheet->setCellValue(decodeExcelColuna($col++).$row, $value['CODMARCA']);
	$sheet->setCellValue(decodeExcelColuna($col++).$row, $value['MARCA']);
	$sheet->setCellValue(decodeExcelColuna($col++).$row, $value['CODFAB']);
	$sheet->setCellValue(decodeExcelColuna($col++).$row, $value['CODFORNEC']);
	$sheet->setCellValue(decodeExcelColuna($col++).$row, $value['FORNECEDOR']);
	$sheet->setCellValue(decodeExcelColuna($col++).$row, $value['NBM']);
	$sheet->setCellValue(decodeExcelColuna($col++).$row, $value['CODORIGEM']);
	$sheet->setCellValue(decodeExcelColuna($col++).$row, $value['CODPROD']);
	$sheet->setCellValue(decodeExcelColuna($col++).$row, $value['DV']);
}
for ($i=1; $i < $col; $i++) { 
	$sheet->getColumnDimension(decodeExcelColuna($i))->setAutoSize(true);
}

$filename 			= @date('dmY_His')."_PRODUTO.xlsx";
$aquivoNome     = filter_var($filename, FILTER_SANITIZE_STRING);
$basename     	= basename($filename);
$urlencode     	= urlencode($filename);

ob_end_clean();
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'. $aquivoNome.'"');
$writer->save('php://output');
exit();