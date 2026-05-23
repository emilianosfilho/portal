<?php 
if ($debug) varDump2("ARQUIVO LEITURA");
if ($debug) varDump2($dados);

if (!file_exists($inputFileName)) {
    exibeMensagem('ERRO Arquivo não encontrado: '.$inputFileName);
    die();
}

if($debug) varDump2("SUCESSO Arquivo encontrado com sucesso: ".$inputFileName);


// include the autoloader, so we can use PhpSpreadsheet
require_once(__DIR__ . '/../../vendor/autoload.php');

/**  Identify the type of $inputinputFileName  **/
$inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
/**  Create a new Reader of the type that has been identified  **/
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
/**  Load $inputinputFileName to a Spreadsheet Object  **/
$spreadsheet = $reader->load($inputFileName);
//aba da planilha
$sheet = $spreadsheet->getActiveSheet();

$cabecalho  = array();
$ARQUIVO = array();

foreach ($sheet->getRowIterator() as $keyRow => $row) {
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(FALSE);
    $item[] = array();
    $contCab = 0;
    foreach ($cellIterator as $keyCell => $cell) {
        $value = $cell->getValue();
        if ($keyRow == 1) {
            $cabecalho[] = $value;
        } else {
            $tmp[$keyRow][] = trim($value);
        }
    }
}

foreach ($tmp as $key2 => $value2) {
    $ARQUIVO[] = array_combine($cabecalho, $value2);
}

if (!empty($ARQUIVO)) {
    $_SESSION['ARQUIVO'] = $ARQUIVO;
}

if($debug) varDump2($_SESSION['ARQUIVO']);
