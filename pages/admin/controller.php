<?php 

if (isset($dados['acao'])) {
	switch ($dados['acao']) {
		case 'limparLista':
			foreach ($_SESSION as $key => $value) {
				if ($key <> "login") {
					unset($_SESSION[$key]);
				}
			}
			break;

		case 'usuario_login':
			usuario_login($dados);
			break;

		case 'sair':
			unset($_SESSION);
			break;
	
		case 'usuario_cadastrar':
			usuario_cadastrar($dados);
			break;
		
		case 'usuario_defineAvatar':
			usuario_defineAvatar($dados);
			break;

		case 'usuario_resetarSenha':
			usuario_resetarSenha($dados);
			break;

		case 'usuario_definirSenha':
			usuario_definirSenha($dados);
			break;

		case 'usuario_alterarPermissoes':
			usuario_alterarPermissoes($dados);
			break;

		case 'usuario_inativar':
			usuario_inativar($dados);
			break;

		case 'usuario_ativar':
			usuario_ativar($dados);
			break;
			
		case 'usuario_atualizar':
			usuario_atualizar($dados);
			break;

		case 'liberarAcessoDadosFornec':
			liberarAcessoDadosFornec();
			break;

		case 'equipamento_pesquisar':
			equipamento_pesquisar($dados);
			break;

		case 'executar':
			break;

		default:
			insereModal("info", "Ação não definida: {$dados['acao']}");
			break;
	}
}