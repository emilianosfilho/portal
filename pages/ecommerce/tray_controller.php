<?php  
require_once("plugins/HTTP_Request2-2.6.0/HTTP/Request2.php");
require_once("pages/ecommerce/wint_function.php");
require_once("pages/ecommerce/tray_api_token.php");
require_once("pages/ecommerce/tray_api_categorias.php");
require_once("pages/ecommerce/tray_api_imagens.php");
require_once("pages/ecommerce/tray_api_produtos.php");
require_once("pages/ecommerce/tray_api_variacoes.php");

// $debug = true;
if($debug) varDump2($dados);

if ($dados['acao']) {
	switch ($dados['acao']) {

		case 'limparLista':
			foreach ($_SESSION as $key => $value) {
				if ($key !== "login") {
					unset($_SESSION[$key]);
				}
			}
			break;
			
		case 'tray_modalConfirmarCancelamento':
			include("pages/ecommerce/tray_modalConfirmarCancelamento.php");
			break;

		case 'tray_cancelarEdicao':
			redireciona("?op=171");
			break;
 
		case 'tray_pesquisaProduto':
			$PRODUTOS = false;
			if (!empty($dados['product_id'])) {
				$PRODUTOS = tray_produto_buscaID($dados['product_id']);
			} else {
				if (!empty($dados['reference'])) {
					$PRODUTOS = tray_produto_buscaReference($dados['reference']);
				} else {
					if (!empty($dados['name'])) {
						$PRODUTOS = tray_produto_buscaName($dados['name']);
					}
				}
			}
			// varDump2($PRODUTOS);
			break;

		case 'tray_editarProduto':
			redireciona("?op=172&product_id={$dados['product_id']}");
			break;

		case 'tray_confirmaExcluirProduto':
			require_once("pages/ecommerce/tray_produtoModalExcluir.php");
			break;

		case 'tray_excluirProduto':
			if (tray_excluirProduto($dados['product_id'], $tray)){
				foreach ($PRODUTOS as $key2 => $value2) {
					foreach ($value2 as $key => $value) {
						if ($value["id"] == $dados['product_id']) {
							unset($PRODUTOS[$key2]);
						}
					}
				}
			}
			break;

		case 'tray_produtoCadastrar':
			include_once("pages/ecommerce/tray_produtoCadastrar.php");
			break;

		case 'tray_atualizarProduto':
			tray_atualizarProduto($dados);
			if(!$debug) redireciona("index.php?op=171");
			break;

		case 'tray_atualizarVariacoes':
			tray_atualizarVariacoes($dados);
			break;

		case 'tray_atualizarImagensProduto':
			tray_atualizarImagensProduto($dados, $tray);
			break;

		case 'tray_addImagemProduto':
			require_once("pages/ecommerce/tray_addImagemProduto.php");
			break;

		case 'tray_adicionarImagemProduto':
			include("pages/ecommerce/tray_moveUploadedFile.php");
			tray_adicionarImagemProduto($dados);
			redireciona("index.php?op=172&product_id={$dados['product_id']}");
			break;

		case 'tray_modalDeletarImagem':
			include("pages/ecommerce/tray_modalDeletarImagem.php");
			break;

		case 'tray_deletarImagem':
			tray_deletarImagem($dados);
			break;			

		case 'tray_modalResetarImagem':
			require_once("pages/ecommerce/tray_modalResetarImagem.php");
			break;

		case 'tray_resetarImagensProduto':
			if (tray_resetarImagensProduto($dados['product_id'], $dados['saldoTotal'])) 
				insereToastr("success", "Imagens resetadas com sucesso!");
			else 
				insereToastr("danger", "ERRO ao resetar Imagens!");
			break;

		case 'tray_produtoArquivoImportar':
			include("pages/ecommerce/arquivoEnviar.php");
			include("pages/ecommerce/arquivoLeitura.php");
			include("pages/ecommerce/tray_produtoCadastrarArquivo.php");
			break;

		case 'tray_produtoModalEliminar':
			include("pages/ecommerce/tray_produtoEliminar.php");
			break;

		default:
			varDump2($dados);
			insereModal("info", "Funcionalidade {$dados['acao']} ainda não implementada");
			break;

	}
}

exibeToastr();
