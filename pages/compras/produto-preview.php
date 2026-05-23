<div class="container-fluid mt-5 text-sm">
  <div class="row">
    <?php 
    require_once('pages/compras/function.php'); 
    require_once('pages/compras/controller.php'); 
    require_once('pages/compras/sidebar.php'); 
		require_once('pages/compras/produto-validaArquivo.php'); 
    if ($_SESSION['login']['MATRICULA'] == "") {
      echo '<div class="alert alert-danger" role="alert">O seu cadastro de usuário está incompleto.</h3>É obrigatório que o campo Matrícula Winthor do seu usuário esteja preenchido corretamente<br><a class="btn btn-primary" href="index.php?op=12&edit&id='.$_SESSION['login']['IDUSUARIO'].'">Clique aqui para atualizar o seu cadastro</div>';
    }
    ?>
    <main class="col-11 ms-sm-auto px-3">
      <div class="row">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><i class="fa-solid fa-boxes"></i> Produtos - Preview</h1>
          <div class="float-end">
            <div class="btn-group">
              <a 
              	href="index.php?op=110&nav=compras&aba=produto&acao=clear" 
              	class="btn btn-outline-secondary"  
              	title="Limpar Lista"
              	> 
                <i class="fa-solid fa-broom"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
	    <?php if (isset($_SESSION['ARQUIVO']) && !empty($_SESSION['ARQUIVO'])): ?>
		<div class="table-responsive">
		<table id="tb_produtos" class="table table-bordered table-striped table-hover mt-4">
			<thead>
				<tr>
					<th>#</th>
					<th>NUMORIGINAL</th>
					<th>MARCA</th>
					<th>DESCRICAO</th>
					<th>CODFAB</th>
					<th>NBM</th>
					<th>ORIGEM</th>
					<th>IMPORTADO</th>
					<th>FORNECEDOR</th>
					<th>COMISSÃO</th>
					<th>REVENDA</th>
					<th>LOCACAO</th>
					<th>DEPARTAMENTO</th>
					<th>SEÇÃO</th>
					<th>CODWINT</th>
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
						echo '<th scope="row">'.($key+1).'</td>';
						echo '<th>'.$value['NUMORIGINAL'].'</td>';
						if (!empty($value['V_MARCA'])) {
							$CONTA_ERROS++;
							echo '<td><span class="badge bg-danger" title="'.$value['V_MARCA'].'"><i class="fa-solid fa-ban"></i></span> '.$value['MARCA'].'</td>';
						} else {
							echo '<td><span class="badge bg-success" title="'.$value['CODMARCA'].'- '.$value['MARCA'].'"><i class="fa-solid fa-check"></i></span> '.$value['MARCA'].'</td>';
						}
						if ($value['V_DESCRICAO']) {
							$CONTA_ERROS++;
							echo '<td><span class="badge bg-danger" title="'.$value['V_DESCRICAO'].'"><i class="fa-solid fa-ban"></i></span> '.$value['DESCRICAO'].'</td>';
						} else {
							echo '<td><span class="badge bg-success" title=""><i class="fa-solid fa-check"></i></span> '.$value['DESCRICAO'].'</td>';
						}
						if ($value['V_CODFAB']) {
							$CONTA_ERROS++;
							echo '<td><span class="badge bg-danger" title="'.$value['V_CODFAB'].'"><i class="fa-solid fa-ban"></i></span> '.$value['CODFAB'].'</td>';
						} else {
							echo '<td><span class="badge bg-success" title=""><i class="fa-solid fa-check"></i></span> '.$value['CODFAB'].'</td>';
						}
						if ($value['V_NBM']) {
							$CONTA_ERROS++;
							echo '<td><span class="badge bg-danger" title="'.$value['V_NBM'].'"><i class="fa-solid fa-ban"></i></span> '.$value['NBM'].'</td>';
						} else {
							echo '<td><span class="badge bg-success" title="NCM: '.$value['CODNCM'].'- '.$value['CODNCMDESC'].'"><i class="fa-solid fa-check"></i></span> '.$value['CODNCM'].'</td>';
						}
						if ($value['V_CODORIGEM']) {
							$CONTA_ERROS++;
							echo '<td><span class="badge bg-danger" title="'.$value['V_CODORIGEM'].'"><i class="fa-solid fa-ban"></i></span> '.$value['CODORIGEM'].'</td>';
						} else {
							echo '<td><span class="badge bg-success" title="'.$value['ORIGEM'].'"><i class="fa-solid fa-check"></i></span> '.$value['CODORIGEM'].'</td>';
						}
						echo '<td><span class="badge bg-success" title="'.(($value['IMPORTADO']=="S")?"PRODUTO IMPORTADO":"PRODUTO NACIONAL").'"><i class="fa-solid fa-check"></i></span> '.$value['IMPORTADO'].'</td>';
						if ($value['V_CODFORNEC']) {
							$CONTA_ERROS++;
							echo '<td><span class="badge bg-danger" title="'.$value['V_CODFORNEC'].'"><i class="fa-solid fa-ban"></i></span> '.$value['CODFORNEC'].'</td>';
						} else {
							echo '<td><span class="badge bg-success" title="CODFORNEC: '.$value['CODFORNEC'].'- '.$value['FORNECEDOR'].'"><i class="fa-solid fa-check"></i></span> '.$value['CODFORNEC'].'- '.$value['FORNECEDOR'].'</td>';
						}
						if ($value['V_PCOMINT1']) {
							$CONTA_ERROS++;
							echo '<td><span class="badge bg-danger" title="'.$value['V_PCOMINT1'].'"><i class="fa-solid fa-ban"></i></span> '.$value['PCOMINT1'].'</td>';
						} else {
							echo '<td><span class="badge bg-success" title="PCOMINT1: '.moedaPHP($value['PCOMINT1']).'"><i class="fa-solid fa-check"></i></span> '.moedaPHP($value['PCOMINT1']).' %</td>';
						}
						echo '<td><span class="badge bg-success" title="'.(($value['REVENDA']=="S")?"PRODUTO REVENDA":"PRODUTO CONSUMO").'"><i class="fa-solid fa-check"></i></span> '.$value['REVENDA'].'- '.(($value['REVENDA']=="S")?"PRODUTO REVENDA":"PRODUTO CONSUMO").'</td>';

						echo '<td><span class="badge bg-success" title="LOCACAO: '.$value['LOCACAO'].'"><i class="fa-solid fa-check"></i></span> '.$value['LOCACAO'].'</td>';
						if ($value['V_CODEPTO']) {
							$CONTA_ERROS++;
							echo '<td><span class="badge bg-danger" title="'.$value['V_CODEPTO'].'"><i class="fa-solid fa-ban"></i></span> '.$value['CODEPTO'].'</td>';
						} else {
							echo '<td><span class="badge bg-success" title="DEPARTAMENTO: '.$value['DEPARTAMENTO'].'"><i class="fa-solid fa-check"></i></span> '.$value['CODEPTO'].'- '.$value['DEPARTAMENTO'].'</td>';
						}
						if ($value['V_CODSEC']) {
							$CONTA_ERROS++;
							echo '<td><span class="badge bg-danger" title="'.$value['V_CODSEC'].'"><i class="fa-solid fa-ban"></i></span> '.$value['CODSEC'].'</td>';
						} else {
							echo '<td><span class="badge bg-success" title="SECAO: '.$value['SECAO'].'"><i class="fa-solid fa-check"></i></span> '.$value['CODSEC'].'- '.$value['SECAO'].'</td>';
						}
						
						if ($value['STATUS'] == "NOVO") {
							echo '<td><span class="badge bg-primary"><i class="fa-solid fa-info"></i></span><strong> NOVO</strong></td>';
						} else {
							echo '<td><span class="badge bg-success"><i class="fa-solid fa-check" title="CODPROD: '.$value['CODPROD'].'-'.$value['DV'].' '.$value['DESCRICAO'].'"></i></span> '.$value['CODPROD'].'-'.$value['DV'].'</td>';
						}
						echo '</tr>';
					}
				}
				?>
			</tbody>
		</table>
	    </div>
	    <?php endif ?>
      </div>

			<div class="row float-start">
				<?php  
				// varDump2($_SESSION['INEXISTENTES']);
				if (isset($_SESSION['MARCAS_INEXISTENTES']) && !empty($_SESSION['MARCAS_INEXISTENTES'])) {
					echo '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalNovasMarcas"> Validar novas marcas</button>';
					include("pages/compras/modalNovasMarcas.php");
				} else {

					if (isset($_SESSION['NCM_INEXISTENTES']) && !empty($_SESSION['NCM_INEXISTENTES'])) {
						echo '<a class="btn btn-danger" href="index.php?op='.$dados['op'].'&acao=exportaNCMnaoCadastrados"> Lista NCM não cadastrados</a>';
					} else {

						if ($CONTA_ERROS > 0) {
							echo '<div class="alert alert-danger" role="alert">Você deve corrigir os problemas para prosseguir</div>';
						} else {
							echo '<a href="index.php?op=112&nav=compras&aba=produto" class="btn btn-lg btn-success"><i class="fa-solid fa-forward"></i> PROSSEGUIR </a>';
						}
					}
				}
				?>
			</div>      

    </main>
  </div><!-- row -->
</div><!-- container-fluid -->