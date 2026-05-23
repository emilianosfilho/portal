<?php  

function buscaSessoesBloqueadas(){
	$sql = 'select h.session_id     Sessao_Travadora,
       ub.username      Usuario_Travador,
       w.session_id     Sessao_Esperando,
       uw.username      Usuario_Esperando,
       w.lock_type,
       h.mode_held,
       w.mode_requested,
       w.lock_id1,
       w.lock_id2
  from dba_locks w, dba_locks h, v$session ub, v$session uw';
  $sql .= "
 where h.blocking_others = 'Blocking'
   and h.mode_held != 'None'
   and h.mode_held != 'Null'
   and w.mode_requested != 'None'
   and h.session_id = ub.sid
   and w.lock_type = h.lock_type
   and w.lock_id1 = h.lock_id1
   and w.lock_id2 = h.lock_id2
   and w.session_id = uw.sid";
   $ret = selectOracle($sql);
   // varDump2($sql);
   // varDump2($ret);
   return $ret;
}

function buscaMontadoras(){
  $sql = "SELECT * FROM VMP_MONTADORA order by montadora asc";
  return selectOracle($sql);
}

function equipamento_pesquisar(array $dados){
  $sql = "";
  $_SESSION['EQUIPAMENTOS'] = selectOracle($sql);
}

function usuario_login(array $dados){
  limpaSession();
  $login  = mb_strtoupper(trim((string) $dados['login']), 'UTF-8');
  $senha  = md5(trim($dados['senha']));
  // varDump2($dados);
  // varDump2("login: {$login}");
  // varDump2("senha: {$dados['senha']}");
  // varDump2("senhaMD5: {$senha}");
  // die();

  $USUARIO = buscaUsuarioLogin($login, $senha);
  if(!$USUARIO){
    // varDump2($USUARIO);
    insereModal('danger', 'o E-mail ou a senha digitada é inválido.<br />Favor verifique se a tecla CapsLock do seu teclado está ativa!');
    geraLogAcesso($login, $senha, 'N');
  } else {
    if(isset($USUARIO['DTEXCLUSAO'])){
      exibeMensagem('Este cadastro foi excluído. <br>Favor entre em contato com o administrador!');
    } else {
      geraLogAcesso($login, $senha, 'S');
      atualizaDataUltimoAcesso($USUARIO['IDUSUARIO']);
      $_SESSION['login'] = $USUARIO;
      $_SESSION['login']['PERMISSOES'] = buscaPermissoesUsuario($USUARIO['IDUSUARIO']);
      redireciona("index.php");
    }
  }
}

function buscaUsuarioLogin(string $login, string $senha){
  $sql = "SELECT * FROM ORCUSUARIO WHERE LOGIN = '{$login}' AND SENHA = '{$senha}'";
  $ret = selectOracle($sql);
  if ($ret) {
    $sql = "UPDATE ORCUSUARIO 
               SET DTULTLOGIN = SYSDATE 
             WHERE LOGIN = '".$login."' 
               AND SENHA = '".$senha."'";
    executarOracle($sql);
    return reset($ret);
  } else {
    return false;
  }
}

function buscaResumoUsuarios(){
  $sql = "select sum(case when u.dtexclusao is null then 1 else 0 end) as QT_ATIVOS,
                 sum(case when u.dtexclusao is not null then 1 else 0 end) as QT_INATIVOS,
                 sum(1) as qt_total
          from orcusuario u";
  $ret = selectOracle($sql);
  return ($ret === true)?reset($ret):false;
}

function atualizaDataUltimoAcesso(int $IDUSUARIO){
  $sql = "UPDATE ORCUSUARIO SET DTULTLOGIN = SYSDATE WHERE IDUSUARIO = {$IDUSUARIO}";
  return executarOracle($sql);
}


function usuario_cadastrar(array $dados){
  $dados['NOME']        = trim(mb_strtoupper($dados['NOME'], 'UTF-8'));
  $dados['LOGIN']       = trim(mb_strtoupper($dados['LOGIN'], 'UTF-8'));
  $dados['EMAIL']       = trim(mb_strtolower($dados['EMAIL'], 'UTF-8'));
  $dados['PERFIL']      = trim(mb_strtolower($dados['PERFIL'], 'UTF-8'));
  $dados['MATRICULA']   = (($dados['MATRICULA'])?intval($dados['MATRICULA']):'NULL');
  $dados['CODUSUR']     = (($dados['CODUSUR'])?intval($dados['CODUSUR']):'NULL');
  $dados['CODUSUR2']    = (($dados['CODUSUR2'])?intval($dados['CODUSUR2']):'NULL');
  $dados['CODCLI']      = (($dados['CODCLI'])?intval($dados['CODCLI']):'NULL');
  $dados['SENHA']       = md5(1234);
  $dados['AVATAR']      = @AVATAR_PADRAO;
  $dados['STATUS']      = 'A';

  
  if (buscaIDusuarioPorEmail($dados['EMAIL'])){
    exibeMensagem("O E-mail informado ".$dados['EMAIL']." já possui cadastro.");
    redireciona('index.php?op=13&IDUSUARIO='.buscaIDusuarioPorEmail($dados['EMAIL']));
  } else {

    $sql = "INSERT INTO ORCUSUARIO (NOME, LOGIN, EMAIL, PERFIL, MATRICULA, CODUSUR, CODUSUR2, CODCLI, SENHA, AVATAR, STATUS) VALUES (
      '".$dados['NOME']."', 
      '".$dados['LOGIN']."', 
      '".$dados['EMAIL']."', 
      '".mb_strtoupper($dados['PERFIL'])."', 
      ".$dados['MATRICULA'].", 
      ".$dados['CODUSUR'].", 
      ".$dados['CODUSUR2'].", 
      ".$dados['CODCLI'].",
      '".$dados['SENHA']."', 
      '".$dados['AVATAR']."', 
      '".$dados['STATUS']."'
      )";
    if (executarOracle($sql)){
      exibeMensagem("Usuário cadastrado com sucesso!");
      redireciona('index.php?op=11&nav=admin&aba=usuarios&IDUSUARIO='.buscaIDusuarioPorEmail($dados['EMAIL']));
    } else {
      insereModal("danger", "ERRO ao cadastrar o usuário.");
      varDump2($sql);
    }
  }
}

function buscaIDusuarioPorEmail(string $email){
  $sql = "SELECT * FROM ORCUSUARIO WHERE EMAIL = '".$email."'";
  if ($ret = selectOracle($sql) ){
    return $ret[0]['IDUSUARIO'];
  } else {
    return false;
  }
}

function buscaUsuariosAtivos(){
  $sql = "SELECT   u.*
                   ,CAST( u.dtultlogin AS DATE) AS dtultlogin
            FROM   orcusuario u
           WHERE   u.dtexclusao IS NULL";
  return selectOracle($sql);
}

function buscaUsuariosInativos(){
  $sql = "SELECT   u.*
                   ,CAST( u.dtultlogin AS DATE) AS dtultlogin
            FROM   orcusuario u
           WHERE   u.dtexclusao IS NOT NULL";
  return selectOracle($sql);
}

function buscaTodosUsuarios(){
  $sql = "SELECT * FROM ORCUSUARIO";
  return selectOracle($sql);
}

function usuario_defineAvatar(array $dados){
  $sql = "UPDATE ORCUSUARIO SET AVATAR = '".$dados['AVATAR']."' WHERE IDUSUARIO = ".$dados['IDUSUARIO'];
  
  if (isset($dados['AVATAR']) 
    && isset($dados['IDUSUARIO']) 
    && !empty($dados['AVATAR']) 
    && !empty($dados['IDUSUARIO'])) {
    
    if (executarOracle($sql)) {
      if ($_SESSION['login']['IDUSUARIO'] == $dados['IDUSUARIO']) {
        $_SESSION['login'] = buscaUsuarioId($dados['IDUSUARIO']);
      }
      insereModal("success", "Avatar alterado com sucesso");
    } else {
      insereModal("danger", "ERRO ao alterar o Avatar");
    }

  } else {
    varDump2($sql);
    insereModal("danger", "ERRO dados inválidos");
  }
}

function usuario_resetarSenha(array $dados){

  if (isset($dados['IDUSUARIO']) && !empty($dados['IDUSUARIO'])) {

    $novaSenha = (string) rand(1000, 9999);
    // varDump2($novaSenha); 
    $sql = "UPDATE ORCUSUARIO SET SENHA = '".md5($novaSenha)."' WHERE IDUSUARIO = ".$dados['IDUSUARIO'];
    if(executarOracle($sql)){
      if ($_SESSION['login']['IDUSUARIO'] == $dados['IDUSUARIO']) {
        $_SESSION['login']['SENHA'] = md5($novaSenha);
      }      
      insereModal("success", "Senha do usuário resetada com sucesso!</p><p>Usuário: ".$dados['LOGIN']."</p><p>Nova Senha:  <b>".$novaSenha."</b></p>");
    } else {
      insereModal("danger", "Erro ao resetar a senha do usuário");
    }

  } else {
    varDump2($dados);
    insereModal("danger", "ERRO dados inválidos");
  }

}

function usuario_definirSenha(array $dados){
  // varDump2($dados); die();

  if (!isset($dados['IDUSUARIO']) || empty($dados['IDUSUARIO'])) {
    throw new Exception("ERRO ao executar usuario_definirSenha. IDUSUARIO inválido!");
  }

  if (trim($dados['SENHA_NEW']) !== trim($dados['CONFIRMACAO'])) {
    throw new Exception("ERRO ao executar usuario_definirSenha. A nova senha não está igual a confirmação!");
  }

  $novaSenha = trim($dados['SENHA_NEW']);
  // varDump2($novaSenha); 
  $sql = "UPDATE ORCUSUARIO SET SENHA = '".md5($novaSenha)."' WHERE IDUSUARIO = ".$dados['IDUSUARIO'];
  if(executarOracle($sql)){
    if ($_SESSION['login']['IDUSUARIO'] == $dados['IDUSUARIO']) {
      $_SESSION['login']['SENHA'] = md5($novaSenha);
    }      
    insereModal("success", "Senha do usuário definida com sucesso!</p><p>Usuário: <b>".ucfirst(mb_strtolower($dados['LOGIN']))."</b></p><p>Nova Senha:  <b>".$novaSenha."</b></p>");
  } else {
    throw new Exception("ERRO ao executar usuario_definirSenha.");
  }
}


function buscaUsuarioId(int $IDUSUARIO){
  if ($IDUSUARIO) {
    $sql = "SELECT * FROM ORCUSUARIO WHERE IDUSUARIO = ".$IDUSUARIO;
    $ret = selectOracle($sql);
    // varDump2($sql); 
    // varDump2($ret); 
    // die();
    return reset($ret);
  } else {
    return false;
  }
}

function usuario_alterarPermissoes(array $dados){
  // varDump2($dados);
  if ($dados['IDUSUARIO']<>""){
    //LIMPA PERMISSÕES QUE JÁ TENHA CADASTRADO
    $sql = "DELETE FROM ORCPERMISSAOUSUARIO WHERE IDUSUARIO = ".$dados['IDUSUARIO'];
    if(executarOracle($sql) === false){
      insereModal('danger', 'Erro ao limpar as Permissões existentes.');
    } else {
      if (isset($dados['permissao'])) {
        $sql = "INSERT ALL ".PHP_EOL;
        foreach ($dados['permissao'] as $IDPERMISSAO => $value) {
          $sql .= " INTO ORCPERMISSAOUSUARIO (IDUSUARIO, IDPERMISSAO) VALUES (".$dados['IDUSUARIO'].", ".$IDPERMISSAO.")".PHP_EOL;
        }
        $sql .= "SELECT * FROM DUAL".PHP_EOL;
        // varDump2($sql);
        if (executarOracle($sql)){
            if ($_SESSION['login']['IDUSUARIO'] == $dados['IDUSUARIO']) {
              $_SESSION['login'] = buscaUsuarioId($dados['IDUSUARIO']);
            }
            insereModal('success', 'Permissões do usuário atualziadas com sucesso.');
        } else {
          insereModal('danger', 'Erro ao atualziar as Permissões do usuário.');
        }
      }
    }
  }
}

function usuario_inativar(array $dados){
  if (isset($dados['IDUSUARIO']) && !empty($dados['IDUSUARIO'])) {

    $sql = "UPDATE ORCUSUARIO 
               SET STATUS = 'I', 
                   DTEXCLUSAO = SYSDATE, 
                   EMAIL = NULL, 
                   LOGIN = NULL, 
                   SENHA = NULL  
             WHERE IDUSUARIO = ".$dados['IDUSUARIO'];
    if (executarOracle($sql)){
        if ($_SESSION['login']['IDUSUARIO'] == $dados['IDUSUARIO']) {
          unset($_SESSION['login']);
        }
        insereModal('success', 'Usuário bloqueado com sucesso.');
    } else {
      insereModal('danger', 'Erro ao bloquear o usuário.');
    }
  } else {
    insereModal('danger', 'ERRO IDUSUÁRIO inválido');
  }

}

function usuario_ativar(array $dados){
  if (isset($dados['IDUSUARIO']) && !empty($dados['IDUSUARIO'])) {

    $sql = "UPDATE ORCUSUARIO SET STATUS = 'A', DTEXCLUSAO IS NULL  WHERE IDUSUARIO = ".$dados['IDUSUARIO'];
    if (executarOracle($sql)){
        if ($_SESSION['login']['IDUSUARIO'] == $dados['IDUSUARIO']) {
          unset($_SESSION['login']);
        }
        insereModal('success', 'Usuário Ativado com sucesso.');
    } else {
      insereModal('danger', 'Erro ao Ativar o usuário.');
    }
  } else {
    insereModal('danger', 'ERRO IDUSUÁRIO inválido');
  }

}

function usuario_atualizar(array $data){
  $data['NOME'] = mb_strtoupper(trim((string) $data['NOME']), 'UTF-8');
  $data['NOME'] = str_replace("&", "", $data['NOME']);
  $data['NOME'] = str_replace("'", " ", $data['NOME']);
  $data['LOGIN'] = mb_strtoupper(trim((string) $data['LOGIN']), 'UTF-8');
  $data['EMAIL'] = mb_strtolower(trim((string) $data['EMAIL']), 'UTF-8');

  $sql = "UPDATE ORCUSUARIO 
             SET NOME = '{$data['NOME']}',  
                 LOGIN = '{$data['LOGIN']}',  
                 EMAIL = '{$data['EMAIL']}',  
                 PERFIL = '{$data['PERFIL']}',  
                 MATRICULA = '{$data['MATRICULA']}',  
                 CODUSUR = '{$data['CODUSUR']}',  
                 CODUSUR2 = '{$data['CODUSUR2']}',  
                 CODCLI = '{$data['CODCLI']}'
           WHERE IDUSUARIO = {$data['IDUSUARIO']}";
  // varDump2($data); 
  // varDump2($sql); 
  // die();

  if ($data['IDUSUARIO'] && !empty($data['IDUSUARIO'])) {
    if (executarOracle($sql) ){
      insereModal("success", "Dados do Usuário atualizados com sucesso!");
    }
  } else {
    insereModal("danger", "IDUSUARIO inválido");
  }
  return;
}

function  geraLogAcesso(string $email, string $senha, string $sucesso){

  $sql = "INSERT INTO ORCACESSO (IDACESSO, DATA, NAVEGADOR, ENDERECOIP, ENDERECOACESSADO, LOGIN, SENHA, SUCESSO, MOBILE) 
  VALUES (NULL, 
    SYSDATE, 
    '".$_SERVER['HTTP_USER_AGENT']."', 
    '".$_SERVER['REMOTE_ADDR']."', 
    '".$_SERVER['HTTP_HOST']."', 
    '".$email."', 
    '".$senha."', 
    '".$sucesso."', 
    '".isMobile()."')";
  executarOracle($sql);
}


function isMobile() {
    if (preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"])){
      return 'S';
    } else {
      return 'N';
    }
}

function buscaPermissoesUsuario(mixed $IDUSUARIO):array{
  $sql = "SELECT P.PERMISSAO
        FROM ORCPERMISSAOUSUARIO PU, ORCPERMISSAO P, ORCUSUARIO U
       WHERE PU.IDPERMISSAO = P.IDPERMISSAO
         AND PU.IDUSUARIO = U.IDUSUARIO
         AND U.IDUSUARIO = ".$IDUSUARIO;
  if ($ret = selectOracle($sql) ){
    $perm = array();
    foreach ($ret as $key => $value) {
      array_push($perm, $value['PERMISSAO']);
    }
    return $perm;
  } else {
    return [];
  }
}

function buscaPermissoes(){
  $sql = "SELECT * FROM ORCPERMISSAO";
  return selectOracle($sql);
}


function  liberarAcessoDadosFornec(){
  $sql = "INSERT INTO PCLIB (
        CODTABELA, 
        CODFUNC, 
        CODIGOA, 
        CODIGON, 
        DATA_LIB, 
        CODFUNC_LIB
    )
    SELECT 
        3,               -- CODTABELA 3 (Fornecedor)
        f.MATRICULA,     -- CODFUNC (O funcionário que recebe a permissão)
        ' ',             -- CODIGOA (código alfanumerico obrigatorio)
        forn.CODFORNEC,  -- CODIGON (O código do fornecedor liberado)
        SYSDATE,         -- DATA_LIB
        1 as CODFUNC_LIB -- CODFUNC_LIB (ID do usuário que realizou a liberação, ex: ADMIN)
    FROM PCEMPR f
    CROSS JOIN PCFORNEC forn
    WHERE f.DT_EXCLUSAO IS NULL 
      AND forn.DTEXCLUSAO IS NULL
      AND f.MATRICULA is not null
      AND nvl(f.situacao, 'A') = 'A'
      AND NOT EXISTS (
          SELECT 1 
          FROM PCLIB L 
          WHERE L.CODTABELA = 3 
            AND L.CODFUNC = f.MATRICULA 
            AND L.CODIGON = forn.CODFORNEC
      )";
  try {
      if (executarOracle($sql)) {
          insereModal('success', 'Liberações de fornecedores atualizadas com sucesso!');
      }
  } catch (Exception $e) {
      insereModal('danger', 'Erro ao processar SQL: ' . $e->getMessage());
  }
}