<?php 
function bagy_categoria_listar(){
	try {
		// Cria o objeto Request2
		$request = new HTTP_Request2();

		// Define a URL base
		$request->setUrl("https://api.dooca.store/categories");

		// Define o método como GET
		$request->setMethod(HTTP_Request2::METHOD_GET);

		// Define o header Authorization
		$request->setHeader([
			'Authorization' => "Bearer ".@TOKEN,
			'Content-Type'  => 'application/json'
		]);

		// Executa a requisição
		$response = $request->send();

		// Verifica se a resposta foi bem-sucedida
		if (200 == $response->getStatus()) {
			$categorias = array();
			$body = (array) json_decode($response->getBody());
			foreach ($body["data"] as $key => $value) {
				$value = (array) $value;
				if (!isset($categorias[$value["name"]])) {
					$categorias[$value["name"]] = $value;
				}
			}
			$meta = (array) $body["meta"];
			$last_page = $meta["last_page"];
			for ($page=2; $page <= $last_page; $page++) { 
				$atr_page = bagy_categoria_listar_page($page);
				foreach ($atr_page as $key => $value) {
					$value = (array) $value;
					if (!isset($categorias[$value["name"]])) {
						$categorias[$value["name"]] = $value;
					}
				}
			}
			return $categorias;

		} else {
				return "Erro HTTP: " . $response->getStatus() . " - " . $response->getReasonPhrase();
		}
	} catch (HTTP_Request2_Exception $e) {
		return "Erro na requisição: " . $e->getMessage();
	}
}


function bagy_categoria_listar_page($page){
	try {
		// Cria o objeto Request2
		$request = new HTTP_Request2();

		// Define a URL base
		$request->setUrl("https://api.dooca.store/categories");

		// Define o método como GET
		$request->setMethod(HTTP_Request2::METHOD_GET);

    // Adiciona o parâmetro na URL
    $request->getUrl()->setQueryVariable('page', $page);

		// Define o header Authorization
		$request->setHeader([
			'Authorization' => "Bearer ".@TOKEN,
			'Content-Type'  => 'application/json'
		]);

		// Executa a requisição
		$response = $request->send();

		// Verifica se a resposta foi bem-sucedida
		if (200 == $response->getStatus()) {
			$atributos = array();
			$body = (array) json_decode($response->getBody());
			foreach ($body["data"] as $key => $value) {
				$value = (array) $value;
				if (!isset($atributos[$value["name"]])) {
					$atributos[$value["name"]] = $value;
				}
			}
			return $atributos;

		} else {
				return "Erro HTTP: " . $response->getStatus() . " - " . $response->getReasonPhrase();
		}
	} catch (HTTP_Request2_Exception $e) {
		return "Erro na requisição: " . $e->getMessage();
	}
}




function bagy_categoria_cadastrar($campos){

	$c['name'] = trim(mb_strtoupper(str_replace("'", "", $campos['NOME']), 'UTF-8'));
	$c['description'] = trim(mb_strtoupper(str_replace("'", "", $campos['DESCRICAO']), 'UTF-8'));
	$c['external_id'] = trim(mb_strtoupper(str_replace("'", "", $campos['NUMORIGINAL']), 'UTF-8'));
	$c['weight'] = trim(mb_strtoupper(str_replace("'", "", $campos['PESOBRUTO']), 'UTF-8'));
	$c['depth'] = trim(mb_strtoupper(str_replace("'", "", $campos['COMPRIMENTOM3']), 'UTF-8'));
	$c['width'] = trim(mb_strtoupper(str_replace("'", "", $campos['LARGURAM3']), 'UTF-8'));
	$c['height'] = trim(mb_strtoupper(str_replace("'", "", $campos['ALTURAM3']), 'UTF-8'));
	$c['meta_title'] = trim(mb_strtolower(str_replace("'", "", $campos['NOME']), 'UTF-8'));
	$c['meta_description'] = trim(mb_strtolower(str_replace("'", "", $campos['meta_description']), 'UTF-8'));
	$c['meta_keywords'] = trim(mb_strtolower(str_replace("'", "", $campos['meta_keywords']), 'UTF-8'));
	$c['video'] = "https://youtu.be/CmCoAzhr71U?si=qF0d9fF4_sLX6tE4";

	$categoria = trim(mb_strtolower(str_replace("'", "", $campos['CATEGORIA']), 'UTF-8'));
	if (empty($categoria)) {
		$c['category_ids'] = "[]"; 
	} else {

	}


	varDump2($c);

	// $request = new HTTP_Request2();
	// $request->setUrl("https://api.dooca.store/categories");
	// $request->setMethod(HTTP_Request2::METHOD_POST);
	// $request->setConfig(array(
	//   'follow_redirects' => TRUE
	// ));
	// $request->setHeader([
	// 	'Authorization' => "Bearer ".@TOKEN,
	// 	'Content-Type'  => 'application/json'
	// ]);
	// try {
	//   $response = $request->send();
	//   if ($response->getStatus() == 200) {
	// 		$json = json_decode($response->getBody(), true);
	//   	if (empty($json["data"])) {
	//   		return false;
	//   	} else {
	//   		return $json["data"];
	//   	}
	//   } else {
	//   	return false;
	//   }
	// }
	// catch(HTTP_Request2_Exception $e) {
	// 	// varDump2($e->getMessage());
	// 	return false;
	// }
}