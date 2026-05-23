<?php 
session_start();
ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ (E_WARNING|E_NOTICE|E_DEPRECATED));
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));
date_default_timezone_set('America/Manaus');
clearstatcache();
require "../../pages/conf/define.php";
require "../../pages/conf/functions.php";
require "../../pages/conf/conectaOracle.php";
require "../../pages/compras/function.php";

require "../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnDimension;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// varDump2($_SESSION['RELATORIO']); die();
$registros = $_SESSION['RELATORIO']['lista'];
$header = reset($registros);

$col=1; $row=1;
foreach ($header as $keyHeader => $valueHeader) {
	if (substr(trim($keyHeader), 0, 2) !== "V_") {
		$sheet->setCellValue(decodeExcelColuna($col).$row, $keyHeader);
		$sheet->getStyle(decodeExcelColuna($col).$row)->getFont()->setBold(true);
		$col++;
	}
}

foreach ($registros as $keyReg => $tuplaReg) {
	$col=1; $row++;
	foreach ($tuplaReg as $keyContent => $valueContent) {
		if (substr(trim($keyContent), 0, 2) !== "V_") {
			if ($keyContent == 'VL_ANUAL' || $keyContent == 'PCOMPRA') {
				if (moedaPHP($valueContent)>0) {
					$sheet->setCellValue(decodeExcelColuna($col).$row, moeda($valueContent));
				} else {
					$sheet->setCellValue(decodeExcelColuna($col).$row, '');
				}
			} else {
				$sheet->setCellValue(decodeExcelColuna($col).$row, (string) $valueContent);
			}
			$sheet->getColumnDimension(decodeExcelColuna($col))->setAutoSize(true);
			$col++;
		}
	}
}

// Cria o objeto writer para salvar como XLSX
$writer = new Xlsx($spreadsheet);

// Define o nome do arquivo
$dir        	= "../../upload/";
$NOMEARQUIVO	= $_SESSION['RELATORIO']['DESCRICAO'].".xlsx";
$filename   	= @date('Ymd_His_').$NOMEARQUIVO;
$fullname   	= $dir.$filename;

// Salva o arquivo
$writer->save($fullname);

if (file_exists($fullname)) {
	// echo "Planilha criada com sucesso: <a href='{$filename}'>{$filename}</a>";

	header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Content-Length: ' . filesize($fullname));
	header('Cache-Control: private');
	
	readfile($fullname);

} else {
	echo "ERRO ao criar a Planilha: $filename";
}
?>