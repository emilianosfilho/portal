<?php

$regMin = intval(0);
$regMax = intval(900000);
$prefixo = "0";

$sql = "select  * from vide where codpeca <> '' and codpeca is not null and id >= ".$regMin." and id < ".$regMax;
$ret = selectOracle($sql);
// varDump2($sql);
// varDump2($ret);
// die();

$maxRegArquivo = intval(100000);
$maxRegScript = intval(1000);
$qtdRegistros = count($ret);
$qtdArquivos = ceil($qtdRegistros / $maxRegArquivo);

if ($maxRegArquivo > $qtdRegistros) {
	$maxRegArquivo = $qtdRegistros;
}

echo "<h1>Geração de arquivos bkp vide</h1>";
echo "<h3>".$qtdArquivos." Arquivos com Prefixo: ".$prefixo." </h3>";
echo "<h3>".$qtdRegistros." Registros encontrados</h3>";
echo "<h3>".$maxRegArquivo." maxRegArquivo</h3>";
echo "<h3>".$maxRegScript." maxRegScript</h3>";

unset($LISTA_SCRIPTS);
$LISTA_SCRIPTS = array();



foreach ($ret as $key => $value) {

	if ($key == 0) {
		$paginaAtual = 1;
		$scriptAtual = 1;
	} else {
		$paginaAtual = ceil(($key+1) / $maxRegArquivo);
		$scriptAtual = ceil(($key+1) / $maxRegScript);
	}

	if (!isset($LISTA_SCRIPTS[$paginaAtual])) {
		$LISTA_SCRIPTS[$paginaAtual] = array();
	}

	// echo "<p>Pagina: ".$paginaAtual."	| Srcipt: ".$scriptAtual."	| Registro atual: ".($contador+1)."	</p>";

	if ($value['inportado'] == "IMP." || $value['inportado'] == "I"){
		$value['inportado'] = "'IMP.'";
	} else {
		$value['inportado'] = "'NAC.'";
	}

	if ($value['preco'] == "") {
		$value['preco'] = "NULL";
	} else {
		$value['preco'] = str_replace(",", ".", str_replace(".", "", $value['preco']));
	}

	if ($value['aplicmarca'] == ""){
		$value['aplicmarca'] = "NULL";
	} else {
		$value['aplicmarca'] = str_replace(":", " ", $value['aplicmarca']);
		$value['aplicmarca'] = str_replace("'", " ", $value['aplicmarca']);
		$value['aplicmarca'] = str_replace("&", " ", $value['aplicmarca']);
		$value['aplicmarca'] = "'".$value['aplicmarca']."'";
	}

	if ($value['vide'] == ""){
		$value['vide'] = "NULL";
	} else {
		$value['vide'] = str_replace(":", " ", $value['vide']);
		$value['vide'] = str_replace("'", " ", $value['vide']);
		$value['vide'] = str_replace("&", " ", $value['vide']);
		$value['vide'] = "'".$value['vide']."'";
	}

	if ($value['marca'] == ""){
		$value['marca'] = "NULL";
	} else {
		$value['marca'] = str_replace(":", " ", $value['marca']);
		$value['marca'] = str_replace("'", " ", $value['marca']);
		$value['marca'] = str_replace("&", " ", $value['marca']);
		$value['marca'] = "'".$value['marca']."'";
	}

	if ($value['descricao'] == ""){
		$value['descricao'] = "NULL";
	} else {
		$value['descricao'] = str_replace(":", " ", $value['descricao']);
		$value['descricao'] = str_replace("'", " ", $value['descricao']);
		$value['descricao'] = str_replace("&", " ", $value['descricao']);
		$value['descricao'] = "'".$value['descricao']."'";
	}

	if ($value['nomeopcao'] == ""){
		$value['nomeopcao'] = "NULL";
	} else {
		$value['nomeopcao'] = str_replace(":", " ", $value['nomeopcao']);
		$value['nomeopcao'] = str_replace("'", " ", $value['nomeopcao']);
		$value['nomeopcao'] = str_replace("&", " ", $value['nomeopcao']);
		$value['nomeopcao'] = "'".$value['nomeopcao']."'";
	}

	$linha_script .= "
	into orcamento_vide (id, codpeca, aplicmarca, vide, descricao, marca, importado, preco, data, nomeopcao, tipo) values "
	. "(".$value['id'].","
	. " '".$value['codpeca']."',"
	. "  ".$value['aplicmarca'].","
	. "  ".$value['vide']." ,"
	. "  ".$value['descricao'].","
	. "  ".$value['marca'].","
	. "  ".$value['inportado'].","
	. "  ".$value['preco']." ,"
	. "  SYSDATE,"
	. "  ".$value['nomeopcao']." ,"
	. " '".$value['tipo']."')";
	
	$criaScript = false;
	if (($contador+1) == $maxRegScript) {
		$criaScript = true;
	} else {
		if (($contador+1) == $qtdRegistros) {
			$criaScript = true;
		}
	}

	if ($criaScript) {
		$script = "insert all ".$linha_script . "
		select * from dual;";
		$linha_script = "";
		$LISTA_SCRIPTS[$paginaAtual][$scriptAtual] = $script;
	}

	$contador++;
	

}
// varDump2($LISTA_SCRIPTS); die();


foreach ($LISTA_SCRIPTS as $keyPagina => $valuePagina) {
	$file_name = date("Y-m-d")."_ARQUIVOVIDE_".$prefixo.($keyPagina).".sql"; 
	$filehandle = fopen($file_name,'w'); 
	foreach ($valuePagina as $keyScript => $valueScript) {
		fwrite($filehandle,$valueScript."\n"); 
	}
	fclose($filehandle); 
	if (file_exists($file_name)) {
		echo '<p><a href="'.$file_name.'">'.$file_name.'</a></p>';
	}
}
