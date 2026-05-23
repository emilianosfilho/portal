<?php 
function tray_validaToken(){
	// $debug = true;
	if($debug) varDump2("tray_validaToken");

	$tipoConexao = array('tipo' => "PRODUCAO"); // HOMOLOGACAO ou PRODUCAO
	if($debug) varDump2($tipoConexao);

	while (!isset($_SESSION['tray']['access_token'])) {
		$_SESSION['tray'] = buscaChavesAcessoTray($tipoConexao);
	}
	if($debug) varDump2($_SESSION['tray']);

	$hoje = @date('Y-m-d H:i:s', strtotime('+1 hours'));
	$date_expiration = $_SESSION['tray']["date_expiration_access_token"];
	if($debug) varDump2("hoje: {$hoje}");
	if($debug) varDump2("date_expiration: {$date_expiration}");

	if (strtotime($date_expiration) < strtotime($hoje)) {
		if($debug) varDump2("token inválido ou expirado");

		$novaChave = (array) json_decode(json_decode(tray_gerarChaveAcesso($_SESSION['tray'])));
		if($debug) varDump2($novaChave);

		if ($novaChave["code"] == 200) {
			$_SESSION['tray'] = $novaChave;
			atualizaDadosToken($tipoConexao, $novaChave);
		} else {
			unset($_SESSION['tray']);
		}

	} else {
		if($debug) varDump2("token válido");
	}

}



function buscaChavesAcessoTray($dados){
	// $debug = true;
	if($debug) varDump2("buscaChavesAcessoTray");

	$sql = "select t.tipo,
			       t.store_id,
			       t.api_address,
			       t.api_host,
			       t.consumer_key,
			       t.consumer_secret,
			       t.code,
			       t.access_token,
			       t.refresh_token,
			       TO_CHAR(t.date_expiration_access_token, 'YYYY-MM-DD HH24:MI:SS') as date_expiration_access_token,
			       TO_CHAR(t.date_expiration_refresh_token, 'YYYY-MM-DD HH24:MI:SS') as date_expiration_refresh_token,
			       TO_CHAR(t.date_activated, 'YYYY-MM-DD HH24:MI:SS') as date_activated
			  from tray_define t
			  where t.tipo = UPPER(TRIM('{$dados["tipo"]}'))";
	
	if ($dados["tipo"] && !empty($dados["tipo"])) {
		if($ret = reset(selectOracle($sql))){
			$ret2 = array();
			foreach ($ret as $key => $value) {
				$ret2[mb_strtolower($key)] = $value;
			}
			if($debug) varDump2($sql);
			if($debug) varDump2($ret2);
			return $ret2;
		} else {
			varDump2($dados);
			varDump2($sql);
			return false;
		}
	} else {
		varDump2($dados);
		prompt("ERRO ao executar a função buscaChavesAcessoTray. Tipo não informado!");
		return false;
	}
}

function tray_gerarChaveAcesso($tray){
	// $debug = true;
	if($debug) varDump2("tray_gerarChaveAcesso");
	if($debug) varDump2($tray);

	$request = new HTTP_Request2();
	$request->setUrl( "{$tray['api_address']}/auth");
	$request->setMethod(HTTP_Request2::METHOD_POST);
	$request->setConfig(array(
		'follow_redirects' => TRUE
	));
	$request->addPostParameter(array(
		'consumer_key' => "{$tray['consumer_key']}",
		'consumer_secret' => "{$tray['consumer_secret']}",
		'code' => "{$tray['code']}"
	));
	// varDump2($request);
	try {
		$response = $request->send();
		if ($response->getStatus() == 200 || $response->getStatus() == 201) {
			if($debug) varDump2("tray_gerarChaveAcesso executada com sucesso!");
			return json_encode($response->getBody(), true);
		}
		else {
			if($debug) varDump2("tray_gerarChaveAcesso executada com ERRO!");
			if($debug) varDump2($tray);
			if($debug) varDump2($response);
			if($debug) varDump2(json_decode($response->getBody()));
			prompt('Unexpected HTTP status: ' . $response->getStatus() . ' ' . $response->getReasonPhrase());
			return false;
		}
	} 
	catch(HTTP_Request2_Exception $e) {
	  prompt('Error: ' . $e->getMessage());
	  return tray_gerarChaveAcesso($tray);
	}
}




function atualizarChavesAcesso($tray){
	// $debug = true;
	if($debug) varDump2("atualizarChavesAcesso");
	if($debug) varDump2($tray);

	$request = new HTTP_Request2();
	$request->setUrl( "{$tray['api_address']}/auth.php?refresh_token={$tray["refresh_token"]}");
	$request->setMethod(HTTP_Request2::METHOD_GET);
	$request->setConfig(array(
		'follow_redirects' => TRUE
	));
	try {
		$response = $request->send();
		if ($response->getStatus() == 200) {
			return $response;
		}
		else {
			if($debug) varDump2($request);
			if($debug) varDump2(json_decode($response->getBody()));
			prompt('Unexpected HTTP status: ' . $response->getStatus() . ' ' . $response->getReasonPhrase());
			return false;
		}
	} 
	catch(HTTP_Request2_Exception $e) {
	  prompt('Error: ' . $e->getMessage());
	  return false;
	}
}

function tray_autenticacao($tray){
	// $debug = true;
	if($debug) varDump2("tray_autenticacao");
	if($debug) varDump2($tray);


	$request = new HTTP_Request2();
	$request->setUrl( "{$tray['api_address']}/auth");
	$request->setMethod(HTTP_Request2::METHOD_POST);
	$request->setConfig(array(
		'follow_redirects' => TRUE
	));
	$request->addPostParameter(array(
		'consumer_key' => "{$tray['consumer_key']}",
		'consumer_secret' => "{$tray['consumer_secret']}",
		'code' => "{$tray['code']}"
	));
	// varDump2($request);
	try {
		$response = $request->send();
		if ($response->getStatus() == 200 || $response->getStatus() == 201) {
			if($debug) varDump2("tray_autenticacao executada com sucesso!");
			return json_encode($response->getBody(), true);
		}
		else {
			if($debug) varDump2($tray);
			if($debug) varDump2($response);
			if($debug) varDump2(json_decode($response->getBody()));
			prompt('Unexpected HTTP status: ' . $response->getStatus() . ' ' . $response->getReasonPhrase());
			return false;
		}
	} 
	catch(HTTP_Request2_Exception $e) {
	  prompt('Error: ' . $e->getMessage());
	  return false;
	}
}

function atualizaDadosToken($tipoConexao, $novaChave){
	// varDump2("Entrou no atualizaDadosToken");
	// varDump2($data);
	$sql = "UPDATE TRAY_DEFINE SET
				access_token = '".$novaChave['access_token']."',
				refresh_token = '".$novaChave['refresh_token']."',
				date_expiration_access_token = TO_DATE('".$novaChave['date_expiration_access_token']."', 'YYYY-MM-DD HH24:MI:SS'),
				date_expiration_refresh_token = TO_DATE('".$novaChave['date_expiration_refresh_token']."', 'YYYY-MM-DD HH24:MI:SS'),
				date_activated = TO_DATE('".$novaChave['date_activated']."', 'YYYY-MM-DD HH24:MI:SS')
			WHERE TIPO = '".$tipoConexao['tipo']."'";
	if ($tipoConexao['tipo'] && !empty($tipoConexao['tipo'])) {
		// varDump2($sql);
		return executarOracle($sql);
	} else {
		varDump2($data);
		varDump2($sql);
		prompt("ERRO ao executar a função atualizaDadosToken. Tipo não informado!");
		return false;
	}
}

?>