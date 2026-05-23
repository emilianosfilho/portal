<?php 
session_start();

ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE);
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE));
date_default_timezone_set('America/Manaus');
require "../../pages/conf/define.php";
require "../../pages/conf/functions.php";
require "../../pages/conf/conectaOracle.php";
require "../../pages/vendas/function.php";
require "../../vendor/autoload.php";
?>
<!doctype html>
<html lang="pt" class="h-100"></html>

<?php 
$dados = $_SESSION['DADOSEXPORT'];
// varDump2($dados); die();

$cab    = buscaCabecalhoExcel($dados['IDORCAMENTO']);
$cab['VALORTOTAL'] = 0;

// varDump2($cab); die();

if ($itens = buscaItensExcel($dados['IDORCAMENTO'])) {

    foreach ($itens as $key => $value) {
    
        if (isset($dados['somenteDisponiveis']) && (intval($value['QTDISPONIVEL']) < intval($value['QTPEDIDA']) ) ) {
            unset($itens[$key]);
        }

        if (!isset($dados['incluirCodpeca'])) {
            unset($itens[$key]['CODPECA']);
        }

        if (!isset($dados['incluirCodprod'])) {
            unset($itens[$key]['CODPROD']);
        }

        if (!isset($dados['incluirNCM'])) {
            unset($itens[$key]['NCM']);
        }

        if (!isset($dados['incluirCST'])) {
            unset($itens[$key]['CST']);
        }

        if (!isset($dados['incluirLocacao'])) {
            unset($itens[$key]['LOCACAO']);
        }
        unset($itens[$key]['QTDISPONIVEL']);

        $cab['VALORTOTAL'] += $value['SUBTOTAL'];
    }


    // varDump2($itens); die();

    if (empty($itens)) {
        exibeMensagem("Nenhum produto a exibir no relatório");
        fechaAba();
    }

} else {
    exibeMensagem("Nenhum produto a exibir no relatório");
    fechaAba();
}


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_SESSION['DADOSEXPORT'])) {

    // varDump2($_SESSION['DADOSEXPORT']); die();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();


    if (!empty($cab)) {
        $col = 1;
        $row = 1;
        // echo "Lista encontrada com sucesso".PHP_EOL;
        foreach ($cab as $keyCab => $valueCab) {
            $sheet->setCellValue(decodeExcelColuna($col++).$row, $keyCab);
        }

        $row++;
        $col = 1;
        foreach ($cab as $keyCab => $valueCab) {
            $sheet->setCellValue(decodeExcelColuna($col++).$row, $valueCab);
        }
        
    } else {
        echo "ERRO cabecalho não encontrado".PHP_EOL;
    }

    if (!empty($itens)) {
        $row++;
        $row++;
        $col = 1;
        // echo "Lista encontrada com sucesso".PHP_EOL;
        $maxCol     = 0;
        foreach (reset($itens) as $keyCab => $valueCab) {
            $maxCol++;
            $sheet->setCellValue(decodeExcelColuna($col++).$row, $keyCab);
        }
        foreach ($itens as $key => $value) {
            $row++;
            $col = 1;
            foreach ($value as $keyItem => $valueItem) {
                $sheet->setCellValue(decodeExcelColuna($col++).$row, $valueItem);
            }
        }
    } else {
        echo "ERRO Itens não encontrado".PHP_EOL;
    }

    $sheet->setCellValue(decodeExcelColuna($maxCol).$row, $valorTotal);

    $aquivoNome     = $dados['titulo'].'.xlsx';
    $aquivoNome     = filter_var($aquivoNome, FILTER_SANITIZE_STRING);
    $aquivoNome     = basename($aquivoNome);
    $aquivoNome     = urlencode($aquivoNome);
    
    ob_end_clean();
    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. $aquivoNome.'"');
    $writer->save('php://output');
    exit();

} else {
    echo "<h1>Nenhum dado encontrado para gerar planilha excel</h1>";
}
