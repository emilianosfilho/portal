<?php

function buscaTodosEstados(){
	$sql = "SELECT UF 
			  FROM PCCIDADE 
			 WHERE LATITUDE IS NOT NULL 
			 GROUP BY UF 
			 ORDER BY UF";
	return selectOracle($sql);
}

function buscarMunicipiosUF($UF){
	$sql = "SELECT NOMECIDADE AS MUNICIPIO 
			FROM PCCIDADE 
			WHERE UF = '".$UF."'
			  AND LATITUDE IS NOT NULL 
			GROUP BY NOMECIDADE 
			ORDER BY NOMECIDADE";
	return selectOracle($sql);
}


function validarFichaPF($dados){
	// varDump2($dados);
	if (validar_cpf($dados['CPF'])) {
		exibeMensagem("CPF informado ".$dados['CPF']." é inválido!");
		return false;
	} else {
		if(cpfExiste($dados['CPF'])){
			// varDump2('atualizaFicha');
			deletarFicha($dados);
		} else {
			// varDump2('cadastraFicha');
		}
		cadastraFicha($dados);
		return true;
	}
}

function buscaDadosFichaID($IDFICHACADASTRAL){
	$sql = "SELECT * FROM CLIFICHACADASTRAL WHERE IDFICHACADASTRAL = ".$IDFICHACADASTRAL;
	if ($ret = selectOracle($sql)) {
		return reset($ret);
	} else {
		return false;
	}
}

function buscaDadosFichaCGC($dados){
	$sql = "SELECT * FROM CLIFICHACADASTRAL WHERE 1=1";
	if ($dados['TIPOFJ'] == "J" && !empty($dados['CNPJ'])) {
		$sql .= "	AND CNPJ = '".mb_strtoupper(trim($dados['CNPJ']), 'UTF-8')."'";
	} else if ($dados['TIPOFJ'] == "F" && !empty($dados['CPF'])) {
		$sql .= "	AND CPF = '".mb_strtoupper(trim($dados['CPF']), 'UTF-8')."'";
	} else {
		exibeMensagem("Erro ao buscar a ficha cadastral");
		return false;
	}
	if ($ret = selectOracle($sql)) {
		return reset($ret);
	} else {
		return false;
	}
}


function buscaFichaCNPJ($CNPJ){
	$sql = "SELECT * FROM CLIFICHACADASTRAL WHERE CNPJ = '{$CNPJ}' ORDER BY IDFICHACADASTRAL DESC";
	// varDump2($sql);
	$ret = selectOracle($sql);
	// varDump2($ret);
	// die();
	if ($ret) {
		return reset($ret);
	} else {
		return $ret;
	}
}


function buscaFichaID($IDFICHACADASTRAL){
	$sql = "SELECT * FROM CLIFICHACADASTRAL WHERE IDFICHACADASTRAL = {$IDFICHACADASTRAL}";
	$ret = selectOracle($sql);
	// varDump2($sql);
	// varDump2($ret);
	// die();
	if ($ret) {
		return reset($ret);
	} else {
		return $ret;
	}
}

function buscaFichaCPF($CPF){
	$sql = "SELECT * FROM CLIFICHACADASTRAL WHERE CPF = '".$CPF."'";
	// varDump2($sql); 
	// varDump2($ret);
	// die();
	if ($ret = selectOracle($sql)) {
		return reset($ret);
	} else {
		return false;
	}
}


function cpfExiste($CPF){
	$sql = "SELECT * FROM CLIFICHACADASTRAL WHERE CPF = '".$CPF."'";
	// varDump2($sql);
	if ($ret = selectOracle($sql)) {
		return reset($ret);
	} else {
		return false;
	}
}

function cnpjExiste($cnpj){
	$sql = "SELECT * FROM CLIFICHACADASTRAL WHERE CNPJ = '".$cnpj."'";
	// varDump2($sql);
	if ($ret = selectOracle($sql)) {
		return reset($ret);
	} else {
		return false;
	}
}


function validar_cnpj($cnpj) {
  // Extrai somente os números

  	if ($cnpj == '11.111.111/1111-11') {
  		return '11.111.111/1111-11';
  	} else {

	  	$cnpj = preg_replace( '/[^0-9]/is', '', $cnpj );	
		
		// Valida tamanho
		if (strlen($cnpj) != 14)
			return false;

		// Verifica se todos os digitos são iguais
		if (preg_match('/(\d)\1{13}/', $cnpj))
			return false;	

		// Valida primeiro dígito verificador
		for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
		{
			$soma += $cnpj[$i] * $j;
			$j = ($j == 2) ? 9 : $j - 1;
		}

		$resto = $soma % 11;

		if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
			return false;

		// Valida segundo dígito verificador
		for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
		{
			$soma += $cnpj[$i] * $j;
			$j = ($j == 2) ? 9 : $j - 1;
		}

		$resto = $soma % 11;

		return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);

  	}
}


function validar_cpf($cpf) {
 
    // Extrai somente os números
    $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
     
    // Verifica se foi informado todos os digitos corretamente
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Faz o calculo para validar o CPF
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

function buscaFichasRecentes(){
	$sql = "SELECT * FROM CLIFICHACADASTRAL 
					WHERE (CPF IS NOT NULL OR CNPJ IS NOT NULL)
					order by data desc";
	// varDump2($sql); 
	// varDump2($ret);
	// die();
	if ($ret = selectOracle($sql)) {
		return $ret;
	} else {
		return false;
	}
}



function criaNovaFicha($dados){
	if ($dados['TIPOFJ'] == "J") {
		$sql = "INSERT INTO CLIFICHACADASTRAL (IDFICHACADASTRAL, DATA, TIPOFJ, CNPJ, RAZAOSOCIAL, INSCESTADUAL, ENDERECO, NUMERO, COMPLEMENTO, CEP, BAIRRO, UF, MUNICIPIO, CONTATONOME, EMAIL, TELCELULAR, TELFIXO, OBSERVACOES, FINANCEIRONOME, FINANCEIROEMAIL, TELCELULARFINANCEIRO, TELFINANCEIRO, FINANCEIROOBS, COMPRADOR1NOME, COMPRADOR1CPF, COMPRADOR1CELULAR, COMPRADOR2NOME, COMPRADOR2CPF, COMPRADOR2CELULAR, COMPRADOR3NOME, COMPRADOR3CPF, COMPRADOR3CELULAR, COMPENDERECO1) VALUES (
			/*IDFICHACADASTRAL*/ (SELECT MAX(IDFICHACADASTRAL)+1 FROM CLIFICHACADASTRAL), 
			/*DATA*/ SYSDATE, 
			/*TIPOFJ*/ '".trim($dados['TIPOFJ'])."',
			/*CNPJ*/ '".trim($dados['CNPJ'])."', 
			/*RAZAOSOCIAL*/ '".(empty($dados['RAZAOSOCIAL'])?'':mb_strtoupper(trim($dados['RAZAOSOCIAL']),'UTF-8'))."', 
			/*INSCESTADUAL*/ '".trim($dados['INSCESTADUAL'])."', 
			/*ENDERECO*/ '".(empty($dados['ENDERECO'])?'':mb_strtoupper(trim($dados['ENDERECO']),'UTF-8'))."', 
			/*NUMERO*/ '".(empty($dados['NUMERO'])?'':mb_strtoupper(trim($dados['NUMERO']),'UTF-8'))."', 
			/*COMPLEMENTO*/ '".(empty($dados['COMPLEMENTO'])?'':mb_strtoupper(trim($dados['COMPLEMENTO']),'UTF-8'))."', 
			/*CEP*/ '".(empty($dados['CEP'])?'':mb_strtoupper(trim($dados['CEP']),'UTF-8'))."', 
			/*BAIRRO*/ '".(empty($dados['BAIRRO'])?'':mb_strtoupper(trim($dados['BAIRRO']),'UTF-8'))."', 
			/*UF*/ '".(empty($dados['UF'])?'':mb_strtoupper(trim($dados['UF']),'UTF-8'))."', 
			/*MUNICIPIO*/ '".(empty($dados['MUNICIPIO'])?'':mb_strtoupper(trim($dados['MUNICIPIO']),'UTF-8'))."', 
			/*CONTATONOME*/ '".(empty($dados['CONTATONOME'])?'':mb_strtoupper(trim($dados['CONTATONOME']),'UTF-8'))."', 
			/*EMAIL*/ '".(empty($dados['EMAIL'])?'':mb_strtolower(trim($dados['EMAIL']),'UTF-8'))."', 
			/*TELCELULAR*/ '".(empty($dados['TELCELULAR'])?'':mb_strtoupper(trim($dados['TELCELULAR']),'UTF-8'))."', 
			/*TELFIXO*/ '".(empty($dados['TELFIXO'])?'':mb_strtoupper(trim($dados['TELFIXO']),'UTF-8'))."', 
			/*OBSERVACOES*/ '".(empty($dados['OBSERVACOES'])?'':mb_strtoupper(trim($dados['OBSERVACOES']),'UTF-8'))."', 
			/*FINANCEIRONOME*/ '".(empty($dados['FINANCEIRONOME'])?'':mb_strtoupper(trim($dados['FINANCEIRONOME']),'UTF-8'))."', 
			/*FINANCEIROEMAIL*/ '".(empty($dados['FINANCEIROEMAIL'])?'':mb_strtoupper(trim($dados['FINANCEIROEMAIL']),'UTF-8'))."', 
			/*TELCELULARFINANCEIRO*/ '".(empty($dados['TELCELULARFINANCEIRO'])?'':mb_strtoupper(trim($dados['TELCELULARFINANCEIRO']),'UTF-8'))."', 
			/*TELFINANCEIRO*/ '".(empty($dados['TELFINANCEIRO'])?'':mb_strtoupper(trim($dados['TELFINANCEIRO']),'UTF-8'))."', 
			/*FINANCEIROOBS*/ '".(empty($dados['FINANCEIROOBS'])?'':mb_strtoupper(trim($dados['FINANCEIROOBS']),'UTF-8'))."', 
			/*COMPRADOR1NOME*/ '".(empty($dados['COMPRADOR1NOME'])?'':mb_strtoupper(trim($dados['COMPRADOR1NOME']),'UTF-8'))."', 
			/*COMPRADOR1CPF*/ '".(empty($dados['COMPRADOR1CPF'])?'':mb_strtoupper(trim($dados['COMPRADOR1CPF']),'UTF-8'))."', 
			/*COMPRADOR1CELULAR*/ '".(empty($dados['COMPRADOR1CELULAR'])?'':mb_strtoupper(trim($dados['COMPRADOR1CELULAR']),'UTF-8'))."', 
			/*COMPRADOR2NOME*/ '".(empty($dados['COMPRADOR2NOME'])?'':mb_strtoupper(trim($dados['COMPRADOR2NOME']),'UTF-8'))."', 
			/*COMPRADOR2CPF*/ '".(empty($dados['COMPRADOR2CPF'])?'':mb_strtoupper(trim($dados['COMPRADOR2CPF']),'UTF-8'))."', 
			/*COMPRADOR2CELULAR*/ '".(empty($dados['COMPRADOR2CELULAR'])?'':mb_strtoupper(trim($dados['COMPRADOR2CELULAR']),'UTF-8'))."', 
			/*COMPRADOR3NOME*/ '".(empty($dados['COMPRADOR3NOME'])?'':mb_strtoupper(trim($dados['COMPRADOR3NOME']),'UTF-8'))."', 
			/*COMPRADOR3CPF*/ '".(empty($dados['COMPRADOR3CPF'])?'':mb_strtoupper(trim($dados['COMPRADOR3CPF']),'UTF-8'))."', 
			/*COMPRADOR3CELULAR*/ '".(empty($dados['COMPRADOR3CELULAR'])?'':mb_strtoupper(trim($dados['COMPRADOR3CELULAR']),'UTF-8'))."', 
			/*COMPENDERECO1*/ '".(empty($dados['COMPENDERECO1'])?'':mb_strtoupper(trim($dados['COMPENDERECO1']),'UTF-8'))."'
		)";
	} else {
		$sql = "INSERT INTO CLIFICHACADASTRAL (IDFICHACADASTRAL, DATA, TIPOFJ, CPF, RG, NOME, PROFISSAO, ENDERECO, NUMERO, COMPLEMENTO, CEP, BAIRRO, UF, MUNICIPIO, CONTATONOME, EMAIL, TELCELULAR, TELFIXO, OBSERVACOES, FINANCEIRONOME, FINANCEIROEMAIL, TELCELULARFINANCEIRO, TELFINANCEIRO, FINANCEIROOBS, COMPRADOR1NOME, COMPRADOR1CPF, COMPRADOR1CELULAR, COMPRADOR2NOME, COMPRADOR2CPF, COMPRADOR2CELULAR, COMPRADOR3NOME, COMPRADOR3CPF, COMPRADOR3CELULAR, COMPENDERECO1) VALUES (
			/*IDFICHACADASTRAL*/ (SELECT MAX(IDFICHACADASTRAL)+1 FROM CLIFICHACADASTRAL), 
			/*DATA*/ SYSDATE, 
			/*TIPOFJ*/ '".trim($dados['TIPOFJ'])."',
			/*CPF*/ '".trim($dados['CPF'])."', 
			/*RG*/ '".trim($dados['RG'])."', 
			/*NOME*/ '".(empty($dados['NOME'])?'':mb_strtoupper(trim($dados['NOME']),'UTF-8'))."', 
			/*PROFISSAO*/ '".(empty($dados['PROFISSAO'])?'':mb_strtoupper(trim($dados['PROFISSAO']),'UTF-8'))."', 
			/*ENDERECO*/ '".(empty($dados['ENDERECO'])?'':mb_strtoupper(trim($dados['ENDERECO']),'UTF-8'))."', 
			/*NUMERO*/ '".(empty($dados['NUMERO'])?'':mb_strtoupper(trim($dados['NUMERO']),'UTF-8'))."', 
			/*COMPLEMENTO*/ '".(empty($dados['COMPLEMENTO'])?'':mb_strtoupper(trim($dados['COMPLEMENTO']),'UTF-8'))."', 
			/*CEP*/ '".(empty($dados['CEP'])?'':mb_strtoupper(trim($dados['CEP']),'UTF-8'))."', 
			/*BAIRRO*/ '".(empty($dados['BAIRRO'])?'':mb_strtoupper(trim($dados['BAIRRO']),'UTF-8'))."', 
			/*UF*/ '".(empty($dados['UF'])?'':mb_strtoupper(trim($dados['UF']),'UTF-8'))."', 
			/*MUNICIPIO*/ '".(empty($dados['MUNICIPIO'])?'':mb_strtoupper(trim($dados['MUNICIPIO']),'UTF-8'))."', 
			/*CONTATONOME*/ '".(empty($dados['CONTATONOME'])?'':mb_strtoupper(trim($dados['CONTATONOME']),'UTF-8'))."', 
			/*EMAIL*/ '".(empty($dados['EMAIL'])?'':mb_strtolower(trim($dados['EMAIL']),'UTF-8'))."', 
			/*TELCELULAR*/ '".(empty($dados['TELCELULAR'])?'':mb_strtoupper(trim($dados['TELCELULAR']),'UTF-8'))."', 
			/*TELFIXO*/ '".(empty($dados['TELFIXO'])?'':mb_strtoupper(trim($dados['TELFIXO']),'UTF-8'))."', 
			/*OBSERVACOES*/ '".(empty($dados['OBSERVACOES'])?'':mb_strtoupper(trim($dados['OBSERVACOES']),'UTF-8'))."', 
			/*FINANCEIRONOME*/ '".(empty($dados['FINANCEIRONOME'])?'':mb_strtoupper(trim($dados['FINANCEIRONOME']),'UTF-8'))."', 
			/*FINANCEIROEMAIL*/ '".(empty($dados['FINANCEIROEMAIL'])?'':mb_strtoupper(trim($dados['FINANCEIROEMAIL']),'UTF-8'))."', 
			/*TELCELULARFINANCEIRO*/ '".(empty($dados['TELCELULARFINANCEIRO'])?'':mb_strtoupper(trim($dados['TELCELULARFINANCEIRO']),'UTF-8'))."', 
			/*TELFINANCEIRO*/ '".(empty($dados['TELFINANCEIRO'])?'':mb_strtoupper(trim($dados['TELFINANCEIRO']),'UTF-8'))."', 
			/*FINANCEIROOBS*/ '".(empty($dados['FINANCEIROOBS'])?'':mb_strtoupper(trim($dados['FINANCEIROOBS']),'UTF-8'))."', 
			/*COMPRADOR1NOME*/ '".(empty($dados['COMPRADOR1NOME'])?'':mb_strtoupper(trim($dados['COMPRADOR1NOME']),'UTF-8'))."', 
			/*COMPRADOR1CPF*/ '".(empty($dados['COMPRADOR1CPF'])?'':mb_strtoupper(trim($dados['COMPRADOR1CPF']),'UTF-8'))."', 
			/*COMPRADOR1CELULAR*/ '".(empty($dados['COMPRADOR1CELULAR'])?'':mb_strtoupper(trim($dados['COMPRADOR1CELULAR']),'UTF-8'))."', 
			/*COMPRADOR2NOME*/ '".(empty($dados['COMPRADOR2NOME'])?'':mb_strtoupper(trim($dados['COMPRADOR2NOME']),'UTF-8'))."', 
			/*COMPRADOR2CPF*/ '".(empty($dados['COMPRADOR2CPF'])?'':mb_strtoupper(trim($dados['COMPRADOR2CPF']),'UTF-8'))."', 
			/*COMPRADOR2CELULAR*/ '".(empty($dados['COMPRADOR2CELULAR'])?'':mb_strtoupper(trim($dados['COMPRADOR2CELULAR']),'UTF-8'))."', 
			/*COMPRADOR3NOME*/ '".(empty($dados['COMPRADOR3NOME'])?'':mb_strtoupper(trim($dados['COMPRADOR3NOME']),'UTF-8'))."', 
			/*COMPRADOR3CPF*/ '".(empty($dados['COMPRADOR3CPF'])?'':mb_strtoupper(trim($dados['COMPRADOR3CPF']),'UTF-8'))."', 
			/*COMPRADOR3CELULAR*/ '".(empty($dados['COMPRADOR3CELULAR'])?'':mb_strtoupper(trim($dados['COMPRADOR3CELULAR']),'UTF-8'))."', 
			/*COMPENDERECO1*/ '".(empty($dados['COMPENDERECO1'])?'':mb_strtoupper(trim($dados['COMPENDERECO1']),'UTF-8'))."'
		)";
	}
	// varDump2($sql);
	// die();
	return executarOracle($sql);
}

function atualizarFicha($dados){
	if ($dados['TIPOFJ'] == "J") {
		$sql = "UPDATE CLIFICHACADASTRAL SET
			DATA = SYSDATE, 
			TIPOFJ = '".trim($dados['TIPOFJ'])."',
			CNPJ = '".trim($dados['CNPJ'])."', 
			RAZAOSOCIAL = '".(empty($dados['RAZAOSOCIAL'])?'':mb_strtoupper(trim($dados['RAZAOSOCIAL']),'UTF-8'))."', 
			INSCESTADUAL = '".trim($dados['INSCESTADUAL'])."', 
			ENDERECO = '".(empty($dados['ENDERECO'])?'':mb_strtoupper(trim($dados['ENDERECO']),'UTF-8'))."', 
			NUMERO = '".(empty($dados['NUMERO'])?'':mb_strtoupper(trim($dados['NUMERO']),'UTF-8'))."', 
			COMPLEMENTO = '".(empty($dados['COMPLEMENTO'])?'':mb_strtoupper(trim($dados['COMPLEMENTO']),'UTF-8'))."', 
			CEP = '".(empty($dados['CEP'])?'':mb_strtoupper(trim($dados['CEP']),'UTF-8'))."', 
			BAIRRO = '".(empty($dados['BAIRRO'])?'':mb_strtoupper(trim($dados['BAIRRO']),'UTF-8'))."', 
			UF = '".(empty($dados['UF'])?'':mb_strtoupper(trim($dados['UF']),'UTF-8'))."', 
			MUNICIPIO = '".(empty($dados['MUNICIPIO'])?'':mb_strtoupper(trim($dados['MUNICIPIO']),'UTF-8'))."', 
			CONTATONOME = '".(empty($dados['CONTATONOME'])?'':mb_strtoupper(trim($dados['CONTATONOME']),'UTF-8'))."', 
			EMAIL = '".(empty($dados['EMAIL'])?'':mb_strtolower(trim($dados['EMAIL']),'UTF-8'))."', 
			TELCELULAR = '".(empty($dados['TELCELULAR'])?'':mb_strtoupper(trim($dados['TELCELULAR']),'UTF-8'))."', 
			TELFIXO = '".(empty($dados['TELFIXO'])?'':mb_strtoupper(trim($dados['TELFIXO']),'UTF-8'))."', 
			OBSERVACOES = '".(empty($dados['OBSERVACOES'])?'':mb_strtoupper(trim($dados['OBSERVACOES']),'UTF-8'))."', 
			FINANCEIRONOME = '".(empty($dados['FINANCEIRONOME'])?'':mb_strtoupper(trim($dados['FINANCEIRONOME']),'UTF-8'))."', 
			FINANCEIROEMAIL = '".(empty($dados['FINANCEIROEMAIL'])?'':mb_strtoupper(trim($dados['FINANCEIROEMAIL']),'UTF-8'))."', 
			TELCELULARFINANCEIRO = '".(empty($dados['TELCELULARFINANCEIRO'])?'':mb_strtoupper(trim($dados['TELCELULARFINANCEIRO']),'UTF-8'))."', 
			TELFINANCEIRO = '".(empty($dados['TELFINANCEIRO'])?'':mb_strtoupper(trim($dados['TELFINANCEIRO']),'UTF-8'))."', 
			FINANCEIROOBS = '".(empty($dados['FINANCEIROOBS'])?'':mb_strtoupper(trim($dados['FINANCEIROOBS']),'UTF-8'))."', 
			COMPRADOR1NOME = '".(empty($dados['COMPRADOR1NOME'])?'':mb_strtoupper(trim($dados['COMPRADOR1NOME']),'UTF-8'))."', 
			COMPRADOR1CPF = '".(empty($dados['COMPRADOR1CPF'])?'':mb_strtoupper(trim($dados['COMPRADOR1CPF']),'UTF-8'))."', 
			COMPRADOR1CELULAR = '".(empty($dados['COMPRADOR1CELULAR'])?'':mb_strtoupper(trim($dados['COMPRADOR1CELULAR']),'UTF-8'))."', 
			COMPRADOR2NOME = '".(empty($dados['COMPRADOR2NOME'])?'':mb_strtoupper(trim($dados['COMPRADOR2NOME']),'UTF-8'))."', 
			COMPRADOR2CPF = '".(empty($dados['COMPRADOR2CPF'])?'':mb_strtoupper(trim($dados['COMPRADOR2CPF']),'UTF-8'))."', 
			COMPRADOR2CELULAR = '".(empty($dados['COMPRADOR2CELULAR'])?'':mb_strtoupper(trim($dados['COMPRADOR2CELULAR']),'UTF-8'))."', 
			COMPRADOR3NOME = '".(empty($dados['COMPRADOR3NOME'])?'':mb_strtoupper(trim($dados['COMPRADOR3NOME']),'UTF-8'))."', 
			COMPRADOR3CPF = '".(empty($dados['COMPRADOR3CPF'])?'':mb_strtoupper(trim($dados['COMPRADOR3CPF']),'UTF-8'))."', 
			COMPRADOR3CELULAR = '".(empty($dados['COMPRADOR3CELULAR'])?'':mb_strtoupper(trim($dados['COMPRADOR3CELULAR']),'UTF-8'))."', 
			COMPENDERECO1 = '".(empty($dados['COMPENDERECO1'])?'':mb_strtoupper(trim($dados['COMPENDERECO1']),'UTF-8'))."'
			WHERE IDFICHACADASTRAL = {$dados['IDFICHACADASTRAL']}";
	} else {
		$sql = "UPDATE CLIFICHACADASTRAL SET
			DATA = SYSDATE, 
			TIPOFJ = '".trim($dados['TIPOFJ'])."',
			CPF = '".trim($dados['CPF'])."', 
			RG = '".trim($dados['RG'])."', 
			NOME = '".(empty($dados['NOME'])?'':mb_strtoupper(trim($dados['NOME']),'UTF-8'))."', 
			PROFISSAO = '".(empty($dados['PROFISSAO'])?'':mb_strtoupper(trim($dados['PROFISSAO']),'UTF-8'))."', 
			ENDERECO = '".(empty($dados['ENDERECO'])?'':mb_strtoupper(trim($dados['ENDERECO']),'UTF-8'))."', 
			NUMERO = '".(empty($dados['NUMERO'])?'':mb_strtoupper(trim($dados['NUMERO']),'UTF-8'))."', 
			COMPLEMENTO = '".(empty($dados['COMPLEMENTO'])?'':mb_strtoupper(trim($dados['COMPLEMENTO']),'UTF-8'))."', 
			CEP = '".(empty($dados['CEP'])?'':mb_strtoupper(trim($dados['CEP']),'UTF-8'))."', 
			BAIRRO = '".(empty($dados['BAIRRO'])?'':mb_strtoupper(trim($dados['BAIRRO']),'UTF-8'))."', 
			UF = '".(empty($dados['UF'])?'':mb_strtoupper(trim($dados['UF']),'UTF-8'))."', 
			MUNICIPIO = '".(empty($dados['MUNICIPIO'])?'':mb_strtoupper(trim($dados['MUNICIPIO']),'UTF-8'))."', 
			CONTATONOME = '".(empty($dados['CONTATONOME'])?'':mb_strtoupper(trim($dados['CONTATONOME']),'UTF-8'))."', 
			EMAIL = '".(empty($dados['EMAIL'])?'':mb_strtolower(trim($dados['EMAIL']),'UTF-8'))."', 
			TELCELULAR = '".(empty($dados['TELCELULAR'])?'':mb_strtoupper(trim($dados['TELCELULAR']),'UTF-8'))."', 
			TELFIXO = '".(empty($dados['TELFIXO'])?'':mb_strtoupper(trim($dados['TELFIXO']),'UTF-8'))."', 
			OBSERVACOES = '".(empty($dados['OBSERVACOES'])?'':mb_strtoupper(trim($dados['OBSERVACOES']),'UTF-8'))."', 
			FINANCEIRONOME = '".(empty($dados['FINANCEIRONOME'])?'':mb_strtoupper(trim($dados['FINANCEIRONOME']),'UTF-8'))."', 
			FINANCEIROEMAIL = '".(empty($dados['FINANCEIROEMAIL'])?'':mb_strtoupper(trim($dados['FINANCEIROEMAIL']),'UTF-8'))."', 
			TELCELULARFINANCEIRO = '".(empty($dados['TELCELULARFINANCEIRO'])?'':mb_strtoupper(trim($dados['TELCELULARFINANCEIRO']),'UTF-8'))."', 
			TELFINANCEIRO = '".(empty($dados['TELFINANCEIRO'])?'':mb_strtoupper(trim($dados['TELFINANCEIRO']),'UTF-8'))."', 
			FINANCEIROOBS = '".(empty($dados['FINANCEIROOBS'])?'':mb_strtoupper(trim($dados['FINANCEIROOBS']),'UTF-8'))."', 
			COMPRADOR1NOME = '".(empty($dados['COMPRADOR1NOME'])?'':mb_strtoupper(trim($dados['COMPRADOR1NOME']),'UTF-8'))."', 
			COMPRADOR1CPF = '".(empty($dados['COMPRADOR1CPF'])?'':mb_strtoupper(trim($dados['COMPRADOR1CPF']),'UTF-8'))."', 
			COMPRADOR1CELULAR = '".(empty($dados['COMPRADOR1CELULAR'])?'':mb_strtoupper(trim($dados['COMPRADOR1CELULAR']),'UTF-8'))."', 
			COMPRADOR2NOME = '".(empty($dados['COMPRADOR2NOME'])?'':mb_strtoupper(trim($dados['COMPRADOR2NOME']),'UTF-8'))."', 
			COMPRADOR2CPF = '".(empty($dados['COMPRADOR2CPF'])?'':mb_strtoupper(trim($dados['COMPRADOR2CPF']),'UTF-8'))."', 
			COMPRADOR2CELULAR = '".(empty($dados['COMPRADOR2CELULAR'])?'':mb_strtoupper(trim($dados['COMPRADOR2CELULAR']),'UTF-8'))."', 
			COMPRADOR3NOME = '".(empty($dados['COMPRADOR3NOME'])?'':mb_strtoupper(trim($dados['COMPRADOR3NOME']),'UTF-8'))."', 
			COMPRADOR3CPF = '".(empty($dados['COMPRADOR3CPF'])?'':mb_strtoupper(trim($dados['COMPRADOR3CPF']),'UTF-8'))."', 
			COMPRADOR3CELULAR = '".(empty($dados['COMPRADOR3CELULAR'])?'':mb_strtoupper(trim($dados['COMPRADOR3CELULAR']),'UTF-8'))."', 
			COMPENDERECO1 = '".(empty($dados['COMPENDERECO1'])?'':mb_strtoupper(trim($dados['COMPENDERECO1']),'UTF-8'))."'
			WHERE IDFICHACADASTRAL = {$dados['IDFICHACADASTRAL']}";
	}
	// varDump2($sql);
	// die();
	return executarOracle($sql);
}



function deletarFicha($dados){
	// varDump2($dados);
	$sql = "DELETE FROM  CLIFICHACADASTRAL ".PHP_EOL;
	if (!empty($dados['CNPJ'])) {
		$sql .= "WHERE CNPJ = '".mb_strtoupper(trim($dados['CNPJ']), 'UTF-8')."'";
	} else {
		$sql .= "WHERE CPF = '".mb_strtoupper(trim($dados['CPF']), 'UTF-8')."'";
	}
	// varDump2($sql);
	return executarOracle($sql);
}


function buscaDadosFicha($dados){
	$sql = "SELECT IDFICHACADASTRAL FROM CLIFICHACADASTRAL WHERE 1=1  AND TIPOFJ = '".$dados['TIPOFJ']."'";
	if ($dados['TIPOFJ'] == "J") {
		$sql .= " AND CNPJ = '".limpaCGCENT($dados['CGCENT'])."'";
	} else {
		$sql .= " AND CPF = '".limpaCGCENT($dados['CGCENT'])."'";
	}
	// varDump2($dados); 
	// varDump2($sql); 
	if ($ret = selectOracle($sql)) {
		// varDump2($ret);
		$ret = reset($ret);
		atualizaDataFicha($ret['IDFICHACADASTRAL']);
		return $ret['IDFICHACADASTRAL'];
	} else {
		return false;
	}
}

function buscaClienteCGC($CGCENT){
	$sql = "SELECT CLIENTE, RAZAOSOCIAL, CGCENT FROM PCCLIENT WHERE CGCENT = ".$CGCENT;
	$ret = selectOracle($sql);
	// varDump2($sql); 
	// varDump2($ret);
	if ($ret == false) {
		return false;
	} else {
		return reset($ret);
	}
}

function buscaFichas(){
	$sql = "SELECT * FROM CLIFICHACADASTRAL ORDER BY DATA DESC";
	return selectOracle($sql);
}

function atualizaDataFicha($IDFICHACADASTRAL){
	if (!empty($IDFICHACADASTRAL)) {
		$sql = "UPDATE CLIFICHACADASTRAL SET DATA = SYSDATE WHERE IDFICHACADASTRAL = ".$IDFICHACADASTRAL;
		executarOracle($sql);
	}
}



function validaDadosPessoais($dados){
	// varDump2($dados);
	$erros = 0;
	if ($dados['RG'] == "") 					$erros++;
	if ($dados['NOME'] == "") 				$erros++;
	if ($dados['ENDERECO'] == "") 		$erros++;
	if ($dados['NUMERO'] == "") 			$erros++;
	if ($dados['BAIRRO'] == "") 			$erros++;
	if ($dados['CEP'] == "") 					$erros++;
	if ($dados['MUNICIPIO'] == "") 		$erros++;
	if ($dados['UF'] == "") 					$erros++;
	if ($erros == 0) {
		$_SESSION['FICHA']['VALIDA_DADOSPESSOAIS'] = true;
		echo '<span class="badge rounded-pill bg-success float-md-end"><i class="fa-solid fa-check"></i></span>';
	} else {
		$_SESSION['FICHA']['VALIDA_DADOSPESSOAIS'] = false;
		echo '<span class="badge rounded-pill bg-danger float-md-end"><i class="fa-solid fa-xmark"></i></span>';
	}
}

function atualizarContatos($dados){
	$sql = "UPDATE CLIFICHACADASTRAL SET

					WHERE IDFICHACADASTRAL = ".$dados['IDFICHACADASTRAL'];
	// varDump2($dados);
	// varDump2($sql);
	return executarOracle($sql);
}

function validaContatos($dados){
	// varDump2($dados);
	$erros = 0;
	if ($dados['EMAIL'] == "") 					$erros++;
	if ($dados['TELCELULAR'] == "") 		$erros++;
	if ($dados['OBSERVACOES'] == "") 		$erros++;
	if ($erros == 0) {
		$_SESSION['FICHA']['VALIDA_CONTATOS'] = true;
		echo '<span class="badge rounded-pill bg-success float-md-end"><i class="fa-solid fa-check"></i></span>';
	} else {
		$_SESSION['FICHA']['VALIDA_CONTATOS'] = false;
		echo '<span class="badge rounded-pill bg-danger float-md-end"><i class="fa-solid fa-xmark"></i></span>';
	}
}

function validaCompradores($dados){
	// varDump2($dados);
	$erros = 0;
	if ($dados['COMPRADOR1NOME'] == "") 			$erros++;
	if ($dados['COMPRADOR1CPF'] == "") 				$erros++;
	if ($dados['COMPRADOR1CELULAR'] == "") 		$erros++;
	if ($erros == 0) {
		$_SESSION['FICHA']['VALIDA_COMPRADORES'] = true;
		echo '<span class="badge rounded-pill bg-success float-md-end"><i class="fa-solid fa-check"></i></span>';
	} else {
		$_SESSION['FICHA']['VALIDA_COMPRADORES'] = false;
		echo '<span class="badge rounded-pill bg-danger float-md-end"><i class="fa-solid fa-xmark"></i></span>';
	}
}

function atualizarComprovantes($dados){
	if (isset($_FILES)) {
		// varDump2($_FILES);
		foreach ($_FILES as $key => $arquivo) {
			switch ($key) {
				case 'COMPDOCUMENTO1':
					$dados['COMPDOCUMENTO1'] = $arquivo['name'];
					break;
				case 'COMPDOCUMENTO2':
					$dados['COMPDOCUMENTO2'] = $arquivo['name'];
					break;
				case 'COMPENDERECO1':
					$dados['COMPENDERECO1'] = $arquivo['name'];
					break;
				case 'COMPENDERECO2':
					$dados['COMPENDERECO2'] = $arquivo['name'];
					break;
			}

			if ($arquivo["error"] == 0) {
		    $dir = @DIR_UPLOAD;
		    if (!is_dir($dir)) {
		        die('ERRO Não encontrado Diretório '.$dir);
		    } else {
		        if($debug) varDump2($dir . " pasta encontrada com sucesso");
		    }

		    $tmp_name     = $arquivo["tmp_name"];
		    $name        	= mb_strtoupper($arquivo["name"],'UTF-8');
		    $filename    	= $dir . $name;
		    // varDump2($tmp_name);
		    // varDump2($filename);
		    $move					= move_uploaded_file($tmp_name, $filename);
		    if($move){
		    	if (file_exists($filename)) {
		    		exibeMensagem('SUCESSO - Arquivo '.$filename.' localizado no sevidor');
		    	} else {
		    		exibeMensagem('ERRO - Arquivo '.$filename.' não localizado no sevidor');
		    	}
		    } else {
	        exibeMensagem('ERRO ao importar o Arquivo '.$filename);
		    }
		    // die();
		  }
		}
		$sql = "UPDATE CLIFICHACADASTRAL SET
							COMPDOCUMENTO1 = '".formataStringArquivo($dados['COMPDOCUMENTO1'])."',
							COMPENDERECO1 = '".formataStringArquivo($dados['COMPENDERECO1'])."',
							COMPDOCUMENTO2 = '".formataStringArquivo($dados['COMPDOCUMENTO2'])."',
							COMPENDERECO2 = '".formataStringArquivo($dados['COMPENDERECO2'])."'
						WHERE IDFICHACADASTRAL = ".$dados['IDFICHACADASTRAL'];
		// varDump2($dados);
		// varDump2($sql);
		return executarOracle($sql);
	} else {
		return false;
	}
}

function validaComprovantes($dados){
	// varDump2($dados);
	$erros = 0;
	if ($dados['COMPDOCUMENTO1'] == "") 			$erros++;
	if ($dados['COMPENDERECO1'] == "") 				$erros++;
	if ($erros == 0) {
		$_SESSION['FICHA']['VALIDA_COMPROVANTES'] = true;
		echo '<span class="badge rounded-pill bg-success float-md-end"><i class="fa-solid fa-check"></i></span>';
	} else {
		$_SESSION['FICHA']['VALIDA_COMPROVANTES'] = false;
		echo '<span class="badge rounded-pill bg-danger float-md-end"><i class="fa-solid fa-xmark"></i></span>';
	}
}

function validaFicha($dados){
	$infodados = false;
	$ficha = false;
	if ($_SESSION['FICHA']['TIPOFJ'] == 'J') {
		validaDadosEmpresariais($dados);
		if ($_SESSION['FICHA']['VALIDA_DADOSEMPRESARIAIS']) {
			$infodados = true;
		}
	} else {
		validaDadosPessoais($dados);
		if ($_SESSION['FICHA']['VALIDA_DADOSPESSOAIS']) {
			$infodados = true;
		}
	}

	validaContatos($dados);
	validaCompradores($dados);
	validaComprovantes($dados);
	
	if ($infodados) {
		if ($_SESSION['FICHA']['VALIDA_CONTATOS']) {
			if ($_SESSION['FICHA']['VALIDA_CONTATOS']) {
				if ($_SESSION['FICHA']['VALIDA_CONTATOS']) {
					$ficha = true;
				}
			}
		}
	}

	return $ficha;

}


function enviarFichaEmail($dados){
	$ficha = buscaDadosFichaID($dados['IDFICHACADASTRAL']);
	if ($ficha['TIPOFJ'] == "F") {
		$ficha['TIPO'] 		= "FICHAPF";
		$ficha['assunto']   = converterUTF8("NOVA FICHA CADASTRAL PESSOA FÍSICA [ ".$ficha['NOME']." ]");
	} else {
		$ficha['TIPO'] 		= "FICHAPJ";
		$ficha['assunto']   = converterUTF8("NOVA FICHA CADASTRAL PESSOA JURÍDICA [ ".$ficha['RAZAOSOCIAL']." ]");
	}

	$ficha['destinatarios'][0]['email'] = mb_strtolower('celeste@vemap.com.br', 'UTF-8');
	$ficha['destinatarios'][0]['nome']  = mb_strtoupper('Celeste', 'UTF-8');
	$ficha['destinatarios'][1]['email'] = mb_strtolower('nilce@vemap.com.br', 'UTF-8');
	$ficha['destinatarios'][1]['nome']  = mb_strtoupper('Nilce', 'UTF-8');
	$ficha['destinatarios'][2]['email'] = mb_strtolower('emilianosfilho@gmail.com', 'UTF-8');
	$ficha['destinatarios'][2]['nome']  = mb_strtoupper('Emiliano Filho', 'UTF-8');
	
	$ficha['ANEXOS'] = array();
	$dirUpload = '/var/www/html/portal/upload/';
	if ($ficha['TIPOFJ'] == "F") {
		$anexo = $dirUpload.somenteNumeros($ficha['CPF']).'_FICHACADASTRAL'.'.pdf';
	} else {
		$anexo = $dirUpload.somenteNumeros($ficha['CNPJ']).'_FICHACADASTRAL'.'.pdf';
	}
  	if (file_exists($anexo)) {
	    array_push($ficha['ANEXOS'], $anexo);
  	}

    if (!empty($ficha['COMPENDERECO1'])) {
		$anexo = explode(".", $ficha['COMPENDERECO1']);
		$anexo = @DIR_UPLOAD . reset($filename).".".strtolower(end($filename));
		if (file_exists($anexo)) {
			array_push($ficha['ANEXOS'], $anexo);
		}
    }
    if (!empty($ficha['COMPENDERECO2'])) {
		$anexo = explode(".", $ficha['COMPENDERECO2']);
		$anexo = @DIR_UPLOAD . reset($filename).".".strtolower(end($filename));
		if (file_exists($anexo)) {
			array_push($ficha['ANEXOS'], $anexo);
		}
    }
    if (!empty($ficha['COMPDOCUMENTO1'])) {
		$anexo = explode(".", $ficha['COMPDOCUMENTO1']);
		$anexo = @DIR_UPLOAD . reset($filename).".".strtolower(end($filename));
		if (file_exists($anexo)) {
			array_push($ficha['ANEXOS'], $anexo);
		}
    }
    if (!empty($ficha['COMPDOCUMENTO2'])) {
		$anexo = explode(".", $ficha['COMPDOCUMENTO2']);
		$anexo = @DIR_UPLOAD . reset($filename).".".strtolower(end($filename));
		if (file_exists($anexo)) {
			array_push($ficha['ANEXOS'], $anexo);
		}
    }

    $conteudo = '/var/www/html/portal/pages/financeiro/contentMail.html';
    if (file_exists($conteudo)) {
    	$ficha['contentMail'] = file_get_contents($conteudo);
    } else {
    	exibeMensagem("Erquivo contentMail não localizado.");
    }

	// varDump2($ficha);
	// die();
	include_once("plugins/PHPMailer/function.php");
	enviarEmail($ficha);
}

function buscaClientesAliquota(){
	$sql = "SELECT * FROM ORCALIQUOTARR";
	return selectOracle($sql);
}

function buscaClienteAliquotaRR(){
	$sql = "SELECT C.CODCLI,
			       C.CLIENTE,
			       C.MUNICENT || ' - ' || C.ESTENT AS MUNICIPIO,
			       R.DTCADASTRO,
			       A.NOME AS USUARIOCADASTRO,
			       R.DTEXCLUSAO,
			       E.NOME AS USUARIOEXCLUSAO
			  FROM ORCALIQUOTARR R, PCCLIENT C, ORCUSUARIO A, ORCUSUARIO E
			 WHERE R.CODCLI = C.CODCLI
			   AND R.IDUSUARIOCADASTRO = A.IDUSUARIO(+)
			   AND R.IDUSUARIOEXCLUSAO = E.IDUSUARIO(+)";
	return selectOracle($sql);
}

function AdicionarClienteAliquotaRR($dados){
	$sql = "INSERT INTO ORCALIQUOTARR (CODCLI, IDUSUARIOCADASTRO) VALUES (".$dados["CODCLI"].", ".$_SESSION['login']["IDUSUARIO"].")";
	if (executarOracle($sql)) {
		insereMOdal("success", "Cliente ".$dados["CODCLI"]." adicionado na lista de clistes com aliquota especial RR.");
	} else {
		insereMOdal("danger", "ERRO ao adicionar o Cliente ".$dados["CODCLI"]." à lista de clistes com aliquota especial RR.");
	}
}