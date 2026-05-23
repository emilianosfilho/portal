<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/compras/function.php'); 
    require_once('pages/compras/controller.php'); 
    require_once('pages/compras/sidebar.php'); 
    unset($_SESSION['LISTAPRODUTOSSALVOS']);
    unset($_SESSION['VALIDACADASTROS']);
    unset($_SESSION['INEXISTENTES']);
    if ($_SESSION['login']['MATRICULA'] == "") {
      echo '<div class="alert alert-danger" role="alert">O seu cadastro de usuário está incompleto.</h3>É obrigatório que o campo Matrícula Winthor do seu usuário esteja preenchido corretamente<br><a class="btn btn-primary" href="index.php?op=12&edit&id='.$_SESSION['login']['IDUSUARIO'].'">Clique aqui para atualizar o seu cadastro</div>';
    }
    ?>
    <main class="col ms-sm-auto px-3">
			<div class="row">
				<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
					<h1 class="h2"><i class="fa-solid fa-magnifying-glass-dollar"></i> Análise de Compra</h1>
				</div>
			</div>
			<?php  
			require_once('pages/compras/controller.php'); 
			?>
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link <?=((!isset($dados['tab']) || $dados['tab']=="analise")?"active":"")?>" id="analise-tab" data-bs-toggle="tab" data-bs-target="#analise" type="button" role="tab" aria-controls="analise" aria-selected="true">8022 ANÁLISE DE COMPRAS</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link <?=((isset($dados['tab']) && $dados['tab']=="arquivo")?"active":"")?>" id="arquivo-tab" data-bs-toggle="tab" data-bs-target="#arquivo" type="button" role="tab" aria-controls="arquivo" aria-selected="false">ANÁLISE DE PRODUTOS</button>
				</li>
			</ul>
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane fade <?=((!isset($dados['tab']) || $dados['tab']=="analise")?"show active":"")?>" id="analise" role="tabpanel" aria-labelledby="analise-tab">
				 
					<div class="row">
						<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
							<div>
								<h3 class="mt-4"><i class="fas fa-search"></i> 8022 - Sugestão de compras</h3>
								<p>Nesta seção você poderá consultar os produtos movimentados para análise de compras.</p>
							</div>
						</div>
						<div class="row">
							<form class="row" action="pages/compras/excel_analiseCompra.php" method="POST" target="_blank">
								<input type="hidden" name="op" value="124">
								<input type="hidden" name="tab" value="analise">
								<input type="hidden" name="NOMEARQUIVO" value="ANALISECOMPRA">

								<div class="col">
									<label class="form-label mb-4">NUMORIGINAL</label>
									<input type="text" name="NUMORIGINAL" class="form-control" autofocus autocomplete="off" placeholder="NUMORIGINAL" disabled>
								</div>

								<div class="col">
									<label class="form-label">Data Inícial<br>Primeiro dia de 4 meses atrás</label>
									<div class="span5" id="sandbox-container">
										<div class="input-group date">
											<input name="dataini4meses" type="text" class="form-control" value="<?=(isset($_POST['dataini4meses']))?$_POST['dataini4meses']:@date('01/m/Y',strtotime("-4 month"))?>">
											<span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
										</div>         
									</div>
								</div>
								<div class="col">
									<label class="form-label">Data Final<br/>Último dia do mês anterior</label>
									<div class="span5" id="sandbox-container">
										<div class="input-group date">
											<input name="datafim4meses" type="text" class="form-control" value="<?=(isset($_POST['datafim4meses']))?$_POST['datafim4meses']:@date('t/m/Y',strtotime("-1 month"))?>">
											<span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
										</div>         
									</div>
								</div>
									
								<div class="col">
									<label class="form-label">Data Inícial<br/>Primeiro dia de 12 meses atrás</label>
									<div class="span5" id="sandbox-container">
										<div class="input-group date">
											<input name="datainiAno" type="text" class="form-control" value="<?=(isset($_POST['datainiAno']))?$_POST['datainiAno']:@date('01/m/Y',strtotime("-12 month"))?>">
											<span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
										</div>         
									</div>
								</div>
								<div class="col">
									<label class="form-label">Data Final<br/>Último dia do mês anterior</label>
									<div class="span5" id="sandbox-container">
										<div class="input-group date">
											<input name="datafimAno" type="text" class="form-control" value="<?=(isset($_POST['datafimAno']))?$_POST['datafimAno']:@date('t/m/Y',strtotime("-1 month"))?>">
											<span class="input-group-addon btn btn-secondary"><i class="fa fa-th"></i></span>
										</div>         
									</div>
								</div>


								<div class="col-1">
									<div class="btn-group float-end  mt-5">
										<button class="btn btn-secondary px-3" type="submit" name="acao" value="gerarArquivoAnaliseCompra">Pesquisar</button>
									</div>
								</div>
							</form>

						</div>
						

					</div>
				</div>
				
				<div class="tab-pane fade <?=((isset($dados['tab']) && $dados['tab']=="arquivo")?"show active":"")?>" id="arquivo" role="tabpanel" aria-labelledby="arquivo-tab">
					
					<div class="row">
						<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
							<div>
								<h3 class="mt-4"><i class="fa-solid fa-file-excel"></i> Enviar Planilha</h3>
								<p>Nesta seção você poderá enviar uma planilha excel contendo uma lista de NUMORIGINAL para a análise.</p>
							</div>
							<a href="<?=@DIR_DOWNLOAD."MODELO_ANALISE_COMPRAS.xlsx"?>" class="btn btn-outline-secondary"><i class="fa-solid fa-file-arrow-down"></i> Baixar Modelo</a>
						</div>
						<div class="col-6">
							<form action="index.php" method="POST" enctype="multipart/form-data">
								<input type="hidden" name="op" value="124">
								<input type="hidden" name="tab" value="arquivo">
								<input type="hidden" name="NOMEARQUIVO" value="ANALISEARQUIVO">
								
								<div class="input-group mb-3">
									<input type="file" name="arquivo" class="form-control" id="inputGroupFile02" required>
									<button type="submit"name="acao" value="enviarArquivo" class="btn btn-secondary" for="inputGroupFile02"><i class="fa-solid fa-share"></i> Enviar Arquivo</button>
								</div>
							</form>
						</div>
					</div>

				</div>
			</div>


		</main>
	</div><!-- row -->
</div><!-- container-fluid -->
