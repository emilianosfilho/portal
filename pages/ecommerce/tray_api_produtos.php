<?php  
function tray_listarProdutos(){
	tray_validaToken();
	// $debug = true;
	
	$request = new HTTP_Request2();
	$request->setUrl("{$_SESSION['tray']["api_host"]}/products?limit=50&access_token={$_SESSION['tray']['access_token']}");
	$request->setMethod(HTTP_Request2::METHOD_GET);
	$request->setConfig(array( 'follow_redirects' => TRUE ));
	$request->setHeader(array( 'Content-Type' => 'application/x-www-form-urlencoded' ));
	try {
	  $response = $request->send();
	  if (($response->getStatus() >= 200) && ($response->getStatus() < 400)) {
	  	$retorno = [];
			$json = json_decode($response->getBody(), true);
			$total = $json["paging"]["total"];
			// varDump2("total: {$total}");
			$pagesTotal = ceil($json["paging"]["total"] / 50);
			// varDump2("pagesTotal: {$pagesTotal}");

			foreach ($json["Products"] as $key => $value) {
				if (!isset($retorno[$value["Product"]["id"]])) {
					$retorno[$value["Product"]["id"]] = $value["Product"];
				}
			}

			for ($page=2; $page <= $pagesTotal; $page++) { 
				if ($prodPage = tray_listarProdutosPage($page) ){
					foreach ($prodPage as $id => $product) {
						if (!isset($retorno[$id])) {
							$retorno[$id] = $product;
						}
					}
				}
			}
			return $retorno;

	  } else {
			prompt('ERRO ao executar tray_listarProdutos');
			prompt('Unexpected HTTP status: ' . $response->getStatus() . ' ' . $response->getReasonPhrase());
			return false;
	  }
	} 
	catch(HTTP_Request2_Exception $e) {
		prompt('ERRO ao executar tray_listarProdutos');
	  prompt('Error: ' . $e->getMessage());
	  return false;
	}
}

function tray_listarProdutosPage($page){
	tray_validaToken();
	// $debug = true;
	
	if($debug) varDump2("tray_listarProdutos");
	if($debug) varDump2($_SESSION['tray']);

	$request = new HTTP_Request2();
	$request->setUrl($_SESSION['tray']["api_host"]."/products?page={$page}&limit=50&access_token={$_SESSION['tray']['access_token']}");
	$request->setMethod(HTTP_Request2::METHOD_GET);
	$request->setConfig(array(
	  'follow_redirects' => TRUE
	));
	try {
		$retorno = [];
	  $response = $request->send();
	  if (($response->getStatus() >= 200) && ($response->getStatus() < 400)) {
			$response_Body = json_decode($response->getBody(), true);
			foreach ($response_Body["Products"] as $key => $Product) {
				$retorno[$Product["Product"]["id"]] = $Product["Product"];
			};
			return $retorno;
	  }
	  else {
			exibeMensagem('Unexpected HTTP status: ' . $response->getStatus() . ' ' . $response->getReasonPhrase());
			return false;
	  }
	} 
	catch(HTTP_Request2_Exception $e) {
	  die('Error: ' . $e->getMessage());
	  return false;
	}
}




function tray_produto_buscaID($product_id){
	tray_validaToken();
	// $debug = true;
	
	$request = new HTTP_Request2();
	$request->setUrl("{$_SESSION['tray']["api_host"]}/products/{$product_id}&access_token={$_SESSION['tray']['access_token']}");
	$request->setMethod(HTTP_Request2::METHOD_GET);
	$request->setConfig(array( 'follow_redirects' => TRUE ));
	$request->setHeader(array( 'Content-Type' => 'application/x-www-form-urlencoded' ));
	try {
	  $response = $request->send();
	  if (($response->getStatus() >= 200) && ($response->getStatus() < 400)) {
			$json = json_decode($response->getBody(), true);
			// varDump2($json);
			return [0 => ["Product" => $json["Product"]]];
	  } else {
			prompt('ERRO ao executar tray_produto_buscaID');
			prompt('Unexpected HTTP status: ' . $response->getStatus() . ' ' . $response->getReasonPhrase());
			return false;
	  }
	} 
	catch(HTTP_Request2_Exception $e) {
		prompt('ERRO ao executar tray_produto_buscaID');
	  prompt('Error: ' . $e->getMessage());
	  return false;
	}
}


function tray_produto_buscaReference($reference){
	tray_validaToken();

	$request = new HTTP_Request2();
	$request->setUrl("{$_SESSION['tray']["api_host"]}/products?reference=".urlencode($reference)."&access_token={$_SESSION['tray']['access_token']}");
	$request->setMethod(HTTP_Request2::METHOD_GET);
	$request->setConfig(array( 'follow_redirects' => TRUE ));
	$request->setHeader(array( 'Content-Type' => 'application/x-www-form-urlencoded' ));
	try {
	  $response = $request->send();
	  if (($response->getStatus() >= 200) && ($response->getStatus() < 400)) {
			$json = json_decode($response->getBody(), true);
			if ($json["paging"]["total"] <= 0) {
				return false;
			} else {
				return $json["Products"];
			}
	  } else {
			prompt('ERRO ao executar tray_produto_buscaReference');
			prompt('Unexpected HTTP status: ' . $response->getStatus() . ' ' . $response->getReasonPhrase());
			return false;
	  }
	} 
	catch(HTTP_Request2_Exception $e) {
		prompt('ERRO ao executar tray_produto_buscaReference');
	  prompt('Error: ' . $e->getMessage());
	  return false;
	}
}


function tray_produto_buscaName($name){
	tray_validaToken();

	$request = new HTTP_Request2();
	$request->setUrl("{$_SESSION['tray']["api_host"]}/products?name=".urlencode("%".mb_strtoupper(trim($name), 'UTF-8')."%")."&access_token={$_SESSION['tray']['access_token']}");
	$request->setMethod(HTTP_Request2::METHOD_GET);
	$request->setConfig(array( 'follow_redirects' => TRUE ));
	$request->setHeader(array( 'Content-Type' => 'application/x-www-form-urlencoded' ));
	try {
	  $response = $request->send();
	  if (($response->getStatus() >= 200) && ($response->getStatus() < 400)) {
			$json = json_decode($response->getBody(), true);
			if ($json["paging"]["total"] <= 0) {
				return false;
			} else {
				return $json["Products"];
			}
	  } else {
			prompt('ERRO ao executar tray_produto_buscaName');
			prompt('Unexpected HTTP status: ' . $response->getStatus() . ' ' . $response->getReasonPhrase());
			return false;
	  }
	} 
	catch(HTTP_Request2_Exception $e) {
		prompt('ERRO ao executar tray_produto_buscaName');
	  die('Error: ' . $e->getMessage());
	  return false;
	}
}

function tray_produto_Cadastrar($product) { 
	tray_validaToken();
	// $debug = true;

	// Dados que você deseja Cadastrar
	$payload = [
		"Product" => [
			"name" => substr(mb_strtoupper($product["name"], 'UTF-8'), 0, 200),
			"description" => substr(trim($product["description"]), 0, 4800),
			"reference" => substr(mb_strtoupper($product["reference"], 'UTF-8'), 0, 120),
			"category_id" => moedaPHP($product["category_id"]),
			"related_categories" => ["{$product["related_categories"]}"],
			"weight" => moedaPHP($product["weight"]),
			"length" => moedaPHP($product["length"]),
			"width" => moedaPHP($product["width"]),
			"height" => moedaPHP($product["height"]),
			"upon_request" => moedaPHP($product["upon_request"]),
			"available" => 1,
			"price" => moedaPHP($product["price"]),
			"video" => '<iframe width="560" height="315" src="https://www.youtube.com/embed/CmCoAzhr71U?si=zAyKJBhEBg4AxTyM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
			"metatag" => 
				[
					[
						"type" => "title",
						"content" => substr(mb_strtolower($product["name"], 'UTF-8'), 0, 200)
					],
					[
						"type" => "description",
						"content" => "A peça ".reset(explode("|", $product["name"]))." tem qualidade garantida pela LOJA VEMAP - Solução de Peças para Tratores e Máquinas Pesadas, com vinte anos de mercado. Entrega para o Sudeste em até dois dias úteis. Aproveite também nossas ofertas."
					],
					[
						"type" => "keywords",
						"content" => "peças para trator, peças trator, peças máquinas pesadas, trator peças, trator caterpillar, peças caterpillar, trator new holland, trator veltra, peças veltra, trator Massey Ferguson, peças Messey Ferguson, trator John Deere, John Deere, peças New Holland"
					]
				],
			"warranty" => "7 dias após o recebimento do produto",
			"availability" => "Imediata",
			"additional_message" => "Frete grátis para Capitais, Distrito Federal e Santarém - PA",
		]
	];
	// varDump2($payload); die();

	$request = new HTTP_Request2();
	$request->setUrl("{$_SESSION['tray']["api_host"]}/products?access_token={$_SESSION['tray']['access_token']}");
	$request->setMethod(HTTP_Request2::METHOD_POST);	
	$request->setConfig(array( 'follow_redirects' => TRUE ));
	$request->setHeader(array( 'Content-Type' => 'application/x-www-form-urlencoded' ));
	$request->addPostParameter($payload);
	if($debug) varDump2($request);

	try {
		$response = $request->send();
		$json = json_decode($response->getBody(), true);
		if (($response->getStatus() >= 200) && ($response->getStatus() < 400)) {
			prompt("SUCESSO ao executar tray_produto_Cadastrar");
			return $json["id"];
		} else {
			prompt("Erro ao executar tray_produto_Cadastrar. Código: " . $response->getStatus());
			prompt($response->getBody());
			varDump2($payload);
			die();
			return false;
		}
	} 
	catch(HTTP_Request2_Exception $e) {
	  die('Error: ' . $e->getMessage());
	  return false;
	}
}

function tray_produto_atualizar($product_id, $product) { 
	tray_validaToken();
	
	// Dados que você deseja atualizar
	$payload = [
		"Product" => [
			"name" => substr(mb_strtoupper($product["name"], 'UTF-8'), 0, 200),
			"description" => substr(trim($product["description"]), 0, 4800),
			"reference" => substr(mb_strtoupper($product["reference"], 'UTF-8'), 0, 120),
			"category_id" => moedaPHP($product["category_id"]),
			"related_categories" => ["{$product["related_categories"]}"],
			"weight" => moedaPHP($product["weight"]),
			"length" => moedaPHP($product["length"]),
			"width" => moedaPHP($product["width"]),
			"height" => moedaPHP($product["height"]),
			"upon_request" => moedaPHP($product["upon_request"]),
			"available" => 1,
			"price" => moedaPHP($product["price"]),
			"video" => '<iframe width="560" height="315" src="https://www.youtube.com/embed/CmCoAzhr71U?si=zAyKJBhEBg4AxTyM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
			"metatag" => 
				[
					[
						"type" => "title",
						"content" => substr(mb_strtolower($product["name"], 'UTF-8'), 0, 200)
					],
					[
						"type" => "description",
						"content" => "A peça ".reset(explode("|", $product["name"]))." tem qualidade garantida pela LOJA VEMAP - Solução de Peças para Tratores e Máquinas Pesadas, com vinte anos de mercado. Entrega para o Sudeste em até dois dias úteis. Aproveite também nossas ofertas."
					],
					[
						"type" => "keywords",
						"content" => "peças para trator, peças trator, peças máquinas pesadas, trator peças, trator caterpillar, peças caterpillar, trator new holland, trator veltra, peças veltra, trator Massey Ferguson, peças Messey Ferguson, trator John Deere, John Deere, peças New Holland"
					]
				],
			"warranty" => "7 dias após o recebimento do produto",
			"availability" => "Imediata",
			"additional_message" => "Frete grátis para Capitais, Distrito Federal e Santarém - PA",
		]
	];
	// varDump2($payload); die();
	$json_data = json_encode($payload);
	// varDump2($json_data);

	$request = new HTTP_Request2();
	$request->setUrl("{$_SESSION['tray']["api_host"]}/products/{$product_id}?access_token={$_SESSION['tray']['access_token']}");
	$request->setMethod(HTTP_Request2::METHOD_PUT);	
	$request->setConfig(array( 'follow_redirects' => TRUE ));
	$request->setHeader('Content-Type', 'application/json');
	// Set the request body
	$request->setBody($json_data);
	// varDump2($request);

	try {
		$response = $request->send();
		$json_body = json_decode($response->getBody(), true);
		if (($response->getStatus() >= 200) && ($response->getStatus() < 400)) {
			prompt("SUCESSO ao executar tray_produto_atualizar");
			return $json_body;
		} else {
			prompt("Erro ao executar tray_produto_atualizar. Código: " . $response->getStatus()."<br>".$response->getBody());
			return false;
		}
	} 
	catch(HTTP_Request2_Exception $e) {
		prompt("Erro ao executar tray_produto_atualizar. Error: " . $e->getMessage());
		return false;
	}
}

function tray_produto_excluir($product_id){
	tray_validaToken();
	// $debug = true;
	
	if($debug) varDump2("tray_produto_excluir");
	if($debug) varDump2($product_id);

	$request = new HTTP_Request2();
	$request->setUrl("{$_SESSION['tray']["api_host"]}/products/{$product_id}?access_token={$_SESSION['tray']['access_token']}");
	$request->setMethod(HTTP_Request2::METHOD_DELETE);
	$request->setConfig(array(
	  'follow_redirects' => TRUE
	));
	try {
		$response = $request->send();
		if (($response->getStatus() >= 200) && ($response->getStatus() < 400)) {
			return true;
		} else {
			prompt("Erro ao executar tray_produto_excluir. Código: " . $response->getStatus()."<br>".$response->getBody());
			return false;
		}
	} 
	catch(HTTP_Request2_Exception $e) {
	  die('Error: ' . $e->getMessage());
	  return false;
	}

}

function tray_atualizarPrecoProduto($product_id, $price = 0){
	tray_validaToken();

	if (!$product_id || empty($product_id)) {
		varDump2("ERRO ao executar tray_atualizarPrecoProduto. product_id inválido.");
	} else {
		if (!$price || empty($price)) {
			varDump2("ERRO ao executar tray_atualizarPrecoProduto. price inválido.");
		} else {

			// Dados que você deseja alterar
			$payload = [
				"Product" => [
					"price" => moedaPHP($price),
				]
			];

			$request = new HTTP_Request2();
			$request->setUrl("{$_SESSION['tray']["api_host"]}/products/{$product_id}?access_token={$_SESSION['tray']['access_token']}");
			$request->setMethod(HTTP_Request2::METHOD_PUT);
			$request->setConfig(array( 'follow_redirects' => TRUE ));
			$request->setHeader(array( 'Content-Type' => 'application/json'	));	
			$request->setBody(json_encode($payload));

			try {
				$response = $request->send();
				if (($response->getStatus() >= 200) && ($response->getStatus() < 400)) {
					prompt("SUCESSO ao executar tray_atualizarPrecoProduto. produto ID: {$product_id}  Menor preço atual: ".$price);
					return json_decode($response->getBody(), true);
				} else {
					prompt("ERRO SUCESSO ao executar tray_atualizarPrecoProduto. produto ID: {$product_id}    Menor preço atual: ".$price.". Código de erro: " . $response->getStatus()."<br>".$response->getBody());
					return false;
				}
			} 
			catch(HTTP_Request2_Exception $e) {
			  die('Error: ' . $e->getMessage());
			  return false;
			}
		}
	}
}

function tray_produto_addRelatedCategories($product_id, $related_categories) { 
	tray_validaToken();
	
	// Dados que você deseja alterar
	$payload = [
		"Product" => [
			"related_categories" => ["{$related_categories}"],
		]
	];
	$json_data = json_encode($payload);
	// varDump2($json_data);

	$request = new HTTP_Request2();
	$request->setUrl("{$_SESSION['tray']["api_host"]}/products/{$product_id}?access_token={$_SESSION['tray']['access_token']}");
	$request->setMethod(HTTP_Request2::METHOD_PUT);	
	$request->setConfig(array( 'follow_redirects' => TRUE ));
	$request->setHeader('Content-Type', 'application/json');
	// Set the request body
	$request->setBody($json_data);
	// varDump2($request);

	try {
		$response = $request->send();
		$json_body = json_decode($response->getBody(), true);
		if (($response->getStatus() >= 200) && ($response->getStatus() < 400)) {
			prompt("SUCESSO ao executar tray_produto_addRelatedCategories");
			// varDump2($json_body);
		} else {
			prompt("Erro ao executar tray_produto_addRelatedCategories. Código: " . $response->getStatus()."<br>".$response->getBody());
			prompt(json_encode($payload));
		}
	} 
	catch(HTTP_Request2_Exception $e) {
	  die('Error: ' . $e->getMessage());
	}
}