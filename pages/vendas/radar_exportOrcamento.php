<?php
session_start();

// Configurações de Erro para Produção (Ajustado para PHP 8)
ini_set("display_errors", 0);
error_reporting(E_ALL & ~(E_WARNING | E_NOTICE | E_DEPRECATED));

date_default_timezone_set('America/Manaus');
clearstatcache();

require_once "../../pages/conf/define.php";
require_once "../../pages/conf/functions.php";
require_once "../../pages/conf/conectaOracle.php"; // Assume-se o uso de oci_connect internamente
require_once "../../pages/vendas/function.php";


// 3. Parâmetros de Inicialização (Ex: Oriundos de filtros ou sessão)

$codcli    = (int) ($_SESSION['login']['CODCLI'] ?? 16837);/** ANALISE DEMANDA */
$codusur   = (int) ($_SESSION['login']['CODUSUR'] ?? 1);/** VEMAP */
$codfilial = (int) ($_SESSION['login']['CODFILIAL'] ?? 1);/** VEMAP */

// 4. Montagem do Cabeçalho do Orçamento
$orcamento = [
    'CLIENTE'  => buscaDadosCliente($codcli),
    'VENDEDOR' => buscaDadosVendedor($codusur),
    'FILIAL'   => buscaDadosFilial($codfilial)
];

$orcamento['COBRANCA'] = buscaDadosCobranca($orcamento['CLIENTE']['CODCOB']);
$orcamento['PLPAG']    = buscaDadosPlpagDefault($orcamento['CLIENTE']['CODCLI']);


// 1. Validação da Sessão
if (!isset($_SESSION['RADAR']) || !is_array($_SESSION['RADAR'])) {
    $_SESSION['RADAR'] = [];
}

// 2. Filtragem de Itens Selecionados (PHP 8 Procedural)
$dadosSelecionados = array_filter($_SESSION['RADAR'], function($item) {
    return isset($item['SELECIONADO']) && 
           ($item['SELECIONADO'] === true || $item['SELECIONADO'] == 'true' || $item['SELECIONADO'] == 1);
});

if (empty($dadosSelecionados)) {
    echo "<script>
            alert('Nenhum item selecionado. Marque os produtos desejados no Radar.');
            window.history.back();
          </script>";
    exit;
}

// Gera o registro na tabela de orçamentos (Ex: PCORCAMC) e retorna o ID
$novoOrcamento = geraNovoCabecalho($orcamento);

if ($novoOrcamento === false) {
  die("Erro ao gerar cabeçalho do orçamento no Oracle.");
}

$idOrcamento = (int) $novoOrcamento['IDORCAMENTO'];

// 5. Processamento dos Itens
foreach ($dadosSelecionados as &$item) {
  $item['IDORCAMENTO'] = $idOrcamento;
  $item['CODPECA']     = sanitizeOracleString($item['NUMORIGINAL']);
  $item['DESCRICAO']   = sanitizeOracleString($item['DESCRICAO']);
  $item['MARCA']       = sanitizeOracleString($item['MARCA']);
  $item['CODPROD']     = ($item['WINT'] ? (int) reset(explode("-", $item['WINT'])): '');
  $item['DV']          = ($item['WINT'] ? (int) end(explode("-", $item['WINT'])): '');

  // Validação de duplicidade
  if (validaItemOrcamento($item)) {
      // Log ou Alerta (Opcional)
      continue; 
  }

  // Tratamento de Origem e Referência (Padrão WinThor)
  if (in_array($item['ORIGEM'], ['NPR', 'VIDE', 'VIDE1', 'VIDE2'])) {
      $item['NUMORIGINAL'] = $item['VIDE'] ?? '';
  }

  // Lógica de Disponibilidade
  $item['PVENDA']       = (float) moedaPHP($item['PVENDA']);
  $item['PVENDAMIN']    = (float) moedaPHP($item['PVENDA']);
  $item['QTDISPONIVEL'] = (int) ($item['QTDISPONIVEL'] ?? 0);
  $item['QTPEDIDA']     = (int) ($item['QTPEDIDA'] ?? 1);

  if ($item['QTDISPONIVEL'] >= $item['QTPEDIDA']) {
      $item['DISPONIBILIDADE'] = 'IMEDIATA';
  } elseif ($item['QTDISPONIVEL'] <= 0) {
      $item['QTDISPONIVEL']    = (int) 0;
      $item['DISPONIBILIDADE'] = 'SOB CONSULTA';
  } else {
      $item['DISPONIBILIDADE'] = "IMEDIATA (".$item['QTDISPONIVEL'].")";
  }

  // Persistência no Banco de Dados (Ex: PCORCAMI)
  insereItemOrcamento($idOrcamento, $item);
}

redireciona("../../index.php?op=62&nav=vendas&acao=consultaOrcamento&IDORCAMENTO=$idOrcamento");