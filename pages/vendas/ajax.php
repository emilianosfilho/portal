<?php 
session_start();
// Desativar exibição de erros direto na tela para não quebrar o JSON
ini_set("display_errors", 0); 
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE|E_DEPRECATED));

require_once __DIR__."/../../pages/conf/define.php";
require_once __DIR__."/../../pages/conf/functions.php";
require_once __DIR__."/../../pages/conf/conectaOracle.php";
require_once __DIR__."/../../pages/vendas/function.php";

// Logo após os requires no ajax.php:
header('Content-Type: application/json; charset=utf-8'); // Força a leitura correta no JS

// Limpa qualquer output acidental (espaços em branco, warnings)
ob_clean();

$dados = !empty($_POST) ? $_POST : $_GET;

if (isset($dados['acao'])) {

	switch ($dados['acao']) {

		case 'getEquipamento':
			echo json_encode(getEquipamento($dados['idMontadora']));
			break;

		case 'buscaHistVendas':
      $numOriginal = sanitizeOracleString($dados['NUMORIGINAL']);
      $resultado = buscaVendas($numOriginal);
      echo json_encode($resultado ?: []);
			break;

		case 'buscaHistCompras':
      $numOriginal = sanitizeOracleString($dados['NUMORIGINAL']);
      $resultado = buscaCompras($numOriginal);
      echo json_encode($resultado ?: []);
			break;

		case 'buscaHistOrcamentos':
      $numOriginal = sanitizeOracleString($dados['NUMORIGINAL']);
      $resultado = buscaCotacoes($numOriginal);
      echo json_encode($resultado ?: []);
			break;
    
    case 'update_selection':
      $idx = $dados['index'];
      $st  = ($dados['status'] === 'true'); 
      if (isset($_SESSION['RADAR'][$idx])) {
          $_SESSION['RADAR'][$idx]['SELECIONADO'] = $st;
      }
      echo json_encode(['status' => 'success']);
      break;

    case 'update_all_selection':
      $status = ($dados['status'] === 'true'); // Converte string do POST para boolean
      
      if (isset($_SESSION['RADAR']) && is_array($_SESSION['RADAR'])) {
          // Percorre a array de dados do Winthor na sessão e atualiza a flag
          foreach ($_SESSION['RADAR'] as $key => $item) {
              $_SESSION['RADAR'][$key]['SELECIONADO'] = $status;
          }
          echo json_encode(['status' => 'success', 'msg' => 'Todos os itens atualizados']);
      } else {
          echo json_encode(['status' => 'error', 'msg' => 'Sessão RADAR não encontrada']);
      }
      break;

    default:
      echo json_encode(['erro' => 'Ação não definida']);
      break;
	}

}
exit;