<style type="text/css">
	.ui-widget {
		font-family: inherit;
		font-size: 9pt;
	}

	form .form-group {
		margin-right: 5px;
		vertical-align: top !important;
	}

	form .form-group p {
		width: 172px;
	}
</style>

<h1>POS</h1>
<form id="add-purchase-form" class="form-inline">
	<fieldset>
		<h4 class="pull-left">Purchase</h4>
		<?php
			$inputs = array(
				'name' => array('label-class' => 'sr-only', 'name' => 'Product Name', 'input-type' => 'text', 'attributes' => array('data-validate' => 'required')),
				'count' => array('label-class' => 'sr-only', 'name' => 'Count', 'input-type' => 'text', 'attributes' => array('data-validate' => 'required'))
			);
			echo $this->element('inline_form', array('inputs' => $inputs, 'params' => (isset($params) ? $params : array()), 'errors' => (isset($errors) ? $errors : array())));
		?>
		<button class="btn btn-primary btn-sm" data-loading-text="Adding">Add</button>
	</fieldset>
</form>
<table id="purchases" class="table table-striped table-hover">
	<tbody>
		<tr>
			<th>ID</th>
			<th>Product</a></th>
			<th>Qty</a></th>
			<th>Price</a></th>
			<th>Total</a></th>
			<th>Actions</th>
		</tr>
	</tbody>
</table>
<button id="process" class="btn btn-primary btn-sm pull-right" data-loading-text="Processing">Process</button>

<script type="text/javascript">
	$(function() {
		var add_purchase_form = $('#add-purchase-form');
		add_purchase_form.validate();
		var products = {};

		$.ajax({
			url: '<?php echo $this->webroot; ?>pages/ajax_get_products',
			type: 'POST',
			beforeSend: function () {
				$('#add-purchase-form').find('fieldset').attr('disabled', 'disabled');
			},
			success: function (result) {
				$('#add-purchase-form').find('fieldset').removeAttr('disabled');
				$('#name').autocomplete({
					source: JSON.parse(result)
				});
			}
		});

		$('body').on('submit', '#add-purchase-form', function() {
			var params = {};
			var inputs = $('#add-purchase-form').serializeArray();
			$.each(inputs, function (i, input) {
				params[input.name] = input.value;
			});
			var t = $(this);
			$.ajax({
				url: '<?php echo $this->webroot; ?>pages/ajax_get_product',
				type: 'POST',
				data: params,
				beforeSend: function() {
					t.find('button').button('loading');
					t.find('fieldset').attr('disabled', 'disabled');
				},
				success: function (result) {
					result = JSON.parse(result);
					if(result != 'not found') {
						if(isset(products[result.Product.id])) {
							var temp_count = parseInt(products[result.Product.id].count) + parseInt(params.count);
							if(temp_count <= parseInt(result.CurrentStock.available_count)) {
								products[result.Product.id].count = temp_count;
								products[result.Product.id].total += (params.count * result.CurrentStock.selling_price);
								$('[data-id="' + result.Product.id + '"]').find('.count').text(products[result.Product.id].count);
								$('[data-id="' + result.Product.id + '"]').find('.total').text(products[result.Product.id].total);
								t[0].reset();
							}
							else {
								show_alerts({alerts: get_alert('error', 'No more available stocks.')});
							}
						}
						else {
							if(params.count <= parseInt(result.CurrentStock.available_count)) {
								var product = products[result.Product.id] = {
									name: result.Product.name,
									price: result.CurrentStock.selling_price,
									count: params.count,
									total: (result.CurrentStock.selling_price * params.count),
									stock_id: result.CurrentStock.id,
									available_count: parseInt(result.CurrentStock.available_count),
									total_count: parseInt(result.CurrentStock.total_count),
									sold_count: parseInt(result.CurrentStock.sold_count)
								};
								var field = '<tr data-id="' + result.Product.id + '"><td>' + result.Product.id + '</td><td>' + product.name + '</td><td class="count">' + product.count + '</td><td>' + product.price + '</td><td class="total">' + product.total + '</td><td><a data-delete=' + result.Product.id + ' title="Remove Purchase"><i class="glyphicon glyphicon-remove"></i></a></td></tr>';
								$('#purchases tbody').append(field);
								t[0].reset();
							}
							else {
								show_alerts({alerts: get_alert('error', 'No more available stocks.')});
							}
						}
					}
					else {
						show_alerts({alerts: get_alert('error', 'Invalid product.')});
					}
					t.find('button').button('reset');
					t.find('fieldset').removeAttr('disabled');
				}
			});
			return false;
		});

		$('body').on('click', '[data-delete]', function() {
			var id = $(this).data('delete');
			delete products[id];
			$(this).closest('tr').remove();
		});

		$('body').on('click', '#process', function() {
			var t = $(this);
			$.ajax({
				url: '<?php echo $this->webroot; ?>pages/ajax_process_sales',
				type: 'POST',
				data: products,
				beforeSend: function () {
					t.button('loading');
				},
				success: function (result) {
					t.button('reset');
					$('[data-id]').remove();
					products = {};
					show_alerts({alerts: get_alert('success', 'Purchases are successfully processed.')});
				}
			});
		});
	});
</script>