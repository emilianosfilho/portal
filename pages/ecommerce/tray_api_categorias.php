<?php  
function tray_categoriaListar(){
	tray_validaToken();
	// $debug = true;
	if($debug) varDump2("tray_categoriaListar");

	$request = new HTTP_Request2();
	$request->setUrl($_SESSION['tray']["api_host"]."/categories/?access_token={$_SESSION['tray']['access_token']}");
	$request->setMethod(HTTP_Request2::METHOD_GET);
	$request->setConfig(array(
	  'follow_redirects' => TRUE
	));
	try {
		$retorno = [];
	  $response = $request->send();
	  if (in_array($response->getStatus(), array(200, 201))) {
			$response_Body = json_decode($response->getBody(), true);
			// if($debug) varDump2($response_Body);

			// foreach ($response_Body["Products"] as $key => $Product) {
			// 	$retorno[$Product["Product"]["id"]] = $Product["Product"];
			// };

			// if ($response_Body["paging"]["total"] > $response_Body["paging"]["limit"]) {
			// 	$pages = ceil($response_Body["paging"]["total"] / $response_Body["paging"]["limit"]);
			// 	for ($page=2; $page <= $pages; $page++) { 
			// 		$result = tray_listarProdutosPage($page);
			// 		// varDump2("page: ".$page);
			// 		// varDump2($result); 
			// 		$retorno = array_merge($retorno, $result);
			// 	}
			// }
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

function buscaCategoriaID($category_id){
	tray_validaToken();
	// $debug = true; 
	
	if($debug) varDump2("buscaCategoriaID");
	if($debug) varDump2($category_id);


	if (!empty($category_id)) {
		$request = new HTTP_Request2();
		$request->setUrl("{$_SESSION['tray']["api_host"]}/categories/{$category_id}?access_token={$_SESSION['tray']['access_token']}");
		$request->setMethod(HTTP_Request2::METHOD_GET);
		$request->setConfig(array(
		  'follow_redirects' => TRUE
		));
		// varDump2($request);
		try {
			$response = $request->send();
			if ($response->getStatus() == 200) {
				$json = json_decode($response->getBody(), true);
				// varDump2($json["Category"]);
				return $json["Category"];
			}
		} 
		catch(HTTP_Request2_Exception $e) {
		  die('Error: ' . $e->getMessage());
		  return false;
		}
	}
}

function tray_categoriaCadastrar($categoria){
	tray_validaToken();
	// $debug = true;
	
	if($debug) varDump2("tray_categoriaCadastrar");
	if($debug) varDump2($categoria);
	

	$categoria = trim(mb_strtoupper($categoria, 'UTF-8'));
	if (empty($categoria)) {
		$categoria = "PEÇAS";
	}

	// verifica se o produto já possui cadastro na TRAY
	if ($Categoria = tray_categoriaConsultar($categoria)){
		insereModal("success", "Categoria Tray {$categoria} identificada com sucesso!");
		varDump2($Categoria);
	}	
	else {

		$request = new HTTP_Request2();
		$request->setUrl("{$_SESSION['tray']["api_host"]}/categories?access_token={$_SESSION['tray']['access_token']}");
		$request->setMethod(HTTP_Request2::METHOD_POST);
		$request->setConfig(array(
		  'follow_redirects' => TRUE
		));
		$request->setHeader(array(
		  'Content-Type' => 'application/x-www-form-urlencoded'
		));
		$request->addPostParameter(
			array(
				"Category" => array(
					"name" => $categoria
					)
				)
			);
		try {
		  $response = $request->send();
		  if ($response->getStatus() == 200 || $response->getStatus() == 201) {
		    $product = json_decode($response->getBody());
		    varDump2($product);
		    insereModal("success","Sucesso - Categoria cadastrada na Tray");
		  }
		  else {
		    insereModal("danger",'Unexpected HTTP status: ' . $response->getStatus() . ' ' . $response->getReasonPhrase());
		    varDump2(json_decode($response->getBody()));
		  }
		} 
		catch(HTTP_Request2_Exception $e) {
		  die('Error: ' . $e->getMessage());
		  return false;
		}
	}
}


function tray_categoriaConsultar($categories_name){
	tray_validaToken();
	// $debug = true;
	
	if($debug) varDump2("tray_categoriaConsultar");
	if($debug) varDump2($categories_name);

	if (!empty($categories_name)) {
		$request = new HTTP_Request2();
		$request->setUrl("{$_SESSION['tray']["api_host"]}/categories?access_token={$_SESSION['tray']['access_token']}&name={$categories_name}");
		$request->setMethod(HTTP_Request2::METHOD_GET);
		$request->setConfig(array(
		  'follow_redirects' => TRUE
		));
		// varDump2($request);
		try {
		  $response = $request->send();
		  if ($response->getStatus() == 200) {
		    $json = json_decode($response->getBody());
			  // varDump2($json);
		    if ($json->paging->total > 0){
			    $category = (array) $json->Categories[0]->Category;
			    // varDump2($category);
			    return $category;
		    } else {
		    	return false;
		    }
		  }
		  else {
		    echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
		    $response->getReasonPhrase();
		    return false;
		  }
		}
		catch(HTTP_Request2_Exception $e) {
		  echo 'Error: ' . $e->getMessage();
		  return false;
		}
	}
}
