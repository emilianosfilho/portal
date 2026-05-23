<?php 
function bagy_listarProdutos(){
	$request = new HTTP_Request2();
	$request->setUrl("https://api.dooca.store/products");
	$request->setMethod(HTTP_Request2::METHOD_GET);
	$request->setConfig(array(
		'follow_redirects' => TRUE
	));
	$request->setHeader([
		'Authorization' => "Bearer ".@TOKEN,
		'Content-Type'  => 'application/json'
	]);
	try {
		$response = $request->send();
		if ($response->getStatus() == 200){
			return json_decode($response->getBody(), true);
		} else {
			return false;
		}
	}
	catch(HTTP_Request2_Exception $e) {
		return json_decode($e->getMessage());
	}
}



function bagy_produto_existe($external_id){
	
	try {
		// Cria o objeto Request2
		$request = new HTTP_Request2();
		$request->setUrl("https://api.dooca.store/products");

		// Define o método como POST
		$request->setMethod(HTTP_Request2::METHOD_GET);

		// Adiciona os parâmetros (array chave => valor)
		$request->getUrl()->setQueryVariable('external_id', $external_id);

		// Se houver token, define o header Authorization
		$request->setHeader([
			'Authorization' => "Bearer ".@TOKEN,
			'Content-Type'  => 'application/json'
		]);

		// Executa a requisição
		$response = $request->send();

		// Retorno
		if (200 == $response->getStatus()) {
				return $response->getBody();
		} else {
				return "Erro HTTP: " . $response->getStatus() . " - " . $response->getReasonPhrase();
		}
	} catch (HTTP_Request2_Exception $e) {
			return "Erro na requisição: " . $e->getMessage();
	}
}

function bagy_produto_cadastrar($campos){

	$payload['name'] = trim(mb_strtoupper(str_replace("'", "", $campos['NOME']), 'UTF-8'));
	$payload['description'] = trim(mb_strtoupper(str_replace("'", "", $campos['DESCRICAO']), 'UTF-8'));
	$payload['external_id'] = trim(mb_strtoupper(str_replace("'", "", $campos['NUMORIGINAL']), 'UTF-8'));
	if ($campos['category_id']) {
		$payload['category_ids'][] = $campos['category_id'];
	} else {
		$payload['category_ids'] = [];
	}
	$payload['weight'] = trim(mb_strtoupper(str_replace("'", "", $campos['PESOBRUTO']), 'UTF-8'));
	$payload['depth'] = trim(mb_strtoupper(str_replace("'", "", $campos['COMPRIMENTOM3']), 'UTF-8'));
	$payload['width'] = trim(mb_strtoupper(str_replace("'", "", $campos['LARGURAM3']), 'UTF-8'));
	$payload['height'] = trim(mb_strtoupper(str_replace("'", "", $campos['ALTURAM3']), 'UTF-8'));
	$payload['meta_title'] = trim(mb_strtolower(str_replace("'", "", $campos['NOME']), 'UTF-8'));
	$payload['meta_description'] = trim(mb_strtolower(str_replace("'", "", $campos['meta_description']), 'UTF-8'));
	$payload['meta_keywords'] = trim(mb_strtolower(str_replace("'", "", $campos['meta_keywords']), 'UTF-8'));
	$payload['video'] = "https://youtu.be/CmCoAzhr71U?si=qF0d9fF4_sLX6tE4";
	// varDump2($payload);	
	// die();	
	
	try {
		// Cria o objeto Request2
		$request = new HTTP_Request2();
		$request->setUrl("https://api.dooca.store/products");

		// Define o método como POST
		$request->setMethod(HTTP_Request2::METHOD_POST);

		// Adiciona os parâmetros (array chave => valor)
		$request->addPostParameter(json_encode($payload));

		// Se houver token, define o header Authorization
		$request->setHeader([
			'Authorization' => "Bearer ".@TOKEN,
			'Content-Type'  => 'application/json'
		]);

		// Executa a requisição
		$response = $request->send();

		// Retorno
		if (200 == $response->getStatus()) {
				return $response->getBody();
		} else {
				return "Erro HTTP: " . $response->getStatus() . " - " . $response->getReasonPhrase();
		}
	} catch (HTTP_Request2_Exception $e) {
			return "Erro na requisição: " . $e->getMessage();
	}
}

function bagy_produto_atualizar($product_id, $campos){
	if (empty($product_id) || empty($campos)) {
		return false;
	} else {
		// varDump2($campos);

		$payload['name'] = trim(mb_strtoupper(str_replace("'", "", $campos['NOME']), 'UTF-8'));
		$payload['description'] = trim(mb_strtoupper(str_replace("'", "", $campos['DESCRICAO']), 'UTF-8'));
		$payload['external_id'] = trim(mb_strtoupper(str_replace("'", "", $campos['NUMORIGINAL']), 'UTF-8'));
		$payload['category_ids'] = array(bgcategoria_buscarNome($campos['CATEGORIA']));
		$payload['weight'] = trim(mb_strtoupper(str_replace("'", "", $campos['PESOBRUTO']), 'UTF-8'));
		$payload['depth'] = trim(mb_strtoupper(str_replace("'", "", $campos['COMPRIMENTOM3']), 'UTF-8'));
		$payload['width'] = trim(mb_strtoupper(str_replace("'", "", $campos['LARGURAM3']), 'UTF-8'));
		$payload['height'] = trim(mb_strtoupper(str_replace("'", "", $campos['ALTURAM3']), 'UTF-8'));
		$payload['video'] = "https://youtu.be/CmCoAzhr71U?si=qF0d9fF4_sLX6tE4";
		$payload['meta_title'] = trim(mb_strtolower(str_replace("'", "", $campos['NOME']), 'UTF-8'));
		$payload['meta_description'] = trim(mb_strtolower(str_replace("'", "", $campos['meta_description']), 'UTF-8'));
		$payload['meta_keywords'] = trim(mb_strtolower(str_replace("'", "", $campos['meta_keywords']), 'UTF-8'));

		// varDump2($payload);

		$request = new HTTP_Request2();
		$request->setUrl("https://api.dooca.store/products/{$product_id}");
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
			if ($response->getStatus() == 200 ){
				return "SUCESSO ao executar bagy_produto_atualizar.";
			} else {
				return "ERRO ao executar bagy_produto_atualizar.".json_decode($response->getBody(), true);
			}
		}
		catch(HTTP_Request2_Exception $e) {
			return "ERRO ao executar bagy_produto_atualizar. ".$e->getMessage();
		}

	}

}

function bagy_produto_excluir($product_id){
	
	if (empty($product_id)) {
		exibeMensagem("ERRO ao executar bagy_produto_excluir. product_id inválido ou nulo");
		return false;
	} else {
		$request = new HTTP_Request2();
		$request->setUrl("https://api.dooca.store/products/{$product_id}");
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
			if ($response->getStatus() == 204 ){
				return "SUCESSO ao executar bagy_produto_excluir.";
			} else {
				return "ERRO ao executar bagy_produto_excluir.".json_decode($response->getBody(), true);
			}
		}
		catch(HTTP_Request2_Exception $e) {
			return "ERRO ao executar bagy_produto_atualizar. ".$e->getMessage();
		}
	}
}