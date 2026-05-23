<?php
function buscaTiposChamado() {
    $sql = "SELECT ID_TIPO, TIPO, DESCRICAO 
            FROM VMP_TIPOS 
            WHERE DATA_EXCLUSAO IS NULL 
            ORDER BY ID_TIPO ASC";
    return selectOracle($sql);
}

function addTipo($dados) {
    // Sanitização e formatação procedural
    $tipo = mb_strtoupper(trim($dados['TIPO'] ?? ''), 'UTF-8');
    $desc = mb_strtoupper(trim($dados['DESCRICAO'] ?? ''), 'UTF-8');

    $sql = "INSERT INTO VMP_TIPOS (TIPO, DESCRICAO) VALUES ('$tipo', '$desc')";
    
    return executarOracle($sql) 
        ? insereModal("success", "Novo tipo adicionado!") 
        : insereModal("danger", "Erro ao adicionar tipo!");
}

function buscaVmpCategorias(){
	$sql = "SELECT * FROM VMP_CATEGORIAS WHERE DATA_EXCLUSAO IS NULL ORDER BY ID_CATEGORIA ASC";
	return selectOracle($sql);
}

/**
 * Busca usuários ativos, com filtro opcional por ID.
 */
function buscaUsuariosChamado(?int $idUsuario = null) {
    // Inicializa o filtro vazio
    $filtro = "";

    // Valida se foi passado um ID válido e maior que zero
    if (!empty($idUsuario)) {
        $idLimpo = (int) $idUsuario; // Força casting para segurança
        $filtro = " AND IDUSUARIO = $idLimpo";
    }
    
    // Query com alias 'U' para melhor legibilidade
    $sql = "SELECT U.IDUSUARIO, SUBSTR(U.NOME, 1, 20) NOME
            FROM ORCUSUARIO U 
            WHERE U.DTEXCLUSAO IS NULL 
            $filtro 
            ORDER BY U.NOME ASC";
            
    return selectOracle($sql);
}

function addCategoria($dados){
	$sql = "INSERT INTO VMP_CATEGORIAS (
				CATEGORIA,
				DESCRICAO
			) VALUES (
				/*CATEGORIA*/'".mb_strtoupper(trim($dados['CATEGORIA']), 'UTF-8')."',
				/*DESCRICAO*/'".mb_strtoupper(trim($dados['DESCRICAO']), 'UTF-8')."'
			)";
	if (executarOracle($sql)){
		insereModal("success", " Nova categoria adicionado com sucesso!");
	} else {
		insereModal("danger", "ERRO ao adicionar a nova categoria!");
	}
} 

function buscaChamadosPendentes(){
	$sql = "SELECT CH.ID_CHAMADO,
			       TO_CHAR(CH.DATA_ABERTURA,'DD-MM-YYYY HH24:MI:SS') AS DATA_ABERTURA,
			       TO_CHAR(CH.DATA_ALTERACAO,'DD-MM-YYYY HH24:MI:SS') AS DATA_ALTERACAO,
			       TO_CHAR(CH.DATA_INICIO,'DD-MM-YYYY HH24:MI:SS') AS DATA_INICIO,
			       TO_CHAR(CH.DATA_PAUSA,'DD-MM-YYYY HH24:MI:SS') AS DATA_PAUSA,
			       TO_CHAR(CH.DATA_FIM,'DD-MM-YYYY HH24:MI:SS') AS DATA_FIM,
			       TO_CHAR(CH.DATA_PREVISAO,'DD-MM-YYYY') AS DATA_PREVISAO,
			       CH.ID_TIPO,
			       CH.ID_CATEGORIA,
			       CH.ID_USUARIO,
			       CH.ID_TECNICO,
			       CH.TITULO,
			       CA.CATEGORIA,
			       TI.TIPO,
			       US.NOME           AS USUARIO,
			       TE.NOME           AS TECNICO,
 	               CASE 
			         WHEN CH.DATA_FIM IS NOT NULL THEN 'FINALIZADO'
			         WHEN CH.DATA_PAUSA IS NOT NULL THEN 'PAUSADO'
			         WHEN CH.DATA_INICIO IS NOT NULL THEN 'EM ATENDIMENTO'
			           ELSE 'AGUARDANDO' END AS STATUS
			  FROM VMP_CHAMADOS   CH,
			       VMP_CATEGORIAS CA,
			       VMP_TIPOS      TI,
			       ORCUSUARIO     US,
			       ORCUSUARIO     TE
			 WHERE CH.ID_CATEGORIA = CA.ID_CATEGORIA
			   AND CH.ID_TIPO = TI.ID_TIPO
			   AND CH.ID_USUARIO = US.IDUSUARIO
			   AND CH.ID_TECNICO = TE.IDUSUARIO(+)
			   AND CH.DATA_FIM IS NULL";
	if (array_search('ADM_CHAMADOS', $_SESSION['login']['PERMISSOES']) == false) {
		$sql .= PHP_EOL."  AND CH.ID_USUARIO = ".$_SESSION['login']['IDUSUARIO'];
	}
		$sql .= PHP_EOL." ORDER BY CASE 
			         WHEN CH.DATA_FIM IS NOT NULL THEN 'FINALIZADO'
			         WHEN CH.DATA_PAUSA IS NOT NULL THEN 'PAUSADO'
			         WHEN CH.DATA_INICIO IS NOT NULL THEN 'EM ATENDIMENTO'
			           ELSE 'AGUARDANDO' END DESC, CH.DATA_ABERTURA ASC";
	return selectOracle($sql);
} 


function buscaChamadosFinalizados(){
	$sql = "SELECT CH.ID_CHAMADO,
			       TO_CHAR(CH.DATA_ABERTURA,'DD-MM-YYYY HH24:MI:SS') AS DATA_ABERTURA,
			       CH.DATA_ALTERACAO,
			       CH.DATA_INICIO,
			       CH.DATA_PAUSA,
			       TO_CHAR(CH.DATA_FIM,'DD-MM-YYYY HH24:MI:SS') AS DATA_FIM,
			       CH.ID_TIPO,
			       CH.ID_CATEGORIA,
			       CH.ID_USUARIO,
			       CH.ID_TECNICO,
			       CH.TITULO,
			       CA.CATEGORIA,
			       TI.TIPO,
			       US.NOME           AS USUARIO,
			       TE.NOME           AS TECNICO,
 	               CASE 
			         WHEN CH.DATA_FIM IS NOT NULL THEN 'FINALIZADO'
			         WHEN CH.DATA_PAUSA IS NOT NULL THEN 'PAUSADO'
			         WHEN CH.DATA_INICIO IS NOT NULL THEN 'EM ATENDIMENTO'
			           ELSE 'AGUARDANDO' END AS STATUS
			  FROM VMP_CHAMADOS   CH,
			       VMP_CATEGORIAS CA,
			       VMP_TIPOS      TI,
			       ORCUSUARIO     US,
			       ORCUSUARIO     TE
			 WHERE CH.ID_CATEGORIA = CA.ID_CATEGORIA
			   AND CH.ID_TIPO = TI.ID_TIPO
			   AND CH.ID_USUARIO = US.IDUSUARIO
			   AND CH.ID_TECNICO = TE.IDUSUARIO(+)
			   AND CH.DATA_FIM IS NOT NULL";
	if ($_SESSION['login']['PERFIL'] !== "ADMINISTRADOR") {
		$sql .= PHP_EOL."  AND CH.ID_USUARIO = ".$_SESSION['login']['IDUSUARIO'];
	}
		$sql .= PHP_EOL." ORDER BY CH.DATA_FIM DESC";
	return selectOracle($sql);
} 

function buscaDadosChamadoID($ID_CHAMADO){

	$sql = "SELECT CH.ID_CHAMADO,
			       TO_CHAR(CH.DATA_ABERTURA,'DD-MM-YYYY HH24:MI:SS') AS DATA_ABERTURA,
			       TO_CHAR(CH.DATA_ALTERACAO,'DD-MM-YYYY HH24:MI:SS') AS DATA_ALTERACAO,
			       TO_CHAR(CH.DATA_INICIO,'DD-MM-YYYY HH24:MI:SS') AS DATA_INICIO,
			       TO_CHAR(CH.DATA_PAUSA,'DD-MM-YYYY HH24:MI:SS') AS DATA_PAUSA,
			       TO_CHAR(CH.DATA_FIM,'DD-MM-YYYY HH24:MI:SS') AS DATA_FIM,
			       TO_CHAR(CH.DATA_PREVISAO,'DD-MM-YYYY') AS DATA_PREVISAO,
			       CH.ID_TIPO,
			       CH.ID_CATEGORIA,
			       CH.ID_USUARIO,
			       CH.ID_TECNICO,
			       CH.TITULO,
			       CA.CATEGORIA,
			       TI.TIPO,
			       US.NOME           AS USUARIO,
			       TE.NOME           AS TECNICO,
 	               CASE 
			         WHEN CH.DATA_FIM IS NOT NULL THEN 'FINALIZADO'
			         WHEN CH.DATA_PAUSA IS NOT NULL THEN 'PAUSADO'
			         WHEN CH.DATA_INICIO IS NOT NULL THEN 'EM ATENDIMENTO'
			           ELSE 'AGUARDANDO' END AS STATUS
			  FROM VMP_CHAMADOS   CH,
			       VMP_CATEGORIAS CA,
			       VMP_TIPOS      TI,
			       ORCUSUARIO     US,
			       ORCUSUARIO     TE
			 WHERE CH.ID_CATEGORIA = CA.ID_CATEGORIA
			   AND CH.ID_TIPO = TI.ID_TIPO
			   AND CH.ID_USUARIO = US.IDUSUARIO
			   AND CH.ID_TECNICO = TE.IDUSUARIO(+)
			   AND CH.ID_CHAMADO = {$ID_CHAMADO}";
	return reset(selectOracle($sql));
} 

function buscaHistoricoChamado($ID_CHAMADO){
	if (!$ID_CHAMADO || empty($ID_CHAMADO)) {
		throw new Exception("Error: ID_CHAMADO chamado inválido ao executar a função: buscaHistoricoChamado");
	}

	$sql = "SELECT 
			    TO_CHAR(hi.data_atualizacao, 'DD-MM-YYYY HH24:MI:SS') AS data,
			    us.nome AS usuario,
			    us.avatar,
			    TO_CHAR(hi.tipo) as tipo,
			    TO_CHAR(hi.historico) AS historico
			FROM vmp_historico hi
			INNER JOIN orcusuario us ON hi.id_usuario = us.idusuario
			WHERE hi.id_chamado = {$ID_CHAMADO}

			UNION ALL

			SELECT 
			    TO_CHAR(an.data_cadastro, 'DD-MM-YYYY HH24:MI:SS') AS data,
			    us.nome AS usuario,
			    us.avatar,
			    'ANEXO' AS tipo,
			    TO_CHAR(an.anexo) AS historico
			FROM vmp_anexos an
			INNER JOIN orcusuario us ON an.id_usuario = us.idusuario
			WHERE an.id_chamado = {$ID_CHAMADO}
			ORDER BY 1 ASC";
	return selectOracle($sql);
} 

/**
 * Adiciona um novo chamado e gera uma notificação para o usuário.
 * * @param array $dados Dados vindos do formulário
 * @return bool
 */
function addChamado(int $idTipo, int $idCategoria, int $idUsuario, string $titulo ) {
    // 1. Sanitização e Casting (Segurança)
    $idTipo      = (int) $idTipo;
    $idCategoria = (int) $idCategoria;
    $idUsuario   = (int) $idUsuario;
    $titulo      = sanitizeOracleString($titulo);

    // 2. SQL Otimizado
    // O Oracle retornará o ID gerado se configurado, mas aqui focamos na execução procedural padrão
    $sql = "INSERT INTO VMP_CHAMADOS (
                ID_TIPO,
                ID_CATEGORIA,
                ID_USUARIO,
                TITULO,
                DATA_ABERTURA
            ) VALUES (
                $idTipo,
                $idCategoria,
                $idUsuario,
                '$titulo',
                SYSDATE
            )";

    // 3. Execução e Notificação
    // IMPORTANTE: Alterado de selectOracle para executarOracle
    if (executarOracle($sql)) {
        // Recuperamos o último ID para a notificação (considerando trigger de sequence do WinThor/Oracle)
    	$idChamado = buscaUltimoChamadoUsuario($idUsuario);

        $msg = "SEU CHAMADO NR $idChamado FOI ABERTO COM SUCESSO: $titulo";
        
        // Dispara a notificação para o usuário que abriu o chamado (ou para um gestor, se desejar)
        addHistoricoChamado($idUsuario, $idChamado, $titulo, 'HIST');
        inserirNotificacao($idUsuario, $idChamado, $msg, null, 'NOVO_CHAMADO');
        
        insereModal("success", "Chamado #$idChamado aberto com sucesso!");
        return true;
    }

    insereModal("danger", "Erro ao abrir chamado!");
    return false;
}

function buscaUltimoChamadoUsuario(int $idUsuario) {
	$sql = "SELECT MAX(ID_CHAMADO) AS LAST_ID FROM VMP_CHAMADOS WHERE ID_USUARIO = $idUsuario";
	if ($resId = selectOracle($sql) ){
		return $resId[0]['LAST_ID'] ?? 0;
	} else {
		return 0;
	}
}


function editarPrevisao($dados){
	$sql = "UPDATE VMP_CHAMADOS 
			   SET DATA_PREVISAO = TO_DATE('{$dados['DATA_PREVISAO']}', 'DD/MM/YYYY')
			 WHERE ID_CHAMADO = {$dados['ID_CHAMADO']}";
	// varDump2($sql);
	return executarOracle($sql);
}


function addHistoricoChamado(int $idUsuario, int $idChamado, string $historico, string $tipo = 'HIST'){
	$idUsuario 	= (int) $idUsuario;
	$idChamado 	= (int) $idChamado;
	$historico 	= sanitizeOracleString($historico);
	$tipo 		= sanitizeOracleString($tipo);

	$sql = "INSERT INTO VMP_HISTORICO (ID_USUARIO, ID_CHAMADO, HISTORICO, TIPO) 
			VALUES ($idUsuario, $idChamado, '$historico', '$tipo')";
	// varDump2($sql);
	// die();
	if(selectOracle($sql)){
		$sql2 = "UPDATE VMP_CHAMADOS SET DATA_ALTERACAO = SYSDATE WHERE ID_CHAMADO = $idChamado";
		// varDump2($sql);
		// die();
		if (executarOracle($sql2)){
	        // Dispara a notificação para o usuário que abriu o chamado (ou para um gestor, se desejar)
	        return inserirNotificacao($idUsuario, $idChamado, $historico, null, 'NOVO_HISTORICO');
		} else {
			return false;
		}
	} else {
		return false;
	}
}


function listarSolucoes(){
  // 1. Caminho do arquivo JSON
  $arquivo = 'pages/chamados/solucao.json';

  if (file_exists($arquivo)) {
    // 2. Ler o conteúdo do arquivo
    $jsonString = file_get_contents($arquivo);
    // varDump2($jsonString);

    // 3. Decodificar a string JSON em array associativo
    $dadosJson = json_decode($jsonString, true);
    return $dadosJson;

  } else {
    echo "Arquivo não existe! ".$arquivo;
  }
}      


function buscarSolucoesID(int $id){
   $solucoes = listarSolucoes();       
   if ($solucoes) {
   		foreach ($solucoes as $key => $value) {
   			if ((int) $value['id'] === $id) {
   				return $value['solucao'];
   			}
   		}
   }
}

function gerenciarAtendimento(array $dados, string $acao) {

    $idUsuario = (int)$dados['ID_USUARIO']; // Cast para segurança (SQL Injection)
    $idChamado = (int)$dados['ID_CHAMADO']; // Cast para segurança (SQL Injection)
    $idUsuarioLogado = $_SESSION['login']['IDUSUARIO'];
    $nomeConsultor = $_SESSION['login']['NOME'];

    $idSolucao = (int)$dados['ID_SOLUCAO']; // Cast para segurança (SQL Injection)
    $solucao = buscarSolucoesID($idSolucao);

    // 1. Mapeamento de configurações por ação para evitar IFs repetitivos
    $configuracoes = [
        'INICIAR' => [
            'texto'  => "O seu chamado #{$idChamado} já foi atribuído ao nossos técnico {$nomeConsultor} e o atendimento foi iniciado.
Estamos analisando as informações fornecidas para buscar a melhor solução.",
            'coluna' => "DATA_INICIO = SYSDATE",
            'tipo'   => "INICIO_ATENDIMENTO"
        ],
        'REINICIAR' => [
            'texto'  => "O seu chamado #{$idChamado} teve o atendimento reiniciado.
Estamos analisando as informações fornecidas para buscar a melhor solução.",
            'coluna' => "DATA_PAUSA = NULL",
            'tipo'   => "REINICIO_ATENDIMENTO"
        ],
        'PAUSAR' => [
            'texto'  => "O seu chamado #{$idChamado} teve o atendimento pausado.
Estamos analisando as informações fornecidas para buscar a melhor solução.",
            'coluna' => "DATA_PAUSA = SYSDATE",
            'tipo'   => "PAUSA_ATENDIMENTO"
        ],
        'FINALIZAR' => [
            'texto'  => "Chamado finalizado. ".$solucao,
            'coluna' => "DATA_FIM = SYSDATE",
            'tipo'   => "FIM_ATENDIMENTO"
        ]
    ];

    // Valida se a ação existe no mapa
    if (!isset($configuracoes[$acao])) {
        return false;
    }

    $conf = $configuracoes[$acao];


    // 2. Define o Histórico
    $historico = $conf['texto'];
    
    // 3. Registra Histórico (Função externa do seu sistema)
    addHistoricoChamado($idUsuarioLogado, $idChamado, $historico, 'HIST');

    // 4. Prepara e executa o SQL no Oracle utilizando OCI8 via executarOracle
    // O campo DATA_ALTERACAO é comum a todos os updates
    $sql = "UPDATE VMP_CHAMADOS 
               SET DATA_ALTERACAO = SYSDATE,
               	   id_tecnico = {$idUsuarioLogado}, 
                   {$conf['coluna']} 
             WHERE ID_CHAMADO = {$idChamado}";

    if (executarOracle($sql)) {
        // 5. Insere Notificação em caso de sucesso
        inserirNotificacao(
            $idUsuario, 
            $idChamado, 
            $historico, 
            null, 
            $conf['tipo']
        );
        return true;
    }

    return false;
}

function buscaAtividadesCabecalho($dados){
	$sql = "SELECT TO_CHAR(C.DATA_ABERTURA, 'DD/MM/YYYY HH24:MI:SS') AS DATA_ABERTURA,
			       U.NOME AS USUARIO,
			       T.NOME AS TECNICO,
			       TP.TIPO,
			       TP.DESCRICAO AS DESCRICAO_TIPO,
			       CA.CATEGORIA,
			       CA.DESCRICAO AS DESCRICAO_CATEGORIA,
			       C.ID_CHAMADO,
			       C.ID_TIPO,
			       C.ID_CATEGORIA,
			       C.ID_USUARIO,
			       C.ID_TECNICO,
			       C.TITULO,
	               CASE 
			         WHEN C.DATA_FIM IS NOT NULL THEN 'FINALIZADO'
			         WHEN C.DATA_PAUSA IS NOT NULL THEN 'PAUSADO'
			         WHEN C.DATA_INICIO IS NOT NULL THEN 'EM ATENDIMENTO'
			           ELSE 'AGUARDANDO' END AS STATUS
			  FROM VMP_CHAMADOS   C,
			       ORCUSUARIO     U,
			       ORCUSUARIO     T,
			       VMP_TIPOS      TP,
			       VMP_CATEGORIAS CA
			 WHERE C.ID_USUARIO = U.IDUSUARIO
			   AND C.ID_TECNICO = T.IDUSUARIO(+)
			   AND C.ID_TIPO = TP.ID_TIPO
			   AND C.ID_CATEGORIA = CA.ID_CATEGORIA
			   AND C.ID_CHAMADO IN (SELECT H.ID_CHAMADO
			          FROM VMP_HISTORICO H
			         WHERE TRUNC(H.DATA_ATUALIZACAO) 
					   		BETWEEN ".formataDataBRtoOracle($dados['DATAINI'])." 
					   		AND ".formataDataBRtoOracle($dados['DATAFIM']).")
			 ORDER BY C.DATA_ABERTURA ASC";
	return selectOracle($sql);
}

function buscaAtividadesRegistros($dados, $cab){
	$sql = "SELECT TO_CHAR(H.DATA_ATUALIZACAO, 'DD/MM/YYYY HH24:MI:SS') AS DATA_ATUALIZACAO,
			       H.ID_CHAMADO,
			       H.ID_USUARIO,
			       U.NOME AS USUARIO,
			       H.HISTORICO
			  FROM VMP_HISTORICO H, ORCUSUARIO U
			 WHERE H.ID_USUARIO = U.IDUSUARIO
			   AND H.ID_CHAMADO = {$cab['ID_CHAMADO']}
			   AND TRUNC(H.DATA_ATUALIZACAO) 
					   		BETWEEN ".formataDataBRtoOracle($dados['DATAINI'])." 
					   		AND ".formataDataBRtoOracle($dados['DATAFIM'])."
			 ORDER BY H.DATA_ATUALIZACAO ASC";
	// varDump2($dados);
	// varDump2($cab);
	// varDump2($sql);
	return selectOracle($sql);
}

function buscaAtividadesRegistrosPorData($data){
	$sql = "SELECT TO_CHAR(H.DATA_ATUALIZACAO, 'DD/MM/YYYY HH24:MI:SS') AS DATA_ATUALIZACAO,
			       H.ID_CHAMADO,
			       C.TITULO,
			       C.ID_USUARIO AS ID_SOLICITANTE,
			       U.NOME AS SOLICITANTE,
			       H.HISTORICO
			  FROM VMP_CHAMADOS C, VMP_HISTORICO H, ORCUSUARIO U
			 WHERE C.ID_CHAMADO = H.ID_CHAMADO
			   AND C.ID_USUARIO = U.IDUSUARIO
			   AND TRUNC(H.DATA_ATUALIZACAO) = TRUNC(".formataDataBRtoOracle($data).")
			 ORDER BY H.DATA_ATUALIZACAO ASC";
	// varDump2($data);
	// varDump2($sql);
	return selectOracle($sql);
}

############################################################################################

/**
 * Insere notificação na tabela VMP_NOTIFICACAO
 */
function inserirNotificacao(int $idDestino, int $idChamado, string $mensagem, ?int $idRemetente = null, string $tipoAcao = 'SISTEMA') {
    
    // Utiliza a sanitizeOracleString do seu functions.php para evitar quebras no SQL
    $mensagemLimpa = sanitizeOracleString($mensagem, 1000);
    $tipoAcaoLimpa = sanitizeOracleString($tipoAcao, 50);
    
    $remetenteSql = is_null($idRemetente) ? "NULL" : (int)$idRemetente;
    $idDestino    = (int)$idDestino;
    $idChamado    = (int)$idChamado;

    $sql = "INSERT INTO VMP_NOTIFICACAO 
            (IDUSUARIO_DESTINO, IDUSUARIO_REMETENTE, IDCHAMADO, MENSAGEM, TIPO_ACAO, LIDA, DATA) 
            VALUES 
            ($idDestino, $remetenteSql, $idChamado, '$mensagemLimpa', '$tipoAcaoLimpa', 'N', CURRENT_TIMESTAMP)";
    
    try {
        return executarOracle($sql); 
    } catch (Exception $e) {
        // Log de erro silencioso ou tratamento específico para não travar o fluxo principal do usuário
        error_log("Erro VMP_NOTIF: " . $e->getMessage());
        return false;
    }
}

/**
 * Busca notificações não lidas para o Polling do portal.
 */
function buscarNotificacoesNaoLidas(int $idUsuario) {
    // 1. Garantir integridade do dado de entrada para evitar SQL Injection
    $idDestino = (int) $idUsuario;

    // 2. Query otimizada com Aliases e Left Join
    // O uso de N.IDUSUARIO_DESTINO e N.LIDA primeiro na cláusula WHERE 
    // casa perfeitamente com o índice IDX_VMP_NOTIF_USER_STATUS (IDUSUARIO_DESTINO, LIDA, DATA DESC)
    $sql = "SELECT N.IDNOTIFICACAO, 
                   N.IDCHAMADO, 
                   N.MENSAGEM, 
                   N.TIPO_ACAO, 
                   N.IDUSUARIO_REMETENTE, 
                   U.NOME AS NOME_REMETENTE,
                   U.AVATAR AS AVATAR_REMETENTE,
                   TO_CHAR(N.DATA, 'DD/MM/YYYY HH24:MI') as DATA_FORMATADA 
            FROM VMP_NOTIFICACAO N
            LEFT JOIN ORCUSUARIO U ON (N.IDUSUARIO_REMETENTE = U.IDUSUARIO)
            WHERE N.LIDA = 'N' 
              AND N.IDUSUARIO_DESTINO = $idDestino 
            ORDER BY N.DATA DESC";
            
    // 3. Chamada utilizando a função de conexão do conectaOracle.php
    return selectOracle($sql);
}

/**
 * Marca uma ou todas as notificações como lidas
 */
function marcarNotificacaoLida(int $idUsuario, int $idChamado) {
    $sql = "UPDATE VMP_NOTIFICACAO 
            SET LIDA = 'S', DTLEITURA = CURRENT_TIMESTAMP
            WHERE IDUSUARIO_DESTINO = $idUsuario 
              AND IDCHAMADO = $idChamado";
    // varDump2($sql);
    return executarOracle($sql);
}
