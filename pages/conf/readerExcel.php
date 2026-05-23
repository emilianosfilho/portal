<?php
function readerExcel($filename){

    // $debug = true;

    if (!file_exists($filename)) {
        varDump2('ERRO Arquivo não encontrado: '.$filename);
        die();
    }

    if($debug) varDump2("SUCESSO Arquivo encontrado com sucesso: ".$filename);
    
    $autoload = '../../vendor/autoload.php';
    if (!file_exists($autoload)) {
        varDump2('ERRO Arquivo não encontrado: '.$autoload);
        die();
    }

    if($debug) varDump2("SUCESSO Arquivo encontrado com sucesso: ".$filename);

    // include the autoloader, so we can use PhpSpreadsheet
    require_once($autoload);

    /**  Identify the type of $inputFileName  **/
    $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filename);
    /**  Create a new Reader of the type that has been identified  **/
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
    /**  Load $inputFileName to a Spreadsheet Object  **/
    $spreadsheet = $reader->load($filename);
    //aba da planilha
    $sheet = $spreadsheet->getActiveSheet();

    $cabecalho  = array();
    $_SESSION['ARQUIVO'] = array();

    foreach ($sheet->getRowIterator() as $keyRow => $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(FALSE);
        $item[] = array();
        $contCab = 0;
        foreach ($cellIterator as $cell) {
            $value = $cell->getValue();
            if ($keyRow == 1) {
                $cabecalho[] = mb_strtoupper(trim($value), 'UTF-8');
            } else {
                $reg[$keyRow][] = trim($value);
            }
        }
    }

    foreach ($reg as $value2) {
        $_SESSION['ARQUIVO'][] = array_combine($cabecalho, $value2);
    }

    if($debug) varDump2($_SESSION['ARQUIVO']);
}
