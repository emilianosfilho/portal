<?php  
// varDump2($dados); 

if (isset($dados['acao']) && $dados['acao']<>"") {
  
	switch ($dados['acao']) {
		case 'limparLista':
			foreach ($_SESSION as $key => $value) {
				if ($key <> 'login') {
					unset($_SESSION[$key]);
				}
			}
			break;

		case 'enviarArquivo':
			require_once("pages/logistica/enviarArquivo.php");
			break;

		##########################################
		## INVENTARIO
		case 'inventario_pesquisar':
			inventario_pesquisar($dados);
			break;

		case 'abrirInventario':
			if (isset($_SESSION['INVENTARIO'])) {
				unset($_SESSION['INVENTARIO']);
			}
			redireciona('index.php?op=139&IDINVENTARIO='.$dados['IDINVENTARIO']);
			break;

		case 'conferirItemINVENTARIO':
			conferirItemINVENTARIO($dados);
			break;

		case 'novoInventario':
			require_once("pages/logistica/modalNovoInventario.php");
			break;

		case 'editarInventario':
			require_once("pages/logistica/modalEditarInventario.php");
			break;

		case 'abrirNovoInventario':
			abrirNovoInventario($dados);
			break;

		case 'atualizaQtconferidaInventario':
			atualizaQtconferidaInventario($dados);
			break;

		case 'finalizarInventario':
			finalizarInventario($dados);
			break;

		case 'exportarInventarioPDF':
			abreNova('pages/logistica/inventario-exportarPDF.php?IDINVENTARIO='.$dados['IDINVENTARIO']);
			break;

		case 'exportarInventarioAdminPDF':
			abreNova('pages/logistica/inventario-exportarAdminPDF.php?IDINVENTARIO='.$dados['IDINVENTARIO']);
			break;


		##########################################
		## PRODUTO
		case 'deleteItemLista':
			unset($_SESSION['produtos'][$dados['key']]);
			break;
		case 'gerarEtiquetasProduto':
			abreNova('pages/logistica/produto-etiquetas.php?POSICAOINI='.$dados['POSICAOINI']);
			break;
		case 'exportaPDFEtiquetaLocacao':
			abreNova('pages/logistica/exportaPDFEtiquetaLocacao.php?POSICAOINI='.$dados['POSICAOINI']);
			break;
		case 'gerarEtiquetasPendentes':
			abreNova('etiquetaInventario.php?IDINVENTARIO='.$dados['IDINVENTARIO'].'&FILTRO=PENDENTES');
			break;
		case 'abrir':
			redireciona("index.php?op=20&edit&IDINVENTARIO=".$dados['IDINVENTARIO']);
			break;
		case 'iniciarSeparacao':
			unset($_SESSION['SEPARACAO']);
			if (!isset($dados['NUMPED'])) {
				insereModal('default', "NUMPED não identificado!");
			} else {
				// $_SESSION['SEPARACAO']['CAB'] = buscaCabCheckout($dados['NUMPED']);
				// $_SESSION['SEPARACAO']['ITE'] = buscaItensCheckout($dados['NUMPED']);
			}
			break;

		case 'pesquisaProduto':
			if ($produtos = pesquisaProduto($dados)) {
				foreach ($produtos as $key => $value) {
					$jaExiste = false;
					foreach ($_SESSION['produtos'] as $keyP => $valueP) {
						if ( ($value['CODPROD'] == $valueP['CODPROD']) ) 
							$jaExiste = true;
					}
					if ($jaExiste === false) {
						$_SESSION['produtos'][] = $value;
					}
				}
			}
			break;	


		case 'produto_modalAlterarLocacao':
			include("pages/logistica/produto_modalAlterarLocacao.php");
			break;
			
		case 'produto_modalEditarProd':
			include("pages/logistica/produto_modalEditarProd.php");
			break;

		case 'produto_defineLocacao':
			if (salvarAlteracaoLocacao($dados) ){
				insereModal("success", "locação alterada com sucesso!");
				redireciona("index.php?op=131&aba=produtos&CODPROD={$dados['CODPROD']}");
			}
			break;


		##########################################
		## CHECK-IN ENTRADA

		case 'checkin_pesquisar':
			$checkin_lista = checkin_pesquisar($dados);
			if(!$checkin_lista){
				insereToastr("success", "Nenhum pedido de compra encontrado para os filtros informados!");
			}
			break;

		case 'checkin_modalConfirmaAbrir':
			require_once("pages/logistica/checkin_modalConfirmaAbrir.php");
			break;

		case 'checkin_modalConfirmaTransito':
			include("pages/logistica/checkin_modalConfirmaTransito.php");
			break;


		case 'checkin_defineTransito':
			if(checkin_defineTransito($dados)){
				insereModal("success", "Conferência de entrada definida como em trânsito com sucesso!");
			} else {
				insereModal("danger", "ERRO ao definir como em trânsito a conferência de entrada!");
			}
			break;

		case 'checkin_modalConfirmaFinalizar':
			include("pages/logistica/checkin_modalConfirmaFinalizar.php");
			break;

		case 'checkin_defineFinalizar':
			if(checkin_defineFinalizar($dados)){
				insereModal("success", "SUCESSO ao Finalizar a conferência de entrada!");
			} else {
				insereModal("danger", "ERRO ao Finalizar a conferência de entrada!");
			}
			break;

		case 'checkin_modalConfirmaExcluir':
			include("pages/logistica/checkin_modalConfirmaExcluir.php");
			break;

		case 'checkin_defineExcluir':
			if(checkin_defineExcluir($dados)){
				insereModal("success", "Conferência de entrada excluída com sucesso!");
			} else {
				insereModal("danger", "ERRO ao excluir a conferência de entrada!");
			}
			break;

		case 'checkin_defineGerar':
			$checkin = validaEProcessaCheckinEntrada($dados);
			if ($checkin === false) {
				$checkin = geraNovoOMGCHECKINC($dados);
			}
			redireciona("index.php?op=135&nav=logistica&aba=entrada&IDCHECKIN=".$checkin['cabecalho']['IDCHECKIN']);
			break;

		case 'checkin_abrir':
			if ($dados['IDCHECKIN'] && $dados['IDCHECKIN'] <> "") {
				redireciona("index.php?op=135&nav=logistica&aba=entrada&IDCHECKIN={$dados['IDCHECKIN']}");
			}
			break;

		case 'checkin_modalconferirItemLocacao':
			include("pages/logistica/checkin_modalconferirItemLocacao.php");
			break;

		case 'checkin_modalAlterarLocacao':
			include("pages/logistica/checkin_modalAlterarLocacao.php");
			break;

		case 'checkin_defineLocacao':
			if (salvarAlteracaoLocacao($dados) ){
				insereModal("success", "locação alterada com sucesso!");
			}
			break;	

		case 'checkin_espelhoNF':
			abreNova('pages/logistica/checkin_espelhoNF.php?NUMNOTA='.$dados['NUMNOTA'].'&NUMTRANSENT='.$dados['NUMTRANSENT']);
			break;	

		case 'checkin_extratoProduto':
			abreNova('pages/logistica/checkin_extratoProduto.php?CODPROD='.$dados['CODPROD']);
			break;
			
		case 'etiqueta_pdf_produto':
			abreNova('pages/logistica/etiqueta_pdf_produto.php?CODPROD='.$dados['CODPROD']);
			break;

		case 'produto_atualizaDados':
			produto_atualizaDados($dados);
			break;


		##########################################
		## LOCAÇÃO


		case 'locacao_pesquisa':
			locacao_pesquisa($dados);
			break;


		##########################################
		## CHECK-OUT SAIDA
		
		case 'confirmarTransito':
			confirmarTransito($dados);
			break;

		case 'exportarPDF-etiquetaVenda':
			abreNova('?NUMPED='.$dados['NUMPED'].'&NUMPEDRCA='.$dados['NUMPEDRCA'].'&NUMPEDCLI='.$dados['NUMPEDCLI'].'');
			break;

		case 'PDF_pedidoConferencia':
			abreNova("pages/logistica/PDF_pedidoConferencia.php?NUMPED={$dados['NUMPED']}&NUMPEDRCA={$dados['NUMPEDRCA']}&NUMPEDCLI={$dados['NUMPEDCLI']}");
			break;

		case 'EXCEL_pedidoConferencia':
			abreNova("pages/logistica/EXCEL_pedidoConferencia.php?NUMPED={$dados['NUMPED']}&NUMPEDRCA={$dados['NUMPEDRCA']}&NUMPEDCLI={$dados['NUMPEDCLI']}");
			break;

		case 'pesquisaNotaEntrada':
			if ($dados['FILTRO'] == 'CANCELADOS') {
				$_SESSION['CANCELADOS'] = false;
				$CANCELADOS = buscaItensCancelados($dados);
				if($CANCELADOS && count($CANCELADOS)>0){
					$_SESSION['CANCELADOS'] = $CANCELADOS;
					redireciona("index.php?op=130&aba=entrada");
				} else {
					insereModal('warning', 'Nenhum item cancelado no período consultado');
				}
			} else {
				$_SESSION['NFENTRADA'] = pesquisaNotaEntrada($dados);
			}
			break;


		case 'modalQTETIQUETA':
			require_once("pages/logistica/modalQTETIQUETA.php");
			break;

		case 'salvarQTETIQUETAProduto':
			salvarQTETIQUETAProduto($dados);
			break;



		case 'historicoLocacao':
			require_once("pages/logistica/modalHistLocacao.php");
			break;

		case 'excluirItem':
			if (isset($_SESSION['produtos'][$dados['key']])) {
				unset($_SESSION['produtos'][$dados['key']]);
				redireciona("index.php?op=131");
			}
			break;

		case 'pesquisaNota':
			$_SESSION['notas'] = pesquisaNota($dados);
			break;

		case 'limparListaNotas':
			if (isset($_SESSION['notas'])) {
				unset($_SESSION['notas']);
			}
			if (isset($_SESSION['PEDVENDA'])) {
				unset($_SESSION['PEDVENDA']);
			}
			break;


		case 'EXCEL_EspelhoNotaEntrada':
			abreNova('pages/logistica/EXCEL_EspelhoNotaEntrada.php?NUMNOTA='.$dados['NUMNOTA'].'&NUMTRANSENT='.$dados['NUMTRANSENT']);
			break;

		case 'checkin_conferirItem':
			if (validaProdutoLocacao($dados)) {
				if (($dados['LOCACAO'] == '9999') || ($dados['LOCACAO'] == '')) {
					insereModal('danger', 'A locação do produto não é uma locação válida. <br>Ajustes a locação do produto antes de conferí-la');
				} else {
					confirmaConferirItemCHECKIN($dados);
				}
			} else {
				insereModal('danger', 'O produto W'.$dados['CODPROD'].' não pertence a esta locação '.$dados['LOCACAO'].'');
			}
			break;

		case 'conferirItemCancelados':
			conferirItemCancelados($dados);
			break;

		case 'gerarExtratoCancelados':
			abreNova("pages/logistica/cancelados-PDF-extrato.php");
			break;

		case 'imprimirConsultaProdutos':
			abreNova("pages/logistica/produto-PDF-consulta.php");
			break;

		case 'EXCEL_ConsultaProdutos':
			abreNova("pages/logistica/EXCEL_ConsultaProdutos.php");
			break;

		case 'editarCheckin':
			insereModalEditarCheckin($dados);
			break;

		case 'salvarEditarCheckin':
			salvarEditarCheckin($dados);
			break;

		case 'editarCancelados':
			insereModalEditarCancelados($dados);
			break;

		case 'salvarEditarCancelados':
			salvarEditarCancelados($dados);
			break;

		case 'finalizarCHECKIN':
			if(finalizarCHECKIN($dados)){
				redireciona('index.php?op=133&acao=limparLista&aba=entrada');
			}
			break;

		case 'pesquisaRelatorio':
			if (!($REL_CHECKOUT = pesquisaRelatorio($dados))) {
				insereModal('danger', 'Não foi possível realizar a pesquisa');
			}
			break;

		case 'checkout_confirmaResetarModal':
			include("pages/logistica/checkout_confirmaResetarModal.php");
			break;

		case 'checkout_resetar':
			checkout_resetar($dados);
			redireciona("?op=132&acao=limparLista&aba=saida");
			break;

		case 'checkout_confirmaAbrir':
			include("pages/logistica/checkout_confirmaAbrirModal.php");
			break;

		case 'checkout_iniciar':
			if ($IDCHECKOUT = checkout_iniciar($dados) ){
				redireciona("?op=137&IDCHECKOUT={$IDCHECKOUT}");
			}
			break;

		case 'checkout_abrir':
			redireciona("?op=137&IDCHECKOUT={$dados['IDCHECKOUT']}");
			break;

		case 'checkout_pesquisar':
      $checkout_lista = checkout_pesquisar($dados);
      if(!$checkout_lista){
        insereToastr("success", "Nenhum pedido de venda encontrado para os filtros informados!");
      }
			break;

		case 'checkout_exportarPDF':
			abreNova("pages/logistica/checkout_exportarPDF.php?IDCHECKOUT=".$dados['IDCHECKOUT']);
			break;


		case 'conferirItemCHECKOUT':
			conferirItemCHECKOUT($dados);
			break;

		case 'editarCHECKOUT':
			insereModalEditarCheckout($dados);
			break;

		case 'salvarEditarCheckout':
			salvarEditarCheckout($dados);
			break;


		case 'finalizarCHECKOUT':
			finalizarCHECKOUT($dados);
			redireciona('index.php?op=132&acao=limparLista&aba=saida');
			break;
		
		case 'excluirItemLocacao':
			if (isset($_SESSION['LOCACAO'][$dados['key']])) {
				unset($_SESSION['LOCACAO'][$dados['key']]);
				redireciona("index.php?op=138");
			}
			break;

		case 'confirmarPrevisao':
			confirmarPrevisao($dados);
			redireciona("index.php?op=137&aba=saida&NUMPED=".$dados['NUMPED']);
			break;

		case 'add5Minutos':
			add5Minutos($dados['NUMPED']);
			break;

		##########################################
		## VIDE
		case 'vide_pesquisar':
			$dados['CODPECA'] = sanitizeOracleString($dados['CODPECA'], 0, true);
			vide_pesquisar($dados['CODPECA']);
			break;			

		case 'vide-modalAtualizar':
    	require_once('pages/logistica/vide-modalAtualizar.php'); 
			break;

		case 'vide_cadastrar':
			vide_merge($dados);
			break;

		case 'vide_atualizar':
			vide_merge($dados);
			break;

		case 'vide-modalConfirmaExclusao':
    	require_once('pages/logistica/vide-modalConfirmaExclusao.php'); 
			break;

		case 'vide_excluir':
			vide_excluir($dados);
			break;

    // require_once('pages/logistica/vide-modalHistorico.php'); 


		##########################################
		## FICHA TÉCNICA
		case 'inserirCategoria':
			inserirCategoria($dados);
			break;
		case 'inserirSubcategoria':
			inserirSubcategoria($dados);
			break;


		##########################################
		## Equipamentos / Máquinas
		case 'equipamentoPesquisar':
			pesquisaEquipamentos($dados);
			break;

		/*****************************************************/
		case 'equip_modalMontadora':
			include ("pages/logistica/equip_modalMontadora.php");
			break;
		case 'equip_montadoraInsert':
			equip_montadoraInsert($dados);
			break;
		case 'equip_excel':
			$_SESSION['EXCEL']['nome'] = "EQUIPAMENTOS";
			$_SESSION['EXCEL']['lista'] = equipamentosListar();
			abreNova("pages/logistica/gerar_Excel.php");
			break;
		case 'montadoraUpdate':
			montadoraUpdate($dados);
			break;
		case 'montadoraDelete':
			montadoraDelete($dados);
			break;

		/*****************************************************/
		case 'equip_modalTipoequip':
			include ("pages/logistica/equip_modalTipoequip.php");
			break;
		case 'equip_tipoequipInsert':
			equip_tipoequipInsert($dados);
			break;
		case 'equip_tipoequipUpdate':
			equip_tipoequipUpdate($dados);
			break;
		case 'equip_tipoequipDelete':
			equip_tipoequipDelete($dados);
			break;

		/*****************************************************/
		case 'equip_modalEquipamento':
			include ("pages/logistica/equip_modalEquipamento.php");
			break;
			
		case 'equip_merge':
			equip_merge($dados);
			redireciona("index.php?op=149&nav=logistica&aba=equipamento");
			break;

		case 'equip_excluir':
			$dados['msgConfirmacao']  = "Excluir o Equipamento ".$dados['EQUIPAMENTO'];
			$dados['acaoConfirmacao'] = "equip_delete";
			require_once "pages/logistica/modalConfirmacao.php";
			break;

		case 'equip_delete':
			equip_delete($dados['IDEQUIPAMENTO']);
			break;

		##########################################
		case 'etiqueta_locacao':
			abreNova("pages/logistica/etiqueta_pdf_locacao.php?".http_build_query($dados));
			break;

		case 'etiqueta_nome':
			abreNova("pages/logistica/etiqueta_pdf_nome.php?".http_build_query($dados));
			break;

		case 'etiqueta_computador':
			abreNova("pages/logistica/etiqueta_pdf_computador.php?".http_build_query($dados));
			break;

		case 'pesquisaNotasEntrega':
			$_SESSION['NFENTREGA'] = pesquisaNotasEntrega($dados);
			break;
		case 'etiqueta_modalEntrega':
			include_once("pages/logistica/etiqueta_modalEntrega.php");
			break;
		case 'etiqueta_Entrega':
			abreNova("pages/logistica/etiqueta_pdf_entrega.php?".http_build_query($dados));
			break;

		##########################################
		default:
			exibeMensagem("Ação ainda não implementada: ".$dados["acao"]);
			varDump2($dados);
			break;

	}
}
