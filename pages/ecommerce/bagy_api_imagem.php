<?php 
$debug = true;

if (!function_exists('bagy_valida_token')) {
	function bagy_valida_token(){
		if (!defined('TOKEN')) {
	    define('TOKEN', "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzaG9wX2lkIjoxMzQxNTcsInR5cGUiOiJhcGkiLCJlbWFpbCI6IjU4NzgyNjQ0OTYzMTYyNTQwMDAwLmJhZ3lAYXBpLmNvbS5iciIsImZpcnN0X25hbWUiOiJWRU1BUCBXSU5USE9SIiwiYWN0aXZlIjp0cnVlLCJpYXQiOjE2OTMzNDEyNzF9.8KTYG7TjnpFtIzZnFnwNVC5emGksKt8-JPex0LhrDV0");
		}
	}
}

function bagy_imagem_cadastrar($product_id, $estoqueTotal = 0){
	

	if (empty($product_id)) {
		varDump2("ERRO ao executar bagy_imagem_cadastrar. product_id inválido ou nulo");
		return false;
	} else {
		$payload = [];
		if ($estoqueTotal == 0) {
			$payload[] = 	array(
				'src' => 'http://186.233.92.186:8000/portaldev/dist/img/tray_img_consulta.jpg',
				'position' => 1,
				'alt' => 'produto_sem_estoque'
			);
		} else {
			$payload[] = 	array(
				'src' => 'http://186.233.92.186:8000/portaldev/dist/img/tray_img_imediata.jpg',
				'position' => 1,
				'alt' => 'produto_com_estoque'
			);
		}
		$payload[] = 	array(
				'src' => 'http://186.233.92.186:8000/portaldev/dist/img/tray_img_1.jpg',
				'position' => 2,
				'alt' => 'vemap_1'
			);

		$payload[] = 	array(
				'src' => 'http://186.233.92.186:8000/portaldev/dist/img/tray_img_2.jpg',
				'position' => 3,
				'alt' => 'vemap_2'
			);
		$payload[] = 	array(
				'src' => 'http://186.233.92.186:8000/portaldev/dist/img/tray_img_3.jpg',
				'position' => 4,
				'alt' => 'vemap_3'
			);
		// varDump2($payload); die();

		$request = new HTTP_Request2();
		$request->setUrl("https://api.dooca.store/products/".$product_id."/images/batch");
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

function bagy_imagem_atualizar($produto_id, $image_id, $campos){
	

}

function bagy_imagem_excluir($produto_id, $image_id){
	

	if (empty($produto_id)) {
		insereModal("danger", "ERRO ao executar bagy_produto_excluir. produto_id inválido ou nulo");
		return false;
	} else {
		if (empty($image_id)) {
			insereModal("danger", "ERRO ao executar bagy_produto_excluir. image_id inválido ou nulo");
			return false;
		} else {

			$request = new HTTP_Request2();
			$request->setUrl("https://api.dooca.store/products/".$produto_id."/images/".$image_id);
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
				return json_decode($response->getBody(), true);
			}
			catch(HTTP_Request2_Exception $e) {
				varDump2($e->getMessage());
				return false;
			}
		}
	}
}