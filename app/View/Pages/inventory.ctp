<style type="text/css">
	#inventory {
		position: relative;
		min-height: 120px;
	}

	#inventory tr td a {
		margin-right: 5px;
	}

	#inventory tr td a.disabled,
	#inventory tr td a.disabled:hover,
	#inventory tr td a[data-disabled="true"],
	#inventory tr td a[data-disabled="true"]:hover {
		color: gray;
	}

	form .form-group {
		margin-right: 5px;
		vertical-align: top !important;
	}

	form .form-group p {
		width: 172px;
	}

	#add-stock-form,
	#new-price-form {
		background: 0;
		margin: 0;
		padding: 0;
		border: none;
	}

	#add-stock-form input[type="text"],
	#new-price-form input[type="text"] {
		display: inline;
		width: 106px;
	}

	#add-stock-form input,
	#new-price-form input {
		margin-right: 3px;
	}

	#add-stock-form .form-group,
	#new-price-form .form-group {
		margin-bottom: 5px;
	}

	#new-price-form .form-group {
		float: left;
		margin-right: 25px;
	}

	#add-stock-form .form-group p,
	#new-price-form .form-group p {
		width: 120px;
	}
</style>

<h1>Inventory</h1>
<form id="product-search-form" class="form-inline">
	<h4 class="pull-left">Search</h4>
	<?php
		$inputs = array(
			'id' => array('label-class' => 'sr-only', 'name' => 'Product ID', 'input-type' => 'text', 'attributes' => array('data-validate' => 'number')),
			'name' => array('label-class' => 'sr-only', 'name' => 'Product Name', 'input-type' => 'text', 'attributes' => array()),
			'available_count' => array('label-class' => 'sr-only', 'name' => 'Stock Availability', 'input-type' => 'select', 'attributes' => array(), 'options' => array('' => 'Stock Availability', 1 => 'With Available Stocks', 2 => 'Empty Stocks'))
		);
		echo $this->element('inline_form', array('inputs' => $inputs, 'params' => (isset($params) ? $params : array()), 'errors' => (isset($errors) ? $errors : array())));
	?>
	<button class="btn btn-primary btn-sm">Search</button>
</form>
<div id="inventory"></div>
<form id="add-product-form" class="form-inline">
	<fieldset>
		<h4 class="pull-left">Add Product</h4>
		<?php
			$inputs = array(
				'name' => array('label-class' => 'sr-only', 'name' => 'Product Name', 'input-type' => 'text', 'attributes' => array('data-validate' => 'required')),
				'purchase_price' => array('label-class' => 'sr-only', 'name' => 'Original Price', 'input-type' => 'text', 'attributes' => array('data-validate' => 'required|money')),
				'selling_price' => array('label-class' => 'sr-only', 'name' => 'Selling Price', 'input-type' => 'text', 'attributes' => array('data-validate' => 'required|money')),
				'available_count' => array('label-class' => 'sr-only', 'name' => 'Stock Count', 'input-type' => 'text', 'attributes' => array('data-validate' => 'required|number')),
				'notes' => array('label-class' => 'sr-only', 'name' => 'Notes', 'input-type' => 'text', 'attributes' => array())
			);
			echo $this->element('inline_form', array('inputs' => $inputs, 'params' => (isset($params) ? $params : array()), 'errors' => (isset($errors) ? $errors : array())));
		?>
		<button class="btn btn-primary btn-sm" data-loading-text="Adding...">Add Product</button>
	</fieldset>
</form>

<script type="text/javascript">
	validate.add_rule(
		'number', function(e) {
			return ((/^[0-9]*$/).test(e.val()) | e.val().length == 0);
		}, "This field must be a whole number."
	);
	validate.add_rule(
		'money', function(e) {
			return ((/^[1-9][0-9]*(|.[0-9]*)$/).test(e.val()) | e.val().length == 0);
		}, "This field must be a number."
	);
	validate.add_rule(
		'gt-old', function(e) {
			console.log(e.data('old-stock'));
			return (!(/^[0-9]*$/).test(e.val()) | e.val().length == 0 | e.val() > e.data('old-stock'));
		}, "New stock must be greater than old stock."
	);

	$(function() {
		var filter_form = $('#product-search-form');
		filter_form.validate();
		var add_form = $('#add-product-form');
		add_form.validate();
		get_inventory();
		var filters;
		var old_add_stock_html;
		var old_content;

		$('body').on('submit', '#product-search-form', function() {
			get_inventory();
			return false;
		})

		$('body').on('click', '.ajax-pagination a', function() {
			page_url = $(this).attr('href');
			if(page_url != null) {
				var params = {};
				var url_filters = page_url.split('/');
				$.each(url_filters, function (key, value) {
					if(value.indexOf(':') > -1) {
						param_parts = value.split(':');
						params[param_parts[0]] = param_parts[1];
					}
				});
				$.each(filters, function (key, value) {
					if(value != '')
						params[key] = value;
				});
				$.ajax({
					url: '<?php echo $this->webroot; ?>pages/ajax_get_inventory',
					type: 'POST',
					data: params,
					beforeSend: function() {
						$('#inventory').append(ajax_loader);
					},
					success: function (result) {
						$('#inventory').html(result);
					}
				});
			}
			return false;
		});

		$('body').on('submit', '#add-product-form', function() {
			var params = {};
			var inputs = add_form.serializeArray();
			$.each(inputs, function (i, input) {
				params[input.name] = input.value;
			});
			params['total_count'] = params['available_count'];
			var t = $(this);
			$.ajax({
				url: '<?php echo $this->webroot; ?>pages/ajax_add_product',
				type: 'POST',
				data: params,
				beforeSend: function() {
					t.find('button').button('loading');
					t.find('fieldset').attr('disabled', 'disabled');
				},
				success: function (result) {
					if(result == 'true') {
						t[0].reset();
						get_inventory();
					}
					else {
						validate.set_error(add_form.find('[name="name"]'), '<p>Product already exist.</p>');
					}
					t.find('button').button('reset');
					t.find('fieldset').removeAttr('disabled');
				}
			});
			return false;
		});

		function get_inventory() {
			var params = {};
			var inputs = filter_form.serializeArray();
			$.each(inputs, function (i, input) {
				params[input.name] = input.value;
			});
			filters = params;
			$.ajax({
				url: '<?php echo $this->webroot; ?>pages/ajax_get_inventory',
				type: 'POST',
				data: params,
				beforeSend: function() {
					$('#inventory').append(ajax_loader);
				},
				success: function (result) {
					$('#inventory').html(result);
				}
			});
		}

		$('body').on('click', '[data-delete]:not(.disabled):not([data-disabled="true"])', function() {
			var id = $(this).data('delete');
			if(confirm('Are you sure you want to delete this product?')) {
				$.ajax({
					url: '<?php echo $this->webroot; ?>pages/ajax_delete_product',
					type: 'POST',
					data: {id: id},
					beforeSend: function() {
						$('#inventory').append(ajax_loader);
					},
					success: function (result) {
						result = JSON.parse(result);
						$('.ajax-loader').remove();
						if(result == 'Success') {
							get_inventory();
							show_alerts({alerts: get_alert('success', 'Product deleted.')});
						}
						else if(result == 'Sold') {
							show_alerts({alerts: get_alert('error', 'Products already sold cannot be deleted.')});
						}
						else if(result == 'Invalid') {
							show_alerts({alerts: get_alert('error', 'Invalid product id.')});
						}
						else {
							console.log(result);
						}
					}
				});
			}
		});

		$('body').on('click', '[data-add-stock]:not(.disabled)', function() {
			$('[data-add-stock]').addClass('disabled');
			$('[data-delete]').addClass('disabled');
			$('[data-new-price]').addClass('disabled');
			if(!$('#add-stock-form').length) {
				var id = $(this).data('add-stock');
				var e = $(this).closest('td').siblings('.add-stock');
				var old_price = old_add_stock_html = e.text();
				var form = '<form id="add-stock-form" data-id="' + id + '"><fieldset><div class="form-group"><input type="text" class="form-control input-sm" name="available_count" placeholder="New Stock" data-validate="required|number|gt-old" data-old-stock="' + old_price + '" value="' + old_price + '"></div><input type="submit" class="btn btn-primary btn-sm" value="Add" data-loading-text="Adding..."><button class="btn btn-primary btn-sm">Cancel</button></fieldset></form>';
				e.html(form);
				$('#add-stock-form').validate();
			}
			else {
				show_alerts({alerts: get_alert('error', 'Add stock form is currently being used.')});
			}
		});

		$('body').on('submit', '#add-stock-form', function() {
			var t = $(this);
			var params = {};
			var inputs = t.serializeArray();
			$.each(inputs, function (i, input) {
				params[input.name] = input.value;
			});
			params['count'] = params['available_count'] - parseInt(t.find('input[type="text"]').data('old-stock'))
			params['total_count'] = parseInt(t.closest('td').siblings('.total-count').text()) + params['count'];
			params['id'] = t.data('id');
			$.ajax({
				url: '<?php echo $this->webroot; ?>pages/ajax_add_stock',
				type: 'POST',
				data: params,
				beforeSend: function() {
					t.find('input[type="submit"]').button('loading');
					t.find('fieldset').attr('disabled');
				},
				success: function (result) {
					result = JSON.parse(result);
					if(result == 'Success') {
						t.closest('.add-stock').siblings('.total-count').html(params['total_count']).siblings('td').find('a[data-new-price]').data('disabled', true).attr('data-disabled', true);
						t.closest('.add-stock').html(params['available_count']);
						show_alerts({alerts: get_alert('success', 'Stock successfully added.')});
					}
					else {
						t.closest('.add-stock').html(old_add_stock_html);
						show_alerts({alerts: get_alert('error', 'Stock haven\'t added.')});
						console.log(result);
					}
					$('[data-add-stock]').removeClass('disabled');
					$('[data-delete]').removeClass('disabled');
					$('[data-new-price]').removeClass('disabled');
				}
			});
			return false;
		});

		$('body').on('click', '#add-stock-form button', function() {
			$('[data-add-stock]').removeClass('disabled');
			$('[data-delete]').removeClass('disabled');
			$('[data-new-price]').removeClass('disabled');
			$(this).closest('.add-stock').html(old_add_stock_html);
			return false;
		});

		$('body').on('click', '[data-new-price]:not(.disabled):not([data-disabled="true"])', function() {
			$('[data-add-stock]').addClass('disabled');
			$('[data-delete]').addClass('disabled');
			$('[data-new-price]').addClass('disabled');
			var id = $(this).data('new-price');
			var purchase_price_content = $(this).closest('tr').find('td:nth-child(3)');
			var selling_price_content = $(this).closest('tr').find('td:nth-child(4)');
			var available_count_content = $(this).closest('tr').find('td:nth-child(5)');
			old_content = purchase_price_content[0].outerHTML + selling_price_content[0].outerHTML + available_count_content[0].outerHTML;
			var form =
				'<td colspan="3" style="width: 384px;">\
					<form id="new-price-form" data-id="' + id + '">\
						<fieldset>\
							<div class="form-group">\
								<input type="text" class="form-control input-sm" name="purchase_price" placeholder="Original Price" data-validate="required|money" data-old-price="' + purchase_price_content.text() + '" value="' + purchase_price_content.text() + '">\
							</div>\
							<div class="form-group">\
								<input type="text" class="form-control input-sm" name="selling_price" placeholder="Selling Price" data-validate="required|money" data-old-price="' + selling_price_content.text() + '" value="' + selling_price_content.text() + '">\
							</div>\
							<div class="form-group">\
								<input type="text" class="form-control input-sm" name="available_count" placeholder="Stock Count" data-validate="required|number">\
							</div>\
							<input type="submit" class="btn btn-primary btn-sm" value="Save" data-loading-text="Saving...">\
							<button class="btn btn-primary btn-sm">Cancel</button>\
						</fieldset>\
					</form>\
				</td>';
			purchase_price_content.replaceWith(form);
			selling_price_content.remove();
			available_count_content.remove();
			$('#new-price-form').validate();
		});

		$('body').on('submit', '#new-price-form', function() {
			var t = $(this);
			var params = {};
			var inputs = t.serializeArray();
			$.each(inputs, function (i, input) {
				params[input.name] = input.value;
			});
			params['product_id'] = t.data('id');
			params['old_purchase_price'] = t.find('input[name="purchase_price"]').data('old-price');
			params['old_selling_price'] = t.find('input[name="selling_price"]').data('old-price');
			$.ajax({
				url: '<?php echo $this->webroot; ?>pages/ajax_add_new_price',
				type: 'POST',
				data: params,
				beforeSend: function() {
					t.find('input[type="submit"]').button('loading');
					t.find('fieldset').attr('disabled');
				},
				success: function (result) {
					result = JSON.parse(result);
					if(result['success']) {
						var td = t.closest('td');
						var tr = t.closest('tr');
						td.next('td').text(0).next('td').text(params['available_count']).next('td').text(result['price_date']).next('td').find('a[data-new-price]').data('disabled', true).attr('data-disabled', true);
						td.replaceWith(old_content);
						tr.find('.add-stock').text(params['available_count']).prev('td').text(params['selling_price']).prev('td').text(params['purchase_price']);
						show_alerts({alerts: get_alert('success', 'New price saved.')});
					}
					else {
						t.closest('td').replaceWith(old_content);
						show_alerts({alerts: get_alert('error', 'Price haven\'t saved.')});
						console.log(result);
					}
					$('[data-add-stock]').removeClass('disabled');
					$('[data-delete]').removeClass('disabled');
					$('[data-new-price]').removeClass('disabled');
				}
			});
			return false;
		});

		$('body').on('click', '#new-price-form button', function() {
			$('[data-add-stock]').removeClass('disabled');
			$('[data-delete]').removeClass('disabled');
			$('[data-new-price]').removeClass('disabled');
			$(this).closest('td').replaceWith(old_content);
			return false;
		});
	});
</script>
