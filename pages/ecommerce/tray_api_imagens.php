<?php  
// function tray_adicionarImagemProduto($dados){
// 	tray_validaToken();
// 	// $debug = true;
	
// 	if($debug) varDump2("tray_adicionarImagemProduto");
// 	if($debug) varDump2($dados);


// 	if ($_SESSION['ARQUIVO']) {
// 		if($debug) varDump2($_SESSION['ARQUIVO']);
// 		$url_completa = 'http://186.233.92.186:8000' . $_SERVER['REQUEST_URI'];
// 		$imagemAdd = str_replace("index.php", @DIR_UPLOAD.$_SESSION['ARQUIVO'][0], $url_completa);
// 		if($debug) varDump2($imagemAdd);

// 		$payload['Images'] = array();
// 		$payload['Images'] += [ "picture_source_1" => $imagemAdd ];
// 		foreach ($_SESSION["produto"]["ProductImage"] as $key => $img) {
// 			if (!empty($img["http"])) {
// 				$label = "picture_source_".($key+2);
// 				$payload['Images'] += [ $label => $img["http"] ];
// 			}
// 		}
// 	}
// 	if($debug) varDump2($payload);

// 	$request = new HTTP_Request2();
// 	$request->setUrl("{$_SESSION['tray']["api_host"]}/products/{$dados['product_id']}/images?access_token={$_SESSION['tray']['access_token']}");
// 	$request->setMethod(HTTP_Request2::METHOD_POST);
// 	$request->setConfig(array( 'follow_redirects' => TRUE ));
// 	$request->setHeader(array( 'Content-Type' => 'application/json'	));	
// 	$request->setBody(json_encode($payload));
// 	if($debug) varDump2($request);

// 	try {
// 	    $response = $request->send();
// 	    if ($response->getStatus() == 200) {
// 	        insereModal("success", "Imagem do produto enviada com sucesso!</p><br><h5>Observação:</h5><p>Assim que realizado o envio, as imagens serão processadas em fila, podendo levar alguns minutos para serem exibidas no cadastro do produto.");
// 	        if($debug) varDump2($response->getBody());
// 	    } else {
// 	        insereModal("danger", "Erro ao enviar a Imagem do produto. Código: " . $response->getStatus()."<br>".$response->getBody());
// 	    }
// 	} catch (HTTP_Request2_Exception $e) {
//         insereModal("danger", 'Erro na requisição: ' . $e->getMessage());
// 	}
// }


// function tray_deletarImagem($dados){
// 	tray_validaToken();
// 	$debug = true;
	
// 	if($debug) varDump2("tray_deletarImagem");
// 	if($debug) varDump2($dados);

// 	$payload['Images'] = [];
// 	for ($key=0; $key < 15; $key++) {
// 		$label = "picture_source_".($key+1);
// 		if (($key+1) != $dados['posicao']) {
// 			$payload['Images'] += [ $label => ($_SESSION["produto"]["ProductImage"][$key]["http"] ?? "") ];
// 		} else {
// 			$payload['Images'] += [ $label => "" ];
// 		}
// 	}
// 	if($debug) varDump2($payload);
// 	// if($debug) die();

// 	$product_id = false;
// 	if (isset($dados['product_id']) && !empty($dados['product_id'])) {
// 		$product_id = $dados['product_id'];
// 	} else {
// 		if (isset($dados['id']) && !empty($dados['id'])) {
// 			$product_id = $dados['id'];
// 		}
// 	}

// 	if ($product_id == false) {
// 		varDump2("ERRO ao executar tray_deletarImagem. product_id inválido.");
// 	} else {

// 		$request = new HTTP_Request2();
// 		$request->setUrl("{$_SESSION['tray']["api_host"]}/products/{$product_id}/images?access_token={$_SESSION['tray']['access_token']}");
// 		$request->setMethod(HTTP_Request2::METHOD_POST);
// 		$request->setConfig(array( 'follow_redirects' => TRUE ));
// 		$request->setHeader(array( 'Content-Type' => 'application/json'	));	
// 		$request->setBody(json_encode($payload));
// 		if($debug) varDump2($request);

// 		try {
// 		    $response = $request->send();
// 		    if ($response->getStatus() == 200) {
// 		        if($debug) varDump2($response->getBody());
// 		        insereModal("success", "Imagem do produto excluída com sucesso!</p><br><h5>Observação:</h5><p>Assim que realizado o envio, as imagens serão processadas em fila, podendo levar alguns minutos para serem exibidas no cadastro do produto.");
// 		        redireciona("index.php?op=172&product_id={$product_id}");
// 		        exit();
// 		    } else {
// 		        insereModal("danger", "Erro ao excluir a Imagem do produto. Código: " . $response->getStatus()."<br>".$response->getBody());
// 		    }
// 		} catch (HTTP_Request2_Exception $e) {
// 	        insereModal("danger", 'Erro na requisição: ' . $e->getMessage());
// 		}
// 	}
// }

function tray_resetarImagensProduto($product_id, $saldoTotal = 0){
	tray_validaToken();

	if (!isset($product_id) || empty($product_id)) {
		varDump2("ERRO ao executar tray_resetarImagensProduto. product_id inválido.");
		return false;
	} else {

		// Dados que você deseja alterar
		$payload = [
		    "Images" => [
				"picture_source_1" => 
					( 
						($saldoTotal > 0) 
						? "http://186.233.92.186:8000/portal/dist/img/tray_img_imediata.jpg"
						: "http://186.233.92.186:8000/portal/dist/img/tray_img_consulta.jpg" 
					),
				"picture_source_2" => "http://186.233.92.186:8000/portal/dist/img/tray_img_1.jpg",
				"picture_source_3" => "http://186.233.92.186:8000/portal/dist/img/tray_img_2.jpg",
				"picture_source_4" => "http://186.233.92.186:8000/portal/dist/img/tray_img_3.jpg",
				"picture_source_5" => "",
				"picture_source_6" => ""			]
		];
		// varDump2($payload);

		$request = new HTTP_Request2();
		$request->setUrl("{$_SESSION['tray']["api_host"]}/products/{$product_id}/images?access_token={$_SESSION['tray']['access_token']}");
		$request->setMethod(HTTP_Request2::METHOD_POST);	
		$request->setConfig(array( 'follow_redirects' => TRUE ));
		$request->setHeader(array( 'Content-Type' => 'application/x-www-form-urlencoded' ));
		$request->addPostParameter($payload);
		// varDump2($request);
		
		try {
		  $response = $request->send();
		  if ($response->getStatus() >= 200 && $response->getStatus() < 400) {
				// prompt('SUCESSO ao executar tray_resetarImagensProduto');
				return true;
		  } else {
				prompt('ERRO ao executar tray_resetarImagensProduto<br/>Unexpected HTTP status: ' . $response->getStatus() . ' ' . $response->getReasonPhrase());
				return false;
		  }
		} 
		catch(HTTP_Request2_Exception $e) {
		  die('Error: ' . $e->getMessage());
		  return false;
		}

	}
}