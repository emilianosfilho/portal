<?php 
require_once 'plugins/HTTP_Request2-2.6.0/HTTP/Request2.php';
require_once("pages/ecommerce/wint_function.php");
require_once("pages/ecommerce/bagy_api_produto.php");
require_once("pages/ecommerce/bagy_api_categoria.php");
require_once("pages/ecommerce/bagy_api_atributo.php");
require_once("pages/ecommerce/bagy_api_variante.php");
require_once("pages/ecommerce/bagy_api_imagem.php");
varDump2($dados);

if (isset($dados['NUMORIGINAL']) && !empty($dados['NUMORIGINAL'])) {

	$prod_wint = buscaDadosProdutoWinthor($dados['NUMORIGINAL']);
	// varDump2($prod_wint);

	$prod_bagy = bagy_produto_existe($dados['NUMORIGINAL']);
	// varDump2($prod_bagy);

	if($prod_wint){

		$cat_bagy = bagy_categoria_existe($prod_wint["PRODUTO"]['CATEGORIA']);
		// varDump2($cat_bagy);
		if ($cat_bagy) {
			$prod_wint["PRODUTO"]['category_ids'] = [$cat_bagy["id"]];
		} else {
			$prod_wint["PRODUTO"]['category_ids'] = [];
		}

		$produto_bagy = false; // variavel para definir o produto bagy válido

		if($prod_bagy){
			foreach ($prod_bagy as $key_prod_bagy => $value_prod_bagy) {
				if ($key_prod_bagy == 0) {
					// BLOCO PARA QUANDO O PRODUTO EXISTE NA BAGY - ATUALIZAR
					if ($produto_bagy = bagy_produto_atualizar($value_prod_bagy["id"], $prod_wint["PRODUTO"]) ){
						varDump2("SUCESSO ao executar bagy_produto_atualizar");
					} else {
						varDump2("ERRO ao executar bagy_produto_atualizar");
					}

				} else {
					// BLOCO PARA EXCLUIR CADASTROS DUPLICADOS NA BAGY
					if (bagy_produto_excluir($value_prod_bagy["id"]) ){
						varDump2("SUCESSO ao executar bagy_produto_excluir");
					} else {
						varDump2("ERRO ao executar bagy_produto_excluir");
					}

				}
			}

		} else {
			// BLOCO PARA QUANDO O PRODUTO NÃO EXISTE NA BAGY - CADASTRAR
			if ($produto_bagy = bagy_produto_cadastrar($prod_wint["PRODUTO"]) ){
				varDump2("SUCESSO ao executar bagy_produto_cadastrar");
			} else {
				varDump2("ERRO ao executar bagy_produto_cadastrar");
			}

		}

		//*********************************************************
		// BLOCO ATUALIZAR VARIANTES
		
		// Indexar arrays pela MARCA para otimizar comparação
		$map_wint = [];
		$map_bagy = [];
		if ($prod_wint["VARIANTES"]) {
			foreach ($prod_wint["VARIANTES"] as $w) {
				$map_wint[$w['MARCA']] = $w;
			}
		}
		if ($produto_bagy["variations"]) {
			foreach ($produto_bagy["variations"] as $b) {
				if (!empty($b["attribute"])) {
					$map_bagy[$b["attribute"]["name"]] = $b;
				}
			}
		}
		// varDump2($map_wint);
		// varDump2($map_bagy);

		// Listas de operações
		$toInsert = [];
		$toUpdate = [];
		$toDelete = [];
		$toNothing = [];

		// Verificar inserções e atualizações
		foreach ($map_wint as $marca => $produtoW) {
	    if (!isset($map_bagy[$marca])) {
	        $toInsert[] = $produtoW;
	    } else {
	    	if ( floatval($map_bagy[$marca]["price"]) == floatval($produtoW['PVENDA'])
	    		&& floatval($map_bagy[$marca]["balance"]) == floatval($produtoW['SALDO'])) {
		    	$toNothing[] = $produtoW;
	    	} else {
		    	$produtoW["variation_id"] = $map_bagy[$marca]["id"];
		    	$produtoW["attribute_value_id"] = $map_bagy[$marca]["attribute_value_id"];
		    	$toUpdate[] = $produtoW;
	    	}
	    }
		}
		// Verificar exclusões (produtos no Tray que não estão no Wint)
		foreach ($map_bagy as $marca => $produtoT) {
		    if (!isset($map_wint[$marca])) {
		        $toDelete[] = $produtoT;
		    }
		}

		$estoqueTotal = 0;

		//Aplica os respectivos scripts
		if (!empty($toInsert)) {
			varDump2(count($toInsert)." toInsert");
			foreach ($toInsert as $key => $campos) {
				$campos["attribute_value_id"] = bagy_atributo_existe($campos["MARCA"]);
				if (!$campos["attribute_value_id"]) {
					$campos["attribute_value_id"] = bagy_atributo_cadastrar($campos["MARCA"]);
				}
				if ($var_bagy = bagy_variante_cadastrar($produto_bagy["id"], $campos)){
					varDump2("SUCESSO ao executar bagy_variante_cadastrar. Marca: ".$campos["MARCA"]." | variation_id: ".$var_bagy["id"]);
					$estoqueTotal += $campos["SALDO"];
				} else {
					varDump2("ERRO ao executar bagy_variante_cadastrar. Marca: ".$campos["MARCA"]);
				}
			}
		}

		if (!empty($toUpdate)) {
			varDump2(count($toUpdate)." toUpdate");
			foreach ($toUpdate as $key => $campos) {
				if ($var_bagy = bagy_variante_atualizar($produto_bagy["id"], $campos) ){
					varDump2("SUCESSO ao executar bagy_variante_atualizar. Marca: ".$campos["MARCA"]." | variation_id: ".$var_bagy["id"]);
					$estoqueTotal += $campos["SALDO"];
				} else {
					varDump2("ERRO ao executar bagy_variante_atualizar. Marca: ".$campos["MARCA"]);
				}
			}
		}

		if (!empty($toDelete)) {
			varDump2(count($toDelete)." toDelete");
			foreach ($toDelete as $key => $campos) {
				// code...
			}
		}

		if (!empty($toNothing)) {
			varDump2(count($toNothing)." Variações com estoque e preço atualizados!");
			foreach ($toNothing as $key => $campos) {
				$estoqueTotal += $campos["SALDO"];
			}
		}

		varDump2("INFO o produto possui um estoque total de: ".$estoqueTotal);

		//*********************************************************
		// BLOCO ATUALIZAR IMAGENS
		$imgUpdate = false;
		varDump2("INFO o produto possui ".count($produto_bagy["images"])." imagens cadastradas.");

		if ( count($produto_bagy["images"]) <= 4 ) {
			foreach ($produto_bagy["images"] as $key => $valueImg) {
				// Imagem Excluir
				bagy_imagem_excluir($produto_bagy["id"], $valueImg["id"]);
			}

			// Imagem Cadastrar 
			if (bagy_imagem_cadastrar($produto_bagy["id"], $estoqueTotal) ){
				varDump2("SUCESSO ao executar bagy_imagem_cadastrar. estoqueTotal: ".$estoqueTotal);
				$estoqueTotal += $campos["SALDO"];
			} else {
				varDump2("ERRO ao executar bagy_imagem_cadastrar.");
			}

		} else {
			varDump2("Nenhuma alteração de imagems a ser feita");
		}


		//########################################################
		// ATUALIZA A TABELA BGPRODUCT2
		BGPRODUCT2_atualizacao($produto_bagy);

	} else {

		if($produto_bagy){
			varDump2("BLOCO PARA QUANDO O PRODUTO EXISTE NA BAGY MAS NÃO É UM PRODUTO WINT VÁLIDO - EXCLUIR NA BAGY");
			die();
		}

	}

}