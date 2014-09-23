<style type="text/css">
	#inventory {
		position: relative;
	}

	form .form-group {
		margin-right: 5px;
		vertical-align: top !important;
	}

	form .form-group p {
		width: 172px;
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
			return ((/^[1-9][0-9]*$/).test(e.val()) | e.val().length == 0);
		}, "This field must be a whole number."
	);
	validate.add_rule(
		'money', function(e) {
			return ((/^[1-9][0-9]*(|.[0-9]*)$/).test(e.val()) | e.val().length == 0);
		}, "This field must be a number."
	);

	$(function() {
		var filter_form = $('#product-search-form');
		filter_form.validate();
		var add_form = $('#add-product-form');
		add_form.validate(false);
		get_inventory();
		var filters;

		$('body').on('submit', '#product-search-form', function() {
			get_inventory();
			return false;
		})

		$('body').on('click', '.ajax-pagination a', function() {
			$this = $(this);
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
			var t = $(this);
			$.ajax({
				url: '<?php echo $this->webroot; ?>pages/ajax_add_product',
				type: 'POST',
				data: params,
				beforeSend: function() {
					t.find('button').button('loading');
					t.find('fieldset').attr('disabled');
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
	});
</script>
