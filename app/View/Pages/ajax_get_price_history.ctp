<h4>Price History for <?php echo $params['name']; ?><button class="btn btn-primary btn-sm pull-right" id="close-price-history">Close</button></h4>
<table class="table table-striped table-hover">
	<tr class="ajax-pagination">
		<th>Purchase Price</th>
		<th>Selling Price</th>
		<th>Available Stocks</th>
		<th>Sold Stocks</th>
		<th>Total Stocks</th>
		<th>Price Date</th>
	</tr>
<?php if($stocks): ?>
	<?php
		foreach ($stocks as $stock) {
			echo "<tr>";
			echo "<td>".$stock['Stock']['purchase_price']."</td>";
			echo "<td>".$stock['Stock']['selling_price']."</td>";
			echo "<td class='add-stock'>".$stock['Stock']['available_count']."</td>";
			echo "<td>".$stock['Stock']['sold_count']."</td>";
			echo "<td class='total-count'>".$stock['Stock']['total_count']."</td>";
			echo "<td>".$stock['Stock']['price_date']."</td>";
			echo "</tr>";
		}
	?>
<?php else: ?>
	<tr class="danger"><td colspan="6">There are no records found.</td></tr>
<?php endif; ?>
</table>