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

$tipoArquivo = 'PRECIFICACAO';

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();


$row = 1; $col = 1;
$sheet->setCellValue(decodeExcelColuna($col++).$row, 'CODPROD');
$sheet->setCellValue(decodeExcelColuna($col++).$row, 'DV');
$sheet->setCellValue(decodeExcelColuna($col++).$row, 'PVENDA');
$sheet->getStyle("A1:".decodeExcelColuna($col)."1")->getFont()->setBold( true );

foreach ($_SESSION['ARQUIVO'] as $key => $value) {
	$row++; $col = 1;
	$sheet->setCellValue(decodeExcelColuna($col++).$row, $value['CODPROD']);
	$sheet->setCellValue(decodeExcelColuna($col++).$row, $value['DV']);
	$sheet->setCellValue(decodeExcelColuna($col++).$row, moeda($value['PVENDA']));
}
for ($i=1; $i < $col; $i++) { 
	$sheet->getColumnDimension(decodeExcelColuna($i))->setAutoSize(true);
}

$filename 			= @date('dmY_').$tipoArquivo.".xls";
$aquivoNome     = filter_var($filename, FILTER_SANITIZE_STRING);
$basename     	= basename($filename);
$urlencode     	= urlencode($filename);

ob_end_clean();
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'. $aquivoNome.'"');
$writer->save('php://output');
exit();