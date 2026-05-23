<?php
//echo "<pre>Entrou no importa xls</pre>";

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../plugins/PHPExcel/Classes/');

/** PHPExcel_IOFactory */
$file = 'plugins/PHPExcel/Classes/PHPExcel/IOFactory.php';
if(file_exists($file)){
    //exibeMensagem("Arquivo ".$file." encontrado");
    require_once $file;
} else {
    exibeMensagem("Arquivo ".$file." NÃO encontrado");
}


//echo 'OK - Arquivo Encontrado.<br />';
//echo 'Loading file ',pathinfo($filename,PATHINFO_BASENAME),' using IOFactory to identify the format<br />';
//echo '<hr />';
$objPHPExcel = PHPExcel_IOFactory::load($newfilename);
$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
// varDump2($sheetData); die();

$ARQUIVO = array();
if(isset($_SESSION['ARQUIVO'])){
    unset($_SESSION['ARQUIVO']);
}
$_SESSION['ARQUIVO'] = array();

foreach ($sheetData as $row) {
    if($row['A'] <> 'NUMPEDIDO'){
        $tmp['NUMPEDIDO']    = intval($row['A']);
        $tmp['CODFILIAL']    = intval($row['B']);
        $tmp['CODFORNEC']    = intval($row['C']);
        $tmp['CODPROD']      = intval($row['D']);
        $tmp['DV']           = intval($row['E']);
        $tmp['QTPEDIDO']     = intval($row['F']);
        $tmp['PCOMPRA']      = floatval($row['G']);
        
        if (!isset($_SESSION['ARQUIVO'][$tmp['NUMPEDIDO']])) {
            $_SESSION['ARQUIVO'][$tmp['NUMPEDIDO']] = array();
        }
        $_SESSION['ARQUIVO'][$tmp['NUMPEDIDO']][] = $tmp;
    }
}

?>