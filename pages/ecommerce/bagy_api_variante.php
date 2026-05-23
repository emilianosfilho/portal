<?php 
$debug = true;

if (!function_exists('bagy_valida_token')) {
	function bagy_valida_token(){
		if (!defined('TOKEN')) {
	    define('TOKEN', "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzaG9wX2lkIjoxMzQxNTcsInR5cGUiOiJhcGkiLCJlbWFpbCI6IjU4NzgyNjQ0OTYzMTYyNTQwMDAwLmJhZ3lAYXBpLmNvbS5iciIsImZpcnN0X25hbWUiOiJWRU1BUCBXSU5USE9SIiwiYWN0aXZlIjp0cnVlLCJpYXQiOjE2OTMzNDEyNzF9.8KTYG7TjnpFtIzZnFnwNVC5emGksKt8-JPex0LhrDV0");
		}
	}
}

function bagy_variante_cadastrar($varianteWint){
	
	if (empty($varianteWint["product_id"])) {
		varDump2("ERRO ao executar bagy_variante_cadastrar. product_id inválido ou nulo");
		return false;
	} else {
		$payload['attribute_value_id'] = bagy_atributo_existe($varianteWint["MARCA"]);
		$payload['product_id'] = $varianteWint["product_id"];
		$payload["balance"] = moedaPHP($varianteWint["SALDO"]);
		$payload["price"] = moedaPHP($varianteWint["PVENDA"]);
		$payload['active'] = true;

		$request = new HTTP_Request2();
		$request->setUrl("https://api.dooca.store/variations");
		$request->setMethod(HTTP_Request2::METHOD_POST);
		$request->setConfig(array(
		  'follow_redirects' => TRUE
		));
		$request->setHeader([
			'Authorization' => "Bearer ".@TOKEN,
			'Content-Type'  => 'application/json'
		]);
		$request->setBody(json_encode($payload));
		try {
		  $response = $request->send();
		  // varDump2($payload);
		  // varDump2($response->getStatus());
		  // varDump2($response->getBody());

		  if (in_array($response->getStatus(), [200, 201]) ){
				prompt("SUCESSO ao executar bagy_variante_cadastrar. Marca: {$varianteWint["MARCA"]}");
		  } else {
		  	prompt("ERRO ao executar bagy_variante_cadastrar. ".json_encode($response->getBody()));
		  	varDump2($varianteWint);
		  	varDump2($payload);
		  	die();
		  }
		}
		catch(HTTP_Request2_Exception $e) {
			prompt("ERRO ao executar bagy_variante_cadastrar. ".json_encode($e->getMessage()));
		}
	}
}

function bagy_variante_zerar($variation_id){
	if (empty($variation_id)) {
		prompt("ERRO ao executar bagy_variante_zerar. variation_id inválido ou nulo");
	} else {
		$payload['attribute_value_id'] = null;
		$payload['price'] = 0;
		$payload['balance'] = 0;
		$payload['active'] = true;
		// varDump2($payload); die();

		$request = new HTTP_Request2();
		$request->setUrl("https://api.dooca.store/variations/{$variation_id}");
		$request->setMethod(HTTP_Request2::METHOD_PUT);
		$request->setConfig(array(
		  'follow_redirects' => TRUE
		));
		$request->setHeader([
			'Authorization' => "Bearer ".@TOKEN,
			'Content-Type'  => 'application/json'
		]);
		$request->setBody(json_encode($payload));
		try {
		  $response = $request->send();
		  if ($response->getStatus() == 200){
				prompt("SUCESSO ao executar bagy_variante_zerar. ");
		  } else {
		  	prompt("ERRO ao executar bagy_variante_zerar. ".json_encode($response->getBody()));
		  }
		}
		catch(HTTP_Request2_Exception $e) {
			prompt("ERRO ao executar bagy_variante_zerar. ".json_encode($e->getMessage()));
		}
	}
}

function bagy_variante_excluir($variation_id){

	if (!$variation_id || empty($variation_id)) {
		return "ERRO ao executar bagy_variante_excluir. variation_id inválido ou nulo";
	} else {

		$request = new HTTP_Request2();
		$request->setUrl("https://api.dooca.store/variations/{$variation_id}");
		$request->setMethod(HTTP_Request2::METHOD_DELETE);
		$request->setConfig(array(
		  'follow_redirects' => TRUE
		));
		$request->setHeader([
			'Authorization' => "Bearer ".@TOKEN,
			'Content-Type'  => 'application/json'
		]);
		try {
		  $response = $request->send();
		  if ($response->getStatus() == 204){
				prompt("SUCESSO ao executar bagy_variante_excluir.");
		  } else {
		  	prompt("ERRO ao executar bagy_variante_excluir. ".json_encode($response->getBody()));
		  }
		}
		catch(HTTP_Request2_Exception $e) {
			prompt("ERRO ao executar bagy_variante_excluir. ".json_encode($e->getMessage()));
		}
	}
}


		// if (empty($variantesBagy) || !$variantesBagy || is_object($variantesBagy)) {
		// 	return "ERRO ao executar bagy_variante_atualizar. variantesBagy inválido ou nulo";
		// } else {
		// 	foreach ($variantesBagy as $varianteBagy) {
		// 		// DELETE VARIANTES BAGY
		// 	}
		// }

		// if (empty($variantesWint) || !$variantesWint || is_object($variantesWint)) {
		// 	return "ERRO ao executar bagy_variante_atualizar. variantesWint inválido ou nulo";
		// } else {
		// 	foreach ($variantesWint as $varianteWint) {
		// 		// INSERT VARIANTES
		// 	}
		// }

	// 	$payload['product_id'] = $product_id;
	// 	$payload['attribute_value_id'] = $campos['attribute_value_id'];
	// 	$payload['price'] = moedaPHP($campos['PVENDA']);
	// 	$payload['balance'] = moedaPHP($campos['SALDO']);
	// 	$payload['active'] = true;

	// 	varDump2("variation_id: " . $campos["variation_id"]);

	// 	if (empty($campos["variation_id"])) {
	// 		varDump2("ERRO ao executar bagy_variante_atualizar. variation_id inválido ou nulo");
	// 		return false;
	// 	} else {

	// 		$request = new HTTP_Request2();
	// 		$request->setUrl("https://api.dooca.store/variations/{$campos["variation_id"]}");
	// 		$request->setMethod(HTTP_Request2::METHOD_PUT);
	// 		$request->setConfig(array(
	// 		  'follow_redirects' => TRUE
	// 		));
	// 		$request->setHeader([
	// 			'Authorization' => "Bearer ".@TOKEN,
	// 			'Content-Type'  => 'application/json'
	// 		]);
	// 		$request->setBody(json_encode($payload));
	// 		try {
	// 		  $response = $request->send();
	// 			$json = json_decode($response->getBody(), true);
	// 		  if (in_array($response->getStatus(), array(200, 201))){
	// 				return $json;
	// 		  } else {
	// 		  	varDump2($json);
	// 				varDump2($payload);
	// 		  	return false;
	// 		  }
	// 		}
	// 		catch(HTTP_Request2_Exception $e) {
	// 			varDump2($e->getMessage());
	// 			return false;
	// 		}
	// 	}
	// }
