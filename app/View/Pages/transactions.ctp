<style type="text/css">
	#transaction-container {
		position: relative;
	}

	#transaction {
		position: relative;
		min-height: 120px;
	}

	#transaction tr td:nth-child(1),
	#transaction tr td:nth-child(3),
	#transaction tr td:nth-child(5),
	#transaction tr td:nth-child(6),
	#transaction tr td:nth-child(7),
	#transaction tr td:nth-child(11),
	#transaction tr td:nth-child(8) {
		width: 70px;
	}

	form .form-group {
		margin-right: 5px;
		vertical-align: top !important;
	}

	form .form-group p {
		width: 172px;
	}
</style>

<h1>Transaction</h1>
<div id="transaction-container">
	<form id="transaction-search-form" class="form-inline">
		<h4 class="pull-left">Search</h4>
		<?php
			$inputs = array(
				'name' => array('label-class' => 'sr-only', 'name' => 'Product Name', 'input-type' => 'text', 'attributes' => array()),
				'type' => array('label-class' => 'sr-only', 'name' => 'Transaction Type', 'input-type' => 'select', 'attributes' => array(), 'options' => array('' => 'Transaction Type', 'new_stock' => 'New Stock', 'add_stock' => 'Add to Stocks', 'purchase_product' => 'Product Purchase'))
			);
			echo $this->element('inline_form', array('inputs' => $inputs, 'params' => (isset($params) ? $params : array()), 'errors' => (isset($errors) ? $errors : array())));
		?>
		<button class="btn btn-primary btn-sm">Search</button>
	</form>
	<div id="transaction"></div>
</div>

<script type="text/javascript">
	$(function() {
		var transaction_container = $('#transaction-container');
		var filter_form = $('#transaction-search-form');
		filter_form.validate();
		get_transaction({});
		var filters = {};

		$('body').on('submit', '#transaction-search-form', function() {
			var params = {};
			var inputs = $('#transaction-search-form').serializeArray();
			$.each(inputs, function (i, input) {
				params[input.name] = input.value;
			});
			filters = params;
			get_transaction(params);
			return false;
		});

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
				get_transaction(params);
			}
			return false;
		});

		function get_transaction(params) {
			$.ajax({
				url: '<?php echo $this->webroot; ?>pages/ajax_get_transactions',
				type: 'POST',
				data: params,
				beforeSend: function() {
					$('#transaction').append(ajax_loader);
				},
				success: function (result) {
					$('#transaction').html(result);
				}
			});
		}
	});
</script>
