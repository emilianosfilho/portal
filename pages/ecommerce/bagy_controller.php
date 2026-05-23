<?php  
// $debug = true;
if($debug) varDump2($dados);

require_once 'HTTP/Request2.php';

if (!defined('TOKEN')) {
  define('TOKEN', "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzaG9wX2lkIjoxMzQxNTcsInR5cGUiOiJhcGkiLCJlbWFpbCI6IjU4NzgyNjQ0OTYzMTYyNTQwMDAwLmJhZ3lAYXBpLmNvbS5iciIsImZpcnN0X25hbWUiOiJWRU1BUCBXSU5USE9SIiwiYWN0aXZlIjp0cnVlLCJpYXQiOjE2OTMzNDEyNzF9.8KTYG7TjnpFtIzZnFnwNVC5emGksKt8-JPex0LhrDV0");
}

if ($dados['acao']) {
	switch ($dados['acao']) {

		case 'limparLista':
			foreach ($_SESSION as $key => $value) {
				if ($key !== "login") {
					unset($_SESSION[$key]);
				}
			}
			break;

		case 'bagy_produto_pesquisa':
			$listaProduto = false;
			$product_id = ($dados['product_id']=="")?false:intval($dados['product_id']);
			$numoriginal = ($dados['numoriginal']=="")?false:trim(mb_strtoupper($dados['numoriginal'], 'UTF-8'));
			$name = ($dados['name']=="")?false:trim(mb_strtoupper($dados['name'], 'UTF-8'));
			$categoria_id = ($dados['categoria_id']=="false")?false:intval($dados['categoria_id']);
			if ($product_id) {
				$listaProduto = bgproduct2_buscaProduct_id($product_id);
			} else if ($numoriginal) {
				$listaProduto = bgproduct2_buscaNumoriginal($numoriginal);
			} else if ($name) {
				$listaProduto = bgproduct2_buscaName($name);
			} else if ($categoria_id) {
				$listaProduto = bgproduct2_buscaCategoria_id($categoria_id);
			}
			break;

		// case 'consultarNumoriginal':
		// 	$NUMORIGINAL = trim(mb_strtoupper($dados['NUMORIGINAL'], 'UTF-8'));
		// 	if ($prod_wint = buscaDadosProdutoWinthor($NUMORIGINAL)) {
		// 		//verifica se o produto já possui cadastro
		// 		if ($product_id = produtoPossuiCadBagy($prod_wint['PRODUTO']['NUMORIGINAL'])){
		// 			//caso o produto já possua cadastro na bagy o produto deverá ser ignorado
		// 			if($debug) varDump2("product_id: " . $product_id);

		// 			insereModal("info", "NUMORIGINAL " . $prod_wint['PRODUTO']['NUMORIGINAL'] . " Já possui cadastro na bagy.<br><br> Product_id: " . $product_id);
				
		// 		} else {
		// 			//caso ainda não possua cadastro este deverá ser criado
		// 			if($debug) varDump2($prod_wint);
		// 			if($debug) varDump2("NUMORIGINAL " . $prod_wint['PRODUTO']['NUMORIGINAL'] . " ainda não possui cadastro na bagy");
		// 			$acao_ajax = "cadastrarProdutoBagy";
		// 		}
		// 	} else {
		// 		insereModal("danger", "Não foram encontrados produtos válidos no Winthor com Número Original ".$dados['NUMORIGINAL']);
		// 	}	
		// 	break;

		// case 'produtoEditarProduto':
		// 	if (isset($dados['product_id']) && $dados['product_id'] != "") {
		// 		require_once('pages/ecommerce/produto_modal_editar.php');
		// 	} else {
		// 		insereModal('warning', 'Não conseguimos identificar o produto. <br>Tente novamente!');
		// 	}
		// 	break;

		// case 'enviarImagem':
		// 	if (isset($_FILES['imagem'])) {
		// 		exibeMensagem("imagem não pode ser enviada no momento!");
		// 		// varDump2($_FILES['imagem']);
		// 	} else {
		// 		exibeMensagem("Files não enviado");
		// 	}
		// 	break;

		// case 'produtoPesquisaBGPRODUCT':
		// 	$lista_produtos = produtoPesquisaBGPRODUCT($dados);
		// 	break;

		// case 'enviarArquivoDesconto':
		// 	if (!$_SESSION['ARQUIVO']) {
		// 		exibeMensagem("Não foi possível encontrar o arquivo no servidor");
		// 	} else {
		// 		varDump2($_SESSION['ARQUIVO']);
		// 	}
		// 	break;

		// case 'enviarPlanilhaIndependente':
		// 	require_once("pages/ecommerce/enviarPlanilhaIndependente.php");
		// 	break;	

		// case 'enviarPlanilhaExclusao':
		// 	require_once("pages/ecommerce/enviarPlanilhaExclusao.php");
		// 	break;	

		case 'bagy_produto_cadastrar':
			require_once("pages/ecommerce/bagy_produto_cadastrar.php");
			break;		

		default:
			insereModal("info", "Funcionalidade ".$dados['acao']." ainda não implementada");
			break;

	}
}

