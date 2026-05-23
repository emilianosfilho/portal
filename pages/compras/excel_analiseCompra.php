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

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// varDump2($dados);

$lista = consultaSugestaoCompra($dados);
// varDump2($lista);

foreach ($lista as $keyRow => $valueRow) {
    // varDump2($valueRow);
    if ($keyRow == 0) {
        $row=1;
        $col=1;
        foreach ($valueRow as $keyHeader => $valueHeader) {
            $sheet->setCellValue(decodeExcelColuna($col++).$row, $keyHeader);
        }
    }
    $row++;

    $col=1; 
    foreach ($valueRow as $keyContent => $valueContent) {
        if (empty(trim($valueContent))) {
            $sheet->setCellValue(decodeExcelColuna($col).$row, '');
        } else {        
            if ($keyContent == 'VL_ANUAL' || $keyContent == 'PCOMPRA') {
                if ( moedaPHP($valueContent)>0) {
                    $sheet->setCellValue(decodeExcelColuna($col).$row, moeda($valueContent));
                } else {
                    $sheet->setCellValue(decodeExcelColuna($col).$row, '');
                }
            } else {
                if ($keyContent == 'DT_ULTIMA_ENT') {

                    $dataEUA = formataDataEUA($valueContent);
                    // varDump2($dataEUA);

                    $excelDateValue = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel( $dataEUA );
                    // varDump2($excelDateValue);

                    $sheet->setCellValue(decodeExcelColuna($col).$row,  $excelDateValue);
                    $sheet->getStyle(decodeExcelColuna($col).$row)
                        ->getNumberFormat()
                        ->setFormatCode(
                            \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DATETIME
                        );
                } else {
                    $sheet->setCellValue(decodeExcelColuna($col).$row, (string) $valueContent);
                }
            }
        }
        $col++;
    }
}

// Cria o objeto writer para salvar como XLSX
$writer = new Xlsx($spreadsheet);

// Define o nome do arquivo
$filename = "ANALISE_COMPRAS_".@date('dmY_His').".xlsx";
$fullname = "../../upload/".$filename;

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