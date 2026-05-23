<div class="terminal mt-4 mx-auto">
	<div class="terminal__titlebar">
		<span class="dot red"></span>
		<span class="dot yellow"></span>
		<span class="dot green"></span>
		<span class="terminal__title">CADASTRO DE PRODUTOS - TRAY</span>
	</div>

	<div class="terminal__screen">

	<?php
	$inicio = microtime(true);

	// $debug = true;
	if($debug) varDump2($dados);

	prompt("Entrou no tray_produtoCadastrar");

	$_SESSION['ARQUIVO'] = [];

	$lista = array();
	$erro = 0;

	if ($dados['NUMORIGINAL'] && !empty($dados['NUMORIGINAL'])) {
		$_SESSION['ARQUIVO'][] = ['NUMORIGINAL' => mb_strtoupper(trim($dados['NUMORIGINAL']), 'UTF-8')];
	}

	if (isset($_FILES['arquivo']) && !empty($_FILES['arquivo'])) {
		if($debug) varDump2($_FILES['arquivo']);

		if ($_FILES['arquivo']["error"] == 0) {
			// bloco cadastrar produto em massa a partir de um arquivo
			include("pages/ecommerce/arquivoEnviar.php");
			include("pages/ecommerce/arquivoLeitura.php");

			if (!isset($_SESSION['ARQUIVO']) || empty($_SESSION['ARQUIVO'])) {
				$erro++;
				insereModal("danger", "SESSION['ARQUIVO'] é obrigatório, não pode ser nulo");
			}

		} else {
			$erro++;
			insereModal("danger", "Não foi possivel identificar um NUMORIGINAL e nem um arquivo válidopara cadastro!");		
		}
	}



	if (!empty($_SESSION['ARQUIVO']) && array_is_list($_SESSION['ARQUIVO'])) {

		if($debug) varDump2($_SESSION['ARQUIVO']);
		prompt(count($_SESSION['ARQUIVO'])." peças para processamento!");

		foreach ($_SESSION['ARQUIVO'] as $key => $produto) {

			if (!isset($produto['NUMORIGINAL']) || empty($produto['NUMORIGINAL'])) {
				prompt("produto['NUMORIGINAL'] é obrigatório, não pode ser nulo");
			
			} else {

				prompt("==================================================");
				prompt("processando registro ".($key+1)." de ".count($_SESSION['ARQUIVO']));
				prompt("NUMORIGINAL: ".$produto['NUMORIGINAL']);

				$produto_Wint = buscaDadosProdutoWinthor($produto['NUMORIGINAL']);
				if (!$produto_Wint) {
					prompt($produto['NUMORIGINAL']." Não é um produto winthor válido!");
				} else {
					prompt($produto['NUMORIGINAL']." É um produto winthor válido. Codprod: W".$produto_Wint["PRODUTO"]['CODPROD']);

				
					//#################################################################################
					//VALIDA CATEGORIA
					$category_id 		= '';
					$category_name	= '';
					if (!empty($produto_Wint["PRODUTO"]["CATEGORIA"])) {
						$category_name = ucfirst(strtolower(trim($produto_Wint["PRODUTO"]["CATEGORIA"])));
						$category = tray_categoriaConsultar($category_name);
						if (isset($category["Categories"]["Category"]) && count($category["Categories"]["Category"])>0 ) {
							$category_id 	= $category["Categories"]["Category"][0]['id'];
						}
					}
					if ($category_id == '') {
						$category_id = '25';
						$category_name = 'Peça';
					}
					prompt("Categoria: " . $category_id.'-'.$category_name);

					$product = array(
						"name" => mb_strtoupper($produto_Wint["PRODUTO"]["NOME"], 'UTF-8'),
						"description" => trim($produto_Wint["PRODUTO"]["DESCRICAO"]),
						"description_small" => trim($produto_Wint["PRODUTO"]["meta_description"]),
						"reference" => mb_strtoupper($produto_Wint["PRODUTO"]["NUMORIGINAL"], 'UTF-8'),
						"weight" => moedaPHP($produto_Wint["PRODUTO"]["PESOBRUTO"]),
						"length" => moedaPHP($produto_Wint["PRODUTO"]["COMPRIMENTOM3"]),
						"width" => moedaPHP($produto_Wint["PRODUTO"]["LARGURAM3"]),
						"height" => moedaPHP($produto_Wint["PRODUTO"]["ALTURAM3"]),
						"category_id" => $category_id,
						"price" => moedaPHP($produto_Wint["PRODUTO"]["PVENDA"]),
					);

					$produtos_tray = tray_consultaProdutoReference($produto['NUMORIGINAL']);
					// varDump2($produtos_tray);

					if ($produtos_tray && !empty($produtos_tray) ) {

						prompt("Possui ".count($produtos_tray)." registro(s) de produto na tray");

						foreach ($produtos_tray as $keyB => $prodTray) {
							// varDump2($prodTray["Product"]["Variant"]);
							$atualizarVariantes = true;

							$product_id = $prodTray["Product"]["id"];
							prompt("Validação do product_id: ".$product_id);

							if ($keyB == 0) {

								$prod_tray = tray_consultaProdutoID($product_id);
								$product["id"] = $product_id;
								// varDump2($prod_tray);

								if (empty($prod_tray["modified"])) {
									$product_modified = $prod_tray["created"];
								} else {
									$product_modified = $prod_tray["modified"];
								}
								prompt("Data da ultima atualização desta peça na tray: ".$product_modified);
								
								$dataParaVerificar = new DateTime($product_modified); 

								$dataAtual = new DateTime('today');
								// prompt($dataParaVerificar->format('Y-m-d'));
								// prompt($dataAtual->format('Y-m-d'));
								
								if ($dataParaVerificar->format('Y-m-d') != $dataAtual->format('Y-m-d')) {
									
									$response = tray_atualizarProduto($product);

									if($response){
										prompt("SUCESSO ao executar tray_atualizarProduto");

										tray_atualizarVariacoes(
											$product_id, 
											$produto_Wint["VARIANTES"], 
											$prodTray["Product"]
										);

									} else {
										prompt("ERRO ao executar tray_atualizarProduto");
									}
								
								} else {
									prompt("Peça já atualizada nas últimas 24 horas!");

									$response = tray_atualizarProduto($product);
									if($response){
										prompt("SUCESSO ao executar tray_atualizarProduto");
										tray_atualizarVariacoes(
											$product_id, 
											$produto_Wint["VARIANTES"], 
											$prodTray["Product"]
										);
									}

								}


							} else {
								if ( tray_excluirProduto($product_id) ){
									prompt("SUCESSO ao excluir cadastro duplicado na Tray. Product_id:".$product_id);
								} else {
									prompt("ERRO ao excluir cadastro duplicado na Tray. Product_id:".$product_id);
								}
							}
							
						}

					} else {
						$prod = tray_produtoCadastrar($product);
						if($prod){
							$product_id = $prod['id'];
							prompt("SUCESSO ao cadastrar produto na tray. product_id: " . $product_id);
							tray_atualizarVariacoes(
								$product_id, 
								$produto_Wint["VARIANTES"], 
								array()
							);

						} else {
							prompt("ERRO ao cadastrar produto na tray.");
						}

					}

				}

			}
			
		} // end foreach

	}
	// Fim da medição
	$fim = microtime(true);

	// Calcula o tempo total em segundos
	$tempoTotal = $fim - $inicio;

	// Converte para horas, minutos e segundos
	$horas   = floor($tempoTotal / 3600);
	$minutos = floor(($tempoTotal % 3600) / 60);
	$segundos = $tempoTotal % 60;

	// Exibe formatado
	$tempo = "Tempo de execução: ";
	if ($horas > 0) {
	    $tempo .= $horas . "h ";
	}
	if ($minutos > 0 || $horas > 0) {
	    $tempo .= $minutos . "m ";
	}
	$tempo .= number_format($segundos, 0) . "s";

	prompt($tempo);
	?>
</div>
</div>