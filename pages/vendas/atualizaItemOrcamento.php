<?php 

	$QTPEDIDA_NEW 		= intval($dados['QTPEDIDA_NEW']);
	$QTPEDIDA_OLD 		= intval($dados['QTPEDIDA_OLD']);

	$PVENDA_NEW 		= floatval(str_replace(",", ".", str_replace(".", "", $dados['PVENDA_NEW'])));
	$PVENDA_OLD 		= floatval($dados['PVENDA_OLD']);
	$PTABELA 			= floatval($dados['PTABELA']);
	$PVENDAMIN 			= floatval($dados['PVENDAMIN']);

	$CODPECA_NEW 		= strtoupper(str_replace("'", " ", $dados['CODPECA_NEW']));
	$CODPECA_OLD 		= strtoupper(str_replace("'", " ", $dados['CODPECA_OLD']));
	$DESCRICAO_NEW		= strtoupper(str_replace("'", " ", $dados['DESCRICAO']));
	$DESCRICAO_OLD		= strtoupper(str_replace("'", " ", $dados['DESCRICAO_OLD']));
	$MARCA_NEW 			= strtoupper(str_replace("'", " ", $dados['MARCA']));
	$MARCA_OLD 			= strtoupper(str_replace("'", " ", $dados['MARCA_OLD']));
	$DISPONIBILIDADE 	= strtoupper(str_replace("'", " ", $dados['DISPONIBILIDADE']));

	// varDump2($dados);
	// varDump2($PVENDA_NEW);
	// varDump2($PTABELA);
	// die();


	//VALIDA SE EXISTE PREÇO AUTORIZADO PARA A VENDA ESPECÍFICA
	//CADASTRADO NA ROTINA 301
	#######################################################################################
	$precoAutorizado = buscaPrecoAutorizado($_SESSION['ORCAMENTO']['CAB']['CODUSUR'], 
											$_SESSION['ORCAMENTO']['CAB']['CODCLI'], 
											$_SESSION['ORCAMENTO']['CAB']['CODPLPAG'],
											$dados['CODPROD']);
	if ($precoAutorizado) {
		insereModal("info", "Este produto possui preço promocional cadastrado no valor de  ".moeda($precoAutorizado+(0.01))."!");
		$PTABELA = min($PTABELA, $precoAutorizado);
	}
	#######################################################################################


	$ok=true;

	if ($PVENDA_NEW < $PVENDAMIN) {
		insereModal("warning", "Preço de venda não permitido <br/>P. Venda: R$ ".moeda($PVENDA_NEW, 2)."<br/>P. Venda Mínimo: R$ ".moeda($PVENDAMIN, 2));
		$PVENDA_NEW = $PVENDA_OLD;
	}
	if ($CODPECA_NEW == "") {
		$ok = false;
		insereModal("warning", "O campo CODPECA não pode estar vazio!");
	}
	if ($DISPONIBILIDADE == "") {
		$ok = false;
		insereModal("warning", "O campo ENTREGA não pode estar vazio!");
	}
	if ($DESCRICAO_NEW == "") {
		$ok = false;
		insereModal("warning", "O campo DESCRIÇÃO não pode estar vazio!");
	}
	if ($MARCA_NEW == "") {
		$ok = false;
		insereModal("warning", "O campo MARCA não pode estar vazio!");
	}

	if($ok){

		$sql = "UPDATE ORCORCAMENTOI I SET
			I.QTPEDIDA = ".$QTPEDIDA_NEW.",
			I.PVENDA = ".$PVENDA_NEW.",
			I.CODPECA = '".$CODPECA_NEW."',
			I.DESCRICAO = '".$DESCRICAO_NEW."',
			I.MARCA = '".$MARCA_NEW."',
			I.DISPONIBILIDADE = '".$DISPONIBILIDADE."'
		 WHERE I.IDORCAMENTO = ".$dados['IDORCAMENTO']."
		   AND I.IDORCAMENTOI = ".$dados['IDORCAMENTOI'];
		$ret = executarOracle($sql);
		// varDump2($sql);
		// varDump2($ret);

		$_SESSION['ORCAMENTO']['ITEM'][$dados['key']]['QTPEDIDA'] 			= $QTPEDIDA_NEW;
		$_SESSION['ORCAMENTO']['ITEM'][$dados['key']]['PVENDA'] 			= $PVENDA_NEW;
		$_SESSION['ORCAMENTO']['ITEM'][$dados['key']]['CODPECA'] 			= $CODPECA_NEW;
		$_SESSION['ORCAMENTO']['ITEM'][$dados['key']]['DESCRICAO'] 			= $DESCRICAO_NEW;
		$_SESSION['ORCAMENTO']['ITEM'][$dados['key']]['MARCA'] 				= $MARCA_NEW;
		$_SESSION['ORCAMENTO']['ITEM'][$dados['key']]['DISPONIBILIDADE'] 	= $DISPONIBILIDADE;


		$sql = "INSERT INTO ORCLOGALTPRECO (
			  IDLOG,
			  DATA,
			  IDUSUARIO,
			  CODCLI,
			  IDORCAMENTO,
			  CODPROD,
			  CODPECA_OLD,
			  CODPECA_NEW,
			  DESCRICAO_OLD,
			  DESCRICAO_NEW,
			  QTPEDIDA_OLD,
			  QTPEDIDA_NEW,
			  PVENDA_OLD,
			  PVENDA_NEW,
			  MARCA_OLD,
			  MARCA_NEW
			) VALUES (
			  /*IDLOG*/NULL,
			  /*DATA*/SYSDATE,
			  /*IDUSUARIO*/".$_SESSION['login']['IDUSUARIO'].",
			  /*CODCLI*/".$dados['CODCLI'].",
			  /*IDORCAMENTO*/".$dados['IDORCAMENTO'].",
			  /*CODPROD*/'".$dados['CODPROD']."',
			  /*CODPECA_OLD*/'".$CODPECA_OLD."',
			  /*CODPECA_NEW*/'".$CODPECA_NEW."',
			  /*DESCRICAO_OLD*/'".$DESCRICAO_OLD."',
			  /*DESCRICAO_NEW*/'".$DESCRICAO_NEW."',
			  /*QTPEDIDA_OLD*/".$QTPEDIDA_OLD.",
			  /*QTPEDIDA_NEW*/".$QTPEDIDA_NEW.",
			  /*PVENDA_OLD*/".$PVENDA_OLD.",
			  /*PVENDA_NEW*/".$PVENDA_NEW.",
			  /*MARCA_OLD*/'".$MARCA_OLD."',
			  /*MARCA_NEW*/'".$MARCA_NEW."'
			)";
		// varDump2($sql);
		$ret = executarOracle($sql);
		// varDump2($ret);
		return $ret;
	}
	// die();

?>