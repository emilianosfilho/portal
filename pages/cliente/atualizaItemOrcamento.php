<?php 

	$QTPEDIDA_OLD 		= intval($dados['QTPEDIDA_OLD']);
	$QTPEDIDA_NEW 		= intval($dados['QTPEDIDA_NEW']);
	$PVENDA_OLD 		= ($dados['PVENDA_OLD']);
	$PVENDA_NEW 		= ($dados['PVENDA_NEW']);

	// varDump2($dados);
	// varDump2($PVENDA_NEW);
	// varDump2($PTABELA);
	// die();


	$ok=true;


	if($ok){

		$sql = "UPDATE ORCORCAMENTOI I SET
			I.QTPEDIDA = ".$QTPEDIDA_NEW."
		 WHERE I.IDORCAMENTO = ".$dados['IDORCAMENTO']."
		   AND I.IDORCAMENTOI = ".$dados['IDORCAMENTOI'];
		$ret = executarOracle($sql);
		// varDump2($sql);
		// varDump2($ret);

		// varDump2($dados);
		// varDump2($_SESSION['ORC_CLIENTE']['ITEM'][$dados['key']]);

		$_SESSION['ORC_CLIENTE']['ITEM'][$dados['key']]['QTPEDIDA'] = intval($QTPEDIDA_NEW);


		$sql = "INSERT INTO ORCLOGALTPRECO (
			  IDLOG,
			  DATA,
			  IDUSUARIO,
			  CODCLI,
			  IDORCAMENTO,
			  CODPROD,
			  QTPEDIDA_OLD,
			  QTPEDIDA_NEW,
			  PVENDA_OLD,
			  PVENDA_NEW
			) VALUES (
			  /*IDLOG*/NULL,
			  /*DATA*/SYSDATE,
			  /*IDUSUARIO*/".$_SESSION['login']['IDUSUARIO'].",
			  /*CODCLI*/".$dados['CODCLI'].",
			  /*IDORCAMENTO*/".$dados['IDORCAMENTO'].",
			  /*CODPROD*/'".$dados['CODPROD']."',
			  /*QTPEDIDA_OLD*/".$QTPEDIDA_OLD.",
			  /*QTPEDIDA_NEW*/".$QTPEDIDA_NEW.",
			  /*PVENDA_OLD*/".$PVENDA_OLD.",
			  /*PVENDA_NEW*/".$PVENDA_NEW."
			)";
		// varDump2($sql);
		$ret = executarOracle($sql);
		// varDump2($ret);
		return $ret;
	}
	// die();

?>