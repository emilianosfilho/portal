<?php  
// varDump2($dados);

if (isset($dados['acao']) && $dados['acao'] != "") {
  switch ($dados['acao']) {
    case 'salvarRelatorioDinamico':
      if (salvarRelatorioDinamico($dados)) {
          insereModal("success", "Relatório configurado com sucesso!");
      } else {
          redireciona("index.php?op=93&nav=relatorios&IDRELATORIO=" . $dados['IDRELATORIO']);
      }
      break;
    
      case 'emitirRelatorio':
        redireciona("index.php?op=92&nav=relatorios&IDRELATORIO=" . $dados['IDRELATORIO']);
        break;
    
      case 'ativarRelatorio':
        ativarRelatorio($dados);
        break;
    
      case 'adicionarFiltro':
        require_once "pages/relatorios/modalFiltro.php";
        break;
    
      case 'editarFiltro':
        require_once "pages/relatorios/modalFiltro.php";
        break;
    
      case 'excluirFiltro':
        require_once "pages/relatorios/modalFiltro.php";
        break;
    
      case 'aplicaradicionarFiltro':
        aplicaradicionarFiltro($dados);
        break;
    
      case 'aplicareditarFiltro':
        aplicareditarFiltro($dados);
        break;
    
      case 'aplicarexcluirFiltro':
        aplicarexcluirFiltro($dados);
        break;
    

    default:
      insereModal('info', " Ação não encontrada: <b>".$dados['acao']."</b>");
      break;
  }
}




    // if (isset($dados['gerarExcel'])) {

    //   switch ($dados['IDRELATORIO']) {

    //     case '5':
    //       $dados['titulo'] = "ANALISE DE ENTRADA";
    //       $dados['lista'] = buscaAnaliseEntrada($dados);        
    //       break;
        
    //     default:
    //       // SE O RELATÓRIO FOR DINAMICO:
    //       if ($dados['NAMEFUNCTION'] == 'dinamico') {
    //           $dados['lista'] = buscaRelatorioDinamico($dados);
    //           $dados['titulo'] = $dados['DESCRICAO'];
    //       } 
    //       // COMPORTAMENTO ANTIGO PARA FUNÇÕES HARDCODED:
    //       else if (function_exists($dados['NAMEFUNCTION'])) {
    //         switch ($dados['NAMEFUNCTION']) {
    //           case 'buscaProdutosCotados':
    //             $dados['lista'] = buscaProdutosCotados($dados);
    //             break;
    //           case 'buscaAnaliseEntrada':
    //             $dados['lista'] = buscaAnaliseEntrada($dados);
    //             break;
    //           case 'buscaProdutosSemEstoque':
    //             $dados['lista'] = buscaProdutosSemEstoque($dados);
    //             break;
    //           case 'listaCadastrosDuplicados':
    //             $dados['lista'] = listaCadastrosDuplicados($dados);
    //             break;
    //           case 'buscaProdutosAppCliente':
    //             $dados['lista'] = buscaProdutosAppCliente($dados);
    //             break;
    //           case 'buscaProdutosEquipamento':
    //             $dados['lista'] = buscaProdutosEquipamento($dados);
    //             break;
    //           case 'buscaProdutosBloqueados':
    //             $dados['lista'] = buscaProdutosBloqueados($dados);
    //             break;
    //           case 'buscaTitulosPagosFornecedorRevenda':
    //             $dados['lista'] = buscaTitulosPagosFornecedorRevenda($dados);
    //             break;




    //           case 'buscaProdutosComEstoqueAnaliseCompras':
    //             $dados['lista'] = buscaProdutosCotados($dados);
    //             if ($compras = consultaSugestaoCompraArquivo($dados) ){
    //               foreach ($dados['lista'] as $key => $value) {
    //                 foreach ($compras as $keyCompras => $valueCompras) {
    //                   if ($value['CODPECA'] == $valueCompras['NUMORIGINAL']) {
    //                     foreach ($valueCompras as $keyVC => $valueVC) {
    //                       $dados['lista'][$key][$keyVC] = $valueVC;
    //                     }
    //                   }
    //                 }
    //                 if (!isset($dados['lista'][0]['NUMORIGINAL'])) {
    //                   $dados['lista'][0]['NUMORIGINAL'] = '';
    //                   $dados['lista'][0]['SALDO'] = '';
    //                   $dados['lista'][0]['2022'] = '';
    //                   $dados['lista'][0]['2023'] = '';
    //                   $dados['lista'][0]['2024'] = '';
    //                   $dados['lista'][0]['2025'] = '';
    //                   $dados['lista'][0]['TODOS'] = '';
    //                   $dados['lista'][0]['DT_ULTIMA_ENT'] = '';
    //                   $dados['lista'][0]['PCOMPRA'] = '';
    //                   $dados['lista'][0]['FORNECEDOR'] = '';
    //                 }
    //               }
    //             }
    //             break;
    //         }
    //       } else {
    //         insereModal('warning', $dados['NAMEFUNCTION'] . " função não encontrada  ");
    //       }
    //       break;
    //   }
    //   // varDump2($dados); die();
    //   if (isset($_SESSION['RELATORIO'])) {
    //     unset($_SESSION['RELATORIO']);
    //   }
    //   if ($dados['lista'] && !empty($dados['lista'])) {
    //     $_SESSION['RELATORIO'] = $dados;
    //     abreNova("pages/relatorios/exportExcel.php", $dados);
    //     redireciona("index.php?op=90");
    //   } else {
    //     insereModal("info", "Nenhum registro encontrado para a consulta");
    //   }
    // }
    // $REL['dados']   = buscaDadosReltarorio($dados['IDRELATORIO']);
    // $REL['filtros'] = buscaFiltrosReltarorio($dados['IDRELATORIO']);
    // // varDump2($REL);