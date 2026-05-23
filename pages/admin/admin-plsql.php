<div class="container-fluid mt-5 pe-3">
	<div class="row">
		<?php 
		require_once('pages/admin/function.php'); 
		require_once('pages/admin/controller.php'); 
		require_once('pages/admin/sidebar.php'); 
		?>
		<main class="col-11 ms-sm-auto px-3">
			<div class="row">
				<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
					<h1 class="h2"><i class="fa-solid fa-database"></i> Consulta Oracle</h1>
					<!-- <button href="index.php?op=12" class="btn btn-outline-primary"> <i class="fas fa-tasks"></i> Novo Inventário</button> -->
				</div>
			</div>

			<div class="row">
				<form class="row" action="index.php" method="POST">
					<input type="hidden" name="op" value="<?=$dados['op']?>">

					<div class="col-md-10">
						<textarea name="scriptsql" id="scriptsql" class="form-control text-sm" rows="10"><?=$dados['scriptsql']?></textarea>
					</div>


					<div class="col-2">
						<button class="btn btn-primary" type="submit" name="acao" value="executar">
							<i class="fa-solid fa-search"></i> Pesquisar
						</button>
					</div>
				</form>

			</div>

			<br><br>

	 
			<?php
			if($_POST['acao'] == 'executar'){
					if(isset($_POST['scriptsql']) && $_POST['scriptsql'] <> ""){

							$sql = TRIM($_POST['scriptsql']);
							$tipo = mb_strtoupper(reset(explode(" ", $sql)));

							if(($tipo == "SELECT") || ($tipo == "WITH")){

									$retorno = selectOracle($sql);

									if($retorno <> false){
										$cont_rows=0;
										foreach ($retorno as $retorno2) {
											$cont_colums=0;
											foreach ($retorno2 as $key => $value) {
												if($cont_rows==0){
													$ROWS[$cont_colums] = $key; 
												}
												$COLS[$cont_rows][$cont_colums++] = $value; 
											}
											$cont_rows++;
										}
									}
								 // vardump2($COLS); 


									echo '    <div class="table-responsive me-3">';
									echo '      <table id="tb_default" class="table table-bordered table-hover table-scrollx" style="width: 100%">';
									echo '        <thead>';
									echo '          <tr>';

									$t=0;
									if(!is_null($ROWS)){
											foreach ($ROWS as $value) {
													if($t==0){ 
															echo '<th>#</th>'; 
													}
													echo '        <th>'.$value.'</th>';
													$t++;
											}
									}

									echo '          </tr>';
									echo '        </thead>';
									echo '        <tbody>';
									$p=0;

									if(!is_null($ROWS)){
											foreach ($COLS as $value) {
													echo '       <tr>';
													echo '<th>'.++$p.'</th>';
													foreach ($value as $value2) {
															echo '       <td>'.$value2.'</td>';
													}
													echo '       </tr>';
											}
									} else {
											echo("<h3>Nenhum registro encontrado</h3>");
									}

									echo '        </tbody>';
									echo '      </table>';
									echo '    </div>';


							} else {
									echo '    <div class="box-header">';
									echo '        <h5 class="box-title"><b>'.trim($sql).'</b></h5>';
									echo '    </div>';
									varDump2(executarOracle($sql));
							}
							
							
					}
			//    die;

			} else {
					$sql = "";
			}
			?>

		</main>
	</div><!-- row -->
</div><!-- container-fluid -->
