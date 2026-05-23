<div class="container-fluid mt-5 text-sm">
	<div class="row">
		<?php 
	    require_once('pages/ecommerce/sidebar.php'); 		
		?>
		<main class="col ms-sm-auto px-3">
			<?php 
				require_once("pages/ecommerce/wint_function.php");
				require_once("plugins/HTTP_Request2-2.6.0/HTTP/Request2.php");
				require_once("pages/ecommerce/tray_api_token.php");
				require_once("pages/ecommerce/tray_api_produtos.php");
				require_once("pages/ecommerce/tray_api_categorias.php");
				require_once("pages/ecommerce/tray_api_variacoes.php");
				require_once("pages/ecommerce/tray_controller.php");
		  ?>

			<div class="row">
				<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
					<h1 class="h2">
						<i class="fa-solid fa-cart-shopping-fast"></i> Consulta Produtos - Tray
					</h1>
					<div class="btn-group"> 
						<?php if ($_SESSION['login']['IDUSUARIO'] == 1): ?>
							<button type="button" class="btn btn-xs btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#tray_produtoModalEliminar" title="Eliminar todos os produtos tray"><i class="fa-solid fa-trash"></i> Eliminar Todos</button>
							<a href="tray_atualizacaoDiaria.php" target="blanck" class="btn btn-xs btn-outline-secondary" title="Atualização Diária"><i class="fa-solid fa-refresh"></i> Atualização</a>
						<?php endif ?>
						<button type="button" class="btn btn-xs btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#tray_produtoModalCadastrar" title="Cadastrar Produtos na Tray"><i class="fa-solid fa-plus"></i> Cadastrar</button>
						<a href="index.php?op=<?=$dados['op']?>" class="btn btn-xs btn-outline-secondary" title="Limpar lista"><i class="fa-solid fa-broom"></i></a>
					</div>
				</div>
			</div>

			<div class="card">
				<div class="card-header">
					<form role="form" method="POST" action="index.php" enctype="multipart/form-data"class=" row g-3">
						<input type="hidden" name="op" value="171">
						<input type="hidden" name="aba" value="tray">

					<div class="col-2">
						<label class="form-label">ID TRAY</label>
						<input type="text" name="product_id" value="<?=$dados["product_id"]?>" placeholder="Código Tray" class="form-control" autocomplete="off">
					</div>

					<div class="col">
						<label class="form-label">Name</label>
						<input type="text" name="name" value="<?=$dados["name"]?>" placeholder="PEÇA" class="form-control" autocomplete="off">
					</div>

					<div class="col-2">
						<label class="form-label">Reference / Núm. Original</label>
						<input type="text" name="reference" value="<?=$dados["reference"]?>" placeholder="PEÇA" class="form-control" autocomplete="off">
					</div>
					
					<div class="col-2">
						<div class="d-grid gap-2">
							<button type="submit" name="acao" value="tray_pesquisaProduto" class="btn btn-secondary mt-4"><i class="fa fa-solid fa-search"></i> Pesquisar</button>
						</div>
					</div>

					</form>
				</div>

				<?php if (isset($PRODUTOS) && !empty($PRODUTOS)): ?>
					<div class="table-responsive">
						<table id="tb_default" class="table table-bordered table-striped table-hover text-lg">
							<thead>
								<tr>
									<th>Código</th>
									<th>Produto</th>
									<th>Categoria</th>
									<th>Preço</th>
									<th>Atualização</th>
									<th>Status</th>
									<th>ações</th>
								</tr>
							</thead>

							<tbody>
								<?php
								foreach ($PRODUTOS as $key => $prod) {
									$product = $prod["Product"];
									echo '<tr style="vertical-align: middle;">';
									echo '<td>'.$product['id'].'</td>';
									echo '<td>'.$product['name'].'</td>';
									echo '<td>'.$product["category_name"].'</td>';
									echo '<td style="text-align: center">R$ '.moeda($product['price']).'</td>';
									echo '<td>'.formataDataTrayToBR($product['modified']).'</td>';
									echo '<td>'.
											(($product['available'] == "1")
												?'<span class="badge bg-success">Ativo</span>'
												:'<span class="badge bg-danger">Inativo</span>'
											).
											'</td>';
									echo '<td>';
									echo '	<form method="POST">';
									echo '		<input type="hidden" name="op" value="'.$dados['op'].'">';
									echo '		<input type="hidden" name="product_id" value="'.$product['id'].'">';
									echo '		<div class="btn-group">';
									echo '			<button type="submit" name="acao" value="tray_editarProduto" class="btn btn-sm py-1" title="Editar produto"><i class="fa fa-edit"></i></button>';
									echo '		</div>';
									echo '	</form>';
									echo '</td>';
									echo '</tr>';
								}
								?>
							</tbody>

						</table>
					</div>
				<?php else: ?>
					<?php  insereToastr("danger", "Nenhum dado informado para pesquisa!"); ?>
				<?php endif ?>

			</div>
		</main>
	</div><!-- row -->
</div><!-- container-fluid -->

