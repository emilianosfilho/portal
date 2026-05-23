<?php
// $debug = true;

if ($debug) varDump2($dados);

if (isset($dados['acao'])) {
		switch ($dados['acao']) {

				case 'clear':
					foreach ($_SESSION as $key => $value) {
						if ($key !== "login" && $key !== "NOFIFICACOES") {
							unset($_SESSION[$key]);
						}
					}
					// varDump2($_SESSION);
					break;
					
				//############################################
				case 'enviarArquivo':
					if ($debug) varDump2("Entrou no enviarArquivo");
					include("pages/compras/enviaArquivo.php");
					if(!isset($_SESSION['ARQUIVO'])){
							exibeMensagem("O leitor do arquivo retornou vazio.");
					} else {
						if ($debug) varDump2($_SESSION['ARQUIVO']); 
						if ($debug) die(); 

						if ($dados['NOMEARQUIVO']) {
							switch ($dados['NOMEARQUIVO']) {
								case 'PRODUTO':
									break;
								case 'PRECIFICACAO':
									break;
								case 'PEDCOMPRA':
									redireciona("index.php?op=119");
									break;
								case 'VIDE':
									redireciona("index.php?op=123");
									break;
								case 'ANALISEARQUIVO':
									redireciona("index.php?op=125");
									break;
								case 'CLASSES':
									redireciona("index.php?op=127");
									break;

								default:
									insereModal("info", "Tipo de arquivo não identificado: ".$dados['NOMEARQUIVO']);  
									// varDump2($dados);
									break;
							}
						}
					}
					break;


				//############################################
				case 'gerarArquivoAnaliseCompra':
					require_once ("pages/compras/gerarArquivoAnaliseCompra.php");
					break;


				//############################################
				// BLOCO PRODUTO
				case 'pesquisarprodutos':
					pesquisarprodutos($dados);
					break;
				case 'produtoExcluir':
					if (isset($_SESSION['PRODUTOS'][$dados['key']])) {
							unset($_SESSION['PRODUTOS'][$dados['key']]);
					}
					break;
				// case 'produto-PDF-consulta':
				// 	abreNova("pages/compras/produto-PDF-consulta.php");
				// 	break;
				// case 'modalAlterarLocacao':
				// 	include("pages/compras/modalAlterarLocacao.php");
				// 	break;
				// case 'validaArquivoPrecificacao':
				// 	include("pages/compras/enviaArquivo.php");
				// 	include("pages/compras/precificacao-validaArquivo.php");
				// 	include("pages/compras/precificacao-preview.php");
				// 	break;
				case 'produto-exportaResultado':
					abreNova("pages/compras/produto-exportaResultado.php");
					break;


				//###################################################
				// BLOCO PEDIDO DE COMPRA
				case 'consultarPedidoCompras':
					consultarPedidoCompras($dados);
					break;

				case 'salvarPedCompra':
					include("pages/compras/pedcompra-salvarPedCompra.php");
					break;


				//###################################################
				// BLOCO VIDE
				case 'consultarVide':
					consultarVide($dados);
					break;

				case 'modalNovoVide':
					include_once("pages/compras/vide-modalNovo.php");
					break;

				case 'salvarNovoVide':
					salvarNovoVide($dados);
					break;

				case 'modalEditarVide':
					include_once("pages/compras/vide-modalEditar.php");
					break;

				case 'salvarAlteracaoVide':
					salvarAlteracaoVide($dados);
					break;

				case 'modalExcluirVide':
					include_once("pages/compras/vide-modalExcluir.php");
					break;

				case 'excluirVide':
					excluirVide($dados);
					break;

				case 'aplicarCadastroVide':
					include_once("pages/compras/vide-aplicarCadastroVide.php");
					break;


				case 'insereNovasMarcas':
					include("pages/compras/insereNovasMarcas.php");
					break;

				case 'excel_analiseArquivoCompra':
					abreNova("pages/compras/excel_analiseArquivoCompra.php");
					break;


						
				case 'exportaNCMnaoCadastrados':
					if (isset($_SESSION['RELATORIO'])) {
							unset($_SESSION['RELATORIO']);
					}
					$_SESSION['RELATORIO']['DESCRICAO'] = "NCM NÃO CADASTRADOS";
					$_SESSION['RELATORIO']['lista']  = $_SESSION['NCM_NAO_CADASTRADOS'];
					abreNova("exportExcel.php");
					break;

				case 'exportarListaProdutos':
					if (isset($_SESSION['RELATORIO'])) {
							unset($_SESSION['RELATORIO']);
					}
					$_SESSION['RELATORIO']['DESCRICAO'] = "PRODUTOS CADASTRADOS";
					$_SESSION['RELATORIO']['lista']  = formataListaArquivoCadastro($_SESSION['PRODUTOS']);
					abreNova("exportExcel.php");
					break;

				case 'precificacao_pesquisar':
					precificacao_pesquisar($dados);
					break;

				case 'precificacao-modalEditarPreco':
					include_once ("pages/compras/precificacao-modalEditarPreco.php");
					break;

				case 'precificacao_editarPreco':
					precificacao_editarPreco($dados);
					break;

				case 'precificacao-modalZerarPreco':
					include_once ("pages/compras/precificacao-modalZerarPreco.php");
					break;

				case 'precificacao_zerarPreco':
					precificacao_zerarPreco($dados);
					break;

				case 'aplicarPrecificacao':
					redireciona("pages/compras/precificacao-aplicar.php");
					break;

				case 'enviarArquivoAlteracaoMassa':
					include('pages/compras/enviaArquivo.php');
					redireciona('index.php?op=117');
					break;
				
				case 'aplicarAlteracaoMassa':
					include('pages/compras/aplicarAlteracaoMassa.php');
					break;

				case 'consultaSugestaoCompra':
					consultaSugestaoCompra($dados);
					break;
				##########################################

				case 'classeInsert':
					classeInsert($dados);
					break;
				
				case 'pesquisarClasses':
					$_SESSION['LISTACLASSES'] = pesquisarClasses($dados);
					break;

				case 'cadastrarLinhas':
					cadastrarLinhas($dados);
					break;

				// case 'aplicarClasses':
				// 	aplicarClasses();
				// 	break;

				case 'classes_aplicarArquivo':
					include_once("pages/compras/classes_aplicarArquivo.php");
					break;

					
				##########################################
				default:
					// varDump2($dados);
					exibeMensagem("Ação ainda não implementada: ".$dados["acao"]);
					break;
		}
}
