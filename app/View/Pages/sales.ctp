<h1>Sales</h1>
<?php $nc = $capital = 3000; ?>
<h4>Capital: <b>Php <?php echo "$capital"; ?></b></h4>
<table class="table table-striped table-hover">
	<tr class="ajax-pagination">
		<th title="Product ID">PID</th>
		<th title="Product Name">Name</th>
		<th title="Stock ID">SID</th>
		<th title="Stock Price Date">SPDate</th>
		<th title="Stock Quantity">SQty</th>
		<th title="Purchase Price">PP</th>
		<th title="Selling Price">SP</th>
		<th title="Transaction ID">TID</th>
		<th title="Transaction Type">TType</th>
		<th title="Date of Transaction">TDate</th>
		<th title="Transaction Quantity">TQty</th>
		<th title="Cost">Cost</th>
		<th title="Sales">Sales</th>
		<th title="Revenue">Revenue</th>
		<th title="Estimated Total Sales">ETS</th>
		<th title="Estimated Total Revenue">ETR</th>
		<th title="Net Cash">NC</th>
		<th title="Net Revenue">NR</th>
	</tr>
<?php if($transactions): ?>
	<?php
		$nr = 0;
		foreach ($transactions as $transaction) {
			$stock_qty = 0;
			foreach ($transaction['Stock']['Transaction'] as $history) {
				if($history['id'] <= $transaction['Transaction']['id']) {
					$type = $history['type'];
					if($type == 'new_stock' | $type == 'add_stock')
						$stock_qty += $history['count'];
					elseif ($type == 'purchase_product')
						$stock_qty -= $history['count'];
				}
			}

			$cost = $transaction['Transaction']['type'] == 'purchase_product' ? ($transaction['Transaction']['count'] * $transaction['Stock']['purchase_price']) : ($transaction['Transaction']['count'] * $transaction['Stock']['purchase_price'] * (-1));
			$sales = $transaction['Transaction']['type'] == 'purchase_product' ? ($transaction['Transaction']['count'] * $transaction['Stock']['selling_price']) : $cost;
			$revenue = $sales - $cost;
			$ets = $stock_qty * $transaction['Stock']['selling_price'];
			$etr = $ets - $stock_qty * $transaction['Stock']['purchase_price'];
			$nc += $sales;
			$nr = $nc - $capital;

			echo "<tr>";
			echo "<td>".$transaction['Stock']['Product']['id']."</td>";
			echo "<td>".$transaction['Stock']['Product']['name']."</td>";
			echo "<td>".$transaction['Stock']['id']."</td>";
			echo "<td>".date('Y-m-d', strtotime($transaction['Stock']['price_date']))."</td>";
			echo "<td>".$stock_qty."</td>";
			echo "<td>".$transaction['Stock']['purchase_price']."</td>";
			echo "<td>".$transaction['Stock']['selling_price']."</td>";
			echo "<td>".$transaction['Transaction']['id']."</td>";
			echo "<td>".$transaction['Transaction']['type']."</td>";
			echo "<td>".date('Y-m-d', strtotime($transaction['Transaction']['date']))."</td>";
			echo "<td>".$transaction['Transaction']['count']."</td>";
			echo "<td>".abs($cost)."</td>";
			echo "<td>".($sales < 0 ? 0 : $sales)."</td>";
			echo "<td>".$revenue."</td>";
			echo "<td>".$ets."</td>";
			echo "<td>".$etr."</td>";
			echo "<td>".$nc."</td>";
			echo "<td>".$nr."</td>";
			echo "</tr>";
		}
	?>
<?php else: ?>
	<tr class="danger"><td colspan="18">There are no records found.</td></tr>
<?php endif; ?>
</table>
<h4>Net Cash: <b>Php <?php echo "$nc"; ?></b></h4>
<h4>Net Revenue: <b>Php <?php echo "$nr"; ?></b></h4>