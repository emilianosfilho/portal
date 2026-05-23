<?php 
function bagy_atributo_listar(){
	try {
		// Cria o objeto Request2
		$request = new HTTP_Request2();

		// Define a URL base
		$request->setUrl("https://api.dooca.store/attributes/123634");

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
			$atributos = array();
			$body = (array) json_decode($response->getBody());
			$body_values = (array) $body["values"];
			$atributos = array();
			foreach ($body_values as $key => $value) {
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


function bagy_atributo_existe($MARCA){
	

	if (empty(trim($MARCA))) {
		return false;
	} else {
		$MARCA = trim(mb_strtoupper(str_replace("'", "", $MARCA), 'UTF-8'));
		// varDump2("MARCA: " . $MARCA);

		$request = new HTTP_Request2();
		$request->setUrl("https://api.dooca.store/attributes/123634");
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
			if (in_array($response->getStatus(), [200, 201]) ){
				$json = json_decode($response->getBody(), true);
				if (!empty($json["values"])) {
					foreach ($json["values"] as $key => $value) {
						if ($value["name"] == $MARCA) {
							return $value["id"];
							break;
						}
					}
				}
			}
			return false;
		}
		catch(HTTP_Request2_Exception $e) {
			// varDump2($e->getMessage());
			return false;
		}
	}
}

function bagy_atributo_cadastrar($MARCA){
	

	$payload['attribute_id'] = 123634;
	$payload['name'] = trim(mb_strtoupper(str_replace("'", "", $MARCA), 'UTF-8'));
	$payload['description'] = trim(mb_strtolower(str_replace("'", "", $MARCA), 'UTF-8'));
	// varDump2($payload);

	if (empty($payload['name'])) {
		return false;
	} else {

		$request = new HTTP_Request2();
		$request->setUrl("https://api.dooca.store/attributes/values");
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
			if (in_array($response->getStatus(), array(200, 201))){
				$json = json_decode($response->getBody(), true);
				return $json;
			} else {
				return false;
			}
		}
		catch(HTTP_Request2_Exception $e) {
			varDump2($e->getMessage());
			return false;
		}
	}
}