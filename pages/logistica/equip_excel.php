<?php
session_start();

// Configurações de Erro para Produção (PHP 8)
ini_set("display_errors", 0); 
error_reporting(E_ALL & ~(E_WARNING | E_NOTICE | E_DEPRECATED));

date_default_timezone_set('America/Manaus');
clearstatcache();

require "../conf/define.php";
require "../conf/functions.php";
require "../conf/conectaOracle.php";
require "function.php";

// Busca os dados (Supondo que retorna array associativo via oci_fetch_all ou similar)
$equipamentos = equipamentosListar();
// varDump2($equipamentos);
// die();

if (!$equipamentos || empty($equipamentos)) {
    echo "<script>alert('Não foram encontrados registros para exportação.'); window.close();</script>";
    exit;
}

// 1. Extração Dinâmica do Cabeçalho
// Pegamos as chaves do primeiro registro do array
$cabecalho_chaves = array_keys($equipamentos[0]);

// Início da montagem do HTML
$html = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
$html .= '<table border="1">';

// 2. Montagem do Cabeçalho (Tratando os nomes das colunas)
$html .= '<thead><tr style="background-color: #D3D3D3; font-weight: bold;">';
foreach ($cabecalho_chaves as $coluna) {
    // Convertemos o nome da coluna para maiúsculo e removemos underscores para estética
    $label = str_replace("_", " ", strtoupper($coluna));
    $html .= '<th>' . htmlspecialchars($label) . '</th>';
}
$html .= '</tr></thead>';

// 3. Montagem do Corpo da Planilha
$html .= '<tbody>';
foreach ($equipamentos as $linha) {
    $html .= '<tr>';
    foreach ($linha as $valor) {
        // Tratamento de valores: Nulos e caracteres especiais
        $campo = ($valor === null) ? '' : $valor;
        $html .= '<td>' . htmlspecialchars($campo) . '</td>';
    }
    $html .= '</tr>';
}
$html .= '</tbody>';
$html .= '</table>';

// 4. Headers para Download
$filename = "EQUIPAMENTOS_VEMAP_" . date('Ymd_His') . ".xls";

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

echo $html;
exit;