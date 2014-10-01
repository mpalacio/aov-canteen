<?php $paginator = $this->Paginator; ?>
<?php echo $this->element('paging_links', array('class' => 'pull-right')); ?>
<table class="table table-striped table-hover">
	<tr class="ajax-pagination">
		<th><?php echo $paginator->sort('Transaction.id', 'ID', array('url' => array('page' => $page))); ?></th>
		<th><?php echo $paginator->sort('Product.name', 'Product', array('url' => array('page' => $page))); ?></th>
		<th><?php echo $paginator->sort('Transaction.type', 'Trans. Type', array('url' => array('page' => $page))); ?></th>
		<th><?php echo $paginator->sort('Transaction.count', 'Count', array('url' => array('page' => $page))); ?></th>
		<th>Total Stocks</th>
		<th><?php echo $paginator->sort('Transaction.date', 'Trans. Date', array('url' => array('page' => $page))); ?></th>
		<th><?php echo $paginator->sort('Stock.purchase_price', 'Purchase Price', array('url' => array('page' => $page))); ?></th>
		<th><?php echo $paginator->sort('Stock.selling_price', 'Selling Price', array('url' => array('page' => $page))); ?></th>
		<th><?php echo $paginator->sort('Stock.price_date', 'Price Date', array('url' => array('page' => $page))); ?></th>
	</tr>
<?php if($transactions): ?>
	<?php
		foreach ($transactions as $transaction) {
			$current_total = 0;
			foreach ($transaction['Stock']['Transaction'] as $history) {
				if($history['id'] <= $transaction['Transaction']['id']) {
					$type = $history['type'];
					if($type == 'new_stock' | $type == 'add_stock')
						$current_total += $history['count'];
					elseif ($type == 'add_stock')
						$current_total -= $history['count'];
				}
			}
			echo "<tr>";
			echo "<td>".$transaction['Transaction']['id']."</td>";
			echo "<td>".$transaction['Stock']['Product']['name']."</td>";
			echo "<td>".$transaction['Transaction']['type']."</td>";
			echo "<td>".$transaction['Transaction']['count']."</td>";
			echo "<td>".$current_total."</td>";
			echo "<td>".$transaction['Transaction']['date']."</td>";
			echo "<td>".$transaction['Stock']['purchase_price']."</td>";
			echo "<td>".$transaction['Stock']['selling_price']."</td>";
			echo "<td>".$transaction['Stock']['price_date']."</td>";
			echo "</tr>";
		}
	?>
<?php else: ?>
	<tr class="danger"><td colspan="9">There are no records found.</td></tr>
<?php endif; ?>
</table>