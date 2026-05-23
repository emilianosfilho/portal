<?php  
function tray_cadastrarVariacao($Variant){
	tray_validaToken();
	// $debug = true;

	// Dados que você deseja inserir
	$payload = [
		"Variant" => [
			"product_id" => $Variant["product_id"],
			"reference" => $Variant["reference"],
			"price" => $Variant["price"],
			"stock" => $Variant["stock"],
			"Sku" => $Variant["Sku"],
		]
	];

	$request = new HTTP_Request2();
	$request->setUrl("{$_SESSION['tray']["api_host"]}/products/variants/?access_token={$_SESSION['tray']['access_token']}");
	$request->setMethod(HTTP_Request2::METHOD_POST);	
	$request->setConfig(array( 'follow_redirects' => TRUE ));
	$request->setHeader(array( 'Content-Type' => 'application/x-www-form-urlencoded' ));
	$request->addPostParameter($payload);
	if($debug) varDump2($request);

	try {
		$response = $request->send();
		if (($response->getStatus() == 200) || ($response->getStatus() == 201)) {
			$json = json_decode($response->getBody(), true);
			prompt("SUCESSO ao executar tray_cadastrarVariacao. id: ".$json['id']);
			// varDump2($json);
			return $json;
		} else {
			prompt("ERRO ao executar tray_cadastrarVariacao.");
			prompt($response->getStatus()."<br>".$response->getBody());
			// prompt(json_encode($payload));
			// die();
			return false;
		}
	} 
	catch(HTTP_Request2_Exception $e) {
	  die('Error: ' . $e->getMessage());
	  return false;
	}

}


function tray_atualizarVariacao($Variant){
	tray_validaToken();

	// Dados que você deseja inserir
	$payload = [
		"Variant" => [
			"product_id" => $Variant["product_id"],
			"reference" => $Variant["reference"],
			"price" => $Variant["price"],
			"stock" => $Variant["stock"],
			"Sku" => $Variant["Sku"],
		]
	];

	$request = new HTTP_Request2();
	$request->setUrl("{$_SESSION['tray']["api_host"]}/products/variants/{$Variant["id"]}?access_token={$_SESSION['tray']['access_token']}");
	$request->setMethod(HTTP_Request2::METHOD_PUT);
	$request->setConfig(array( 'follow_redirects' => TRUE ));
	$request->setHeader(array( 'Content-Type' => 'application/json'	));	
	$request->setBody(json_encode($payload));
	try {
		$response = $request->send();
		if (($response->getStatus() == 200) || ($response->getStatus() == 201)) {
			$json = json_decode($response->getBody(), true);
			prompt("SUCESSO ao atualizar a variant_id {$Variant["id"]}");
			// prompt($response->getBody());
			// varDump2($payload);
			return $json;
		} else {
			prompt("ERRO ao processar tray_atualizarVariacao.");
			// prompt($response->getStatus()."<br>".$response->getBody());
			// prompt($payload);
			return false;
		}
	} 
	catch(HTTP_Request2_Exception $e) {
	  die('Error: ' . $e->getMessage());
	  return false;
	}

}

function tray_variacao_atualizar($product_id, $Variants_Wint, $product_Tray){
	tray_validaToken();
	// varDump2($product_id);
	// varDump2($Variants_Wint);
	// varDump2($product_Tray);
	// die();

	if ($product_Tray && !empty($product_Tray)) {
		foreach ($product_Tray as $variant) {
			tray_variacao_excluir($variant['id']);
		}
	}

	if ($Variants_Wint && !empty($Variants_Wint)) {
		$saldoTotal = 0;
		$marcas = [];
		foreach ($Variants_Wint as $key => $variant) {
			if ($variant["QTSALDO"] > 0) {
				$saldoTotal += $variant["QTSALDO"];
				if (in_array($variant["MARCA"], $marcas)) {
					$variant["MARCA"] .= ".";
					if (in_array($variant["MARCA"], $marcas)) {
						$variant["MARCA"] .= ".";
						if (in_array($variant["MARCA"], $marcas)) {
							$variant["MARCA"] .= ".";
						}
					}
				}
				tray_variacao_cadastrar($product_id, $variant);
			} else {
				$indisponivel[] = $variant;
			}
		}

		if ($saldoTotal == 0) {
			foreach ($indisponivel as $key => $variant) {
				tray_variacao_cadastrar($product_id, $variant);
			}
		}
	}	
	
}


function tray_variacao_cadastrar($product_id, $Variant){
	tray_validaToken();
	// $debug = true;

	$payload = [
	"Variant" => [
		  "product_id" => $product_id,
		  "price" => $Variant["PVENDA"],
		  "stock" => $Variant["QTSALDO"],
		  "reference" => $Variant["CODPROD"],
		  "weight" => $Variant["PESOBRUTO"],
		  "length" => $Variant["COMPRIMENTOM3"],
		  "width" => $Variant["LARGURAM3"],
		  "height" => $Variant["ALTURAM3"],
		  "start_promotion" => $Variant["DTINICIO"],
		  "end_promotion" => $Variant["DTFIM"],
		  "promotional_price" => $Variant["VLPROMO"],
		  "Sku" => [
		  	"0" =>[
		  		"type" => 'MARCAS',
		  		"value" => $Variant["MARCA"]
		  	]
		  ]
		]
	];
	if($debug) varDump2($payload);

	$request = new HTTP_Request2();
	$request->setUrl("{$_SESSION['tray']["api_host"]}/products/variants/?access_token={$_SESSION['tray']['access_token']}");
	$request->setMethod(HTTP_Request2::METHOD_POST);	
	$request->setConfig(array( 'follow_redirects' => TRUE ));
	$request->setHeader(array( 'Content-Type' => 'application/x-www-form-urlencoded' ));
	$request->addPostParameter($payload);
	if($debug) varDump2($request);
	if($debug) die();

	try {
		$response = $request->send();
		$json = json_decode($response->getBody(), true);
		if (($response->getStatus() >= 200) && ($response->getStatus() < 400)) {
			prompt("SUCESSO ao executar tray_variacao_cadastrar. MARCA: ".$Variant["MARCA"]);
			return $json;
		} else {
			prompt("ERRO ao executar tray_variacao_cadastrar. MARCA: ".$Variant["MARCA"]);
			varDump2($json);
			return false;
		}
	} 
	catch(HTTP_Request2_Exception $e) {
	  die('Error: ' . $e->getMessage());
	  return false;
	}

}



function tray_variacao_excluir($variant_id){
	tray_validaToken();

	$request = new HTTP_Request2();
	$request->setUrl("{$_SESSION['tray']["api_host"]}/products/variants/{$variant_id}?access_token={$_SESSION['tray']['access_token']}");
	$request->setMethod(HTTP_Request2::METHOD_DELETE);
	$request->setConfig(array( 
		'follow_redirects' => TRUE 
	));
	try {
		$response = $request->send();
		$json = json_decode($response->getBody(), true);
		if (($response->getStatus() >= 200) && ($response->getStatus() < 400)) {
			// prompt("SUCESSO ao executar tray_variacao_excluir. variant_id: {$variant_id}");
			return true;
		} else {
			prompt("ERRO ao executar tray_variacao_excluir. variant_id: {$variant_id}");
			varDump2($json);
			return false;
		}
	} 
	catch(HTTP_Request2_Exception $e) {
	  die('Error: ' . $e->getMessage());
	  return false;
	}
}

function tray_variacao_buscaID($variant_id){
	tray_validaToken();
	// $debug = true;
	if($debug) varDump2("tray_variacao_buscaID");
	if($debug) varDump2("variant_id: ".$variant_id);	

	$request = new HTTP_Request2();
	$request->setUrl($_SESSION['tray']["api_host"]."/products/variants/{$variant_id}?access_token={$_SESSION['tray']['access_token']}");
	$request->setMethod(HTTP_Request2::METHOD_GET);
	$request->setConfig(array(
		'follow_redirects' => TRUE
	));
	try {
		$response = $request->send();
		$json = json_decode($response->getBody(), true);
		if (($response->getStatus() >= 200) && ($response->getStatus() < 400)) {
			// if($debug) prompt("SUCESSO ao executar tray_variacao_buscaID.");
			return $json;
		} else {
			prompt("ERRO ao executar tray_variacao_buscaID.");
			varDump2($json);
			return false;
		}
	} 
	catch(HTTP_Request2_Exception $e) {
	  die('Error: ' . $e->getMessage());
	  return false;
	}
}

function tray_atualizarVariacoes($dados){
	$product_id = $dados["product_id"];
	// varDump2("product_id: {$product_id}");

	$reference = $dados["reference"];
	// varDump2("reference: {$reference}");

	$produto_Wint = buscaDadosProdutoWinthor($reference);
	// varDump2($produto_Wint["VARIANTES"]);

	$saldoTotal = 0;
	if ($produto_Wint["VARIANTES"]) {
		foreach ($produto_Wint["VARIANTES"] as $keyVar => $valueVar) {
			$saldoTotal += moeda($valueVar["QTSALDO"]);
		}
	}	
	tray_resetarImagensProduto($product_id, $saldoTotal);	

	tray_variacao_atualizar(
		$product_id, 
		$produto_Wint["VARIANTES"], 
		$_SESSION['PROD_TRAY']["Variant"]
	);

	$field_log = array(
		"product_id" => $product_id,
		"reference" => $produto_Tray['reference'],
		"name" => $produto_Tray["name"]
	);
	TRAY_PRODUCT_insert($field_log);
}