<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/logistica/function.php'); 
    require_once('pages/logistica/controller.php'); 
    require_once('pages/logistica/sidebar.php'); 
		require_once('pages/logistica/vide-validaArquivo.php'); 
    ?>
    <main class="col-11 ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">
            <i class="fa-solid fa-retweet"></i> Consulta de Vide
          </h1>
          <div class="btn-group float-end">
            <button class="btn btn-outline-secondary px-3"  title="Cadastrar Novo - Vide" data-bs-toggle="modal" data-bs-target="#vide-modalCadastrar">
              <i class="fa-solid fa-star"></i>
            </button>
            <button class="btn btn-outline-secondary px-3"  title="Enviar Arquivo - Vide" data-bs-toggle="modal" data-bs-target="#vide-modalEnviarArquivo">
              <i class="fa-solid fa-upload"></i>
            </button>
            <a href="?op=40&acao=limparLista&nav=logistica&aba=vide" class="btn btn-outline-secondary"  title="Limpar Lista - Vide"> 
              <i class="fa-solid fa-broom"></i>
            </a>
          </div>
        </div>
      </div>

      <div class="row">
	      <?php if (isset($_SESSION['ARQUIVO']) && !empty($_SESSION['ARQUIVO'])): ?>
	      <div class="card">
	        <div class="card-body">
	        	<div class="table-responsive">
	            <table id="tb_default2" class="table table-bordered table-striped table-hover mt-4">
	              <thead>
									<tr>
										<th>CODPECA</th>
										<th>APLICMARCA</th>
										<th>VIDE</th>
										<th>DESCRICAO</th>
										<th>MARCA</th>
										<th>IMPORTADO</th>
										<th>PRECO</th>
										<th>DATA</th>
										<th>NOMEOPCAO</th>
										<th>IDVIDE</th>
									</tr>
	              </thead>
								<tbody>
									<?php
									if ($_SESSION['ARQUIVO']) {
										$CONTA_ERROS = 0;
											
										foreach ($_SESSION['ARQUIVO'] as $key => $value) {
											// varDump2($value); die();

											echo PHP_EOL;
											echo '<tr>';
											if (!empty($value['V_CODPECA'])) {
												$CONTA_ERROS++;
												echo '<td><span class="badge bg-danger" title="'.$value['V_CODPECA'].'"><i class="fa-solid fa-ban"></i></span> '.$value['CODPECA'].'</td>';
											} else {
												echo '<td><span class="badge bg-success" title=""><i class="fa-solid fa-check"></i></span> '.$value['CODPECA'].'</td>';
											}
											if ($value['V_APLICMARCA']) {
												$CONTA_ERROS++;
												echo '<td><span class="badge bg-danger" title="'.$value['V_APLICMARCA'].'"><i class="fa-solid fa-ban"></i></span> '.$value['APLICMARCA'].'</td>';
											} else {
												echo '<td><span class="badge bg-success" title=""><i class="fa-solid fa-check"></i></span> '.$value['APLICMARCA'].'</td>';
											}
											echo '<td>'.$value['VIDE'].'</td>';
											if ($value['V_DESCRICAO']) {
												$CONTA_ERROS++;
												echo '<td><span class="badge bg-danger" title="'.$value['V_DESCRICAO'].'"><i class="fa-solid fa-ban"></i></span> '.$value['DESCRICAO'].'</td>';
											} else {
												echo '<td><span class="badge bg-success"><i class="fa-solid fa-check"></i></span> '.$value['DESCRICAO'].'</td>';
											}
											echo '<td>'.$value['MARCA'].'</td>';
											echo '<td>'.$value['IMPORTADO'].'</td>';
											echo '<td>'.$value['PRECO'].'</td>';
											echo '<td>'.$value['DATA'].'</td>';

											echo '<td><span class="badge bg-success" title=""><i class="fa-solid fa-check"></i></span> '.$value['NOMEOPCAO'].'</td>';

											if ($value['IDVIDE']) {
												echo '<td><span class="badge bg-success" title=""><i class="fa-solid fa-check"></i></span> '.$value['IDVIDE'].'</td>';
											} else {
												echo '<td><span class="badge bg-primary"><i class="fa-solid fa-star"></i></span> NOVO</td>';
											}

											echo '</tr>';
										}
									}
									?>
								</tbody>
	            </table>
	          </div>
	        </div>
	      </div>
	      <?php endif ?>
      </div>

			<div class="row float-start">
				<?php  
				if ($CONTA_ERROS > 0) {
					echo '<div class="alert alert-danger" role="alert">Você deve corrigir os problemas para prosseguir</div>';
				} else {
					echo '<a href="index.php?op=42&nav=logistica&aba=vide" class="btn btn-lg btn-success"><i class="fa-solid fa-forward"></i> PROSSEGUIR </a>';
				}
				?>
			</div>      

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->