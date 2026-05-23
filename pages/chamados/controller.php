<?php  
// $debug = true;
// varDump2($dados); 

if (isset($dados['acao']) && $dados['acao']<>"") {
	switch ($dados['acao']) {

		case 'modalAddTipo':
		    require_once("pages/chamados/modalAddTipo.php");
			break;
		case 'addTipo':
			addTipo($dados);
			break;

		case 'modalAddCategoria':
		    require_once("pages/chamados/modalAddCategoria.php");
			break;
		case 'addCategoria':
			addCategoria($dados);
			break;

		case 'modalAddChamado':
		    require_once("pages/chamados/modalAddChamado.php");
			break;
		case 'addChamado':
			addChamado($dados['ID_TIPO'], $dados['ID_CATEGORIA'], $dados['ID_USUARIO'], $dados['TITULO']);
			break;

		case 'modalAddHistorico':
		    require_once("pages/chamados/modalAddHistorico.php");
			break;

		case 'modalRelatorioAtividades':
		    require_once("pages/chamados/modalRelatorioAtividades.php");
			break;

		case 'modalEditarPrevisao':
		    require_once("pages/chamados/modalEditarPrevisao.php");
			break;

		case 'modalFinalizar':
		    require_once("pages/chamados/modalFinalizar.php");
			break;

		case 'editarPrevisao':
			editarPrevisao($dados);
			break;

		case 'PDF_relatorioAtividades':
			if ($dados['GRUPO'] == "CHAMADO") {
	    		abreNova("pages/chamados/PDF_relatorioAtividades.php?".http_build_query($dados));
			} else {
	    		abreNova("pages/chamados/PDF_relatorioAtividadesDatahora.php?".http_build_query($dados));
			}
			break;

		case 'visualizar':
			$chamado = buscaChamadoID($dados["ID_CHAMADO"]);
			if($debug) varDump2($chamado);
			break;

		case 'iniciarAtendimento':
			gerenciarAtendimento($dados, 'INICIAR');
			redireciona("index.php?op=36&ID_CHAMADO=".$dados["ID_CHAMADO"]);
			break;

		case 'reiniciarAtendimento':
			gerenciarAtendimento($dados, 'REINICIAR');
			redireciona("index.php?op=36&ID_CHAMADO=".$dados["ID_CHAMADO"]);
			break;

		case 'pausarAtendimento':
			gerenciarAtendimento($dados, 'PAUSAR');
			break;

		case 'chamado_finalizar':
			gerenciarAtendimento($dados, 'FINALIZAR');
			exibeMensagem("Chamado finalizado com sucesso!");
			redireciona("index.php?op=35&nav=chamados");
			break;

		case 'addHistoricoChamado':
			addHistoricoChamado($_SESSION['login']['IDUSUARIO'], $dados["ID_CHAMADO"], $dados["HISTORICO"], 'HIST');
			redireciona("index.php?op=36&ID_CHAMADO=".$dados["ID_CHAMADO"]);
			break;

		case 'modalAddAnexo':
		    require_once("pages/chamados/modalAddAnexo.php");
			break;
		case 'addAnexoChamado':
			if (isset($_FILES) && !empty($_FILES)) {
				require_once("pages/chamados/enviaArquivo.php");
				addHistoricoChamado($_SESSION['login']['IDUSUARIO'], $dados["ID_CHAMADO"], $inputFileName, 'ANEXO');
			}
			redireciona("index.php?op=36&ID_CHAMADO=".$dados["ID_CHAMADO"]);
			break;

		default:
			insereModal("info", "Ação ainda não definida: ".$dados['acao']);
			break;
	}
}