<div class="container-fluid mt-5 text-sm">
	<div class="row">
		<?php 
	    require_once('pages/ecommerce/sidebar.php'); 		
		?>
		<main class="col ms-sm-auto px-3">
			<?php 
			require_once("pages/ecommerce/bagy_controller.php");
			require_once("pages/ecommerce/bagy_api_produto.php");
			require_once("pages/ecommerce/bagy_api_categoria.php");
			require_once("pages/ecommerce/bagy_produto_modal_cadastrar.php");
			?>

			<div class="row">
				<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
					<h1 class="h2">
						<i class="fa-solid fa-bags-shopping"></i> Consulta Produtos - Bagy
					</h1>
					<div class="btn-group"> 
						<button type="button" class="btn btn-xs btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#bagy_produto_modal_cadastrar" title="Cadastrar Produtos na Tray"><i class="fa-solid fa-plus"></i> Cadastrar</button>
						<a href="index.php?op=161&acao=limparLista&aba=bagy" class="btn btn-xs btn-outline-secondary" title="Limpar lista"><i class="fa-solid fa-broom"></i></a>
					</div>
				</div>
			</div>

			<div class="card">
				<div class="card-header">
					<form role="form" method="POST" action="index.php" enctype="multipart/form-data"class=" row g-3">
						<input type="hidden" name="op" value="161">
						<input type="hidden" name="aba" value="bagy">

					<div class="col">
						<label class="form-label">PEÇA</label>
						<input type="text" name="name" placeholder="PEÇA" class="form-control" autocomplete="off">
					</div>

					<div class="col">
						<label class="form-label">ID Bagy</label>
						<input type="text" name="product_id" placeholder="Código Bagy" class="form-control" autocomplete="off">
					</div>
					
					<div class="col">
						<label class="form-label">Categoria</label>
            <select name="category_id" id="category_id" class="form-select select2">
            	<option value="all">-- Todas --</option>
            	<?php  
            	if ($categorias = bagy_categoria_listar()) {
            		foreach ($categorias as $key => $value) {
            			echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
            		}
            	}
            	?>
            </select>
					</div>

					<div class="col-2">
						<div class="d-grid gap-2">
							<button type="submit" name="acao" value="bagy_produto_pesquisa" class="btn btn-secondary mt-4"><i class="fa fa-solid fa-search"></i> Pesquisar</button>
						</div>
					</div>

					</form>
				</div>

				<?php if (isset($listaProduto) && !empty($listaProduto)): ?>
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
								foreach ($listaProduto as $key => $value) {
									if (isset($value["Product"]['id']) && !empty($value["Product"]['id'])) {
										if ($product = Bagy_consultaProdutoID($value["Product"]['id']) ){
											echo '<tr style="vertical-align: middle;">';
											echo '<td>'.$product['id'].'</td>';
											echo '<td>'.$product['name'].'</td>';
											echo '<td>'.$product["category_name"].'</td>';
											echo '<td style="text-align: center">R$ '.moeda($product['price']).'</td>';
											echo '<td>'.formataDataBagyToBR($product['modified']).'</td>';
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
											echo '			<button type="submit" name="acao" value="Bagy_editarProduto" class="btn btn-primary btn-sm py-1" title="Editar produto"><i class="fa fa-edit"></i></button>';
											echo '			<button type="submit" name="acao" value="Bagy_confirmaExcluirProduto" class="btn btn-danger btn-sm py-1" title="Editar produto"><i class="fa fa-trash"></i></button>';
											echo '		</div>';
											echo '	</form>';
											echo '</td>';
											echo '</tr>';
										}
									}
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

