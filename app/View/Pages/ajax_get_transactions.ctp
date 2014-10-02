<?php $paginator = $this->Paginator; ?>
<?php echo $this->element('paging_links', array('class' => 'pull-right')); ?>
<table class="table table-striped table-hover">
	<tr class="ajax-pagination">
		<th title="Product ID"><?php echo $paginator->sort('Product.id', 'PID', array('url' => array('page' => $page))); ?></th>
		<th title="Product Name"><?php echo $paginator->sort('Product.name', 'Name', array('url' => array('page' => $page))); ?></th>
		<th title="Stock ID"><?php echo $paginator->sort('Stock.id', 'SID', array('url' => array('page' => $page))); ?></th>
		<th title="Stock Price Date"><?php echo $paginator->sort('Stock.price_date', 'SPDate', array('url' => array('page' => $page))); ?></th>
		<th title="Stock Quantity">SQty</th>
		<th title="Purchase Price"><?php echo $paginator->sort('Stock.purchase_price', 'PP', array('url' => array('page' => $page))); ?></th>
		<th title="Selling Price"><?php echo $paginator->sort('Stock.selling_price', 'SP', array('url' => array('page' => $page))); ?></th>
		<th title="Transaction ID"><?php echo $paginator->sort('Transaction.id', 'TID', array('url' => array('page' => $page))); ?></th>
		<th title="Transaction Type"><?php echo $paginator->sort('Transaction.type', 'TType', array('url' => array('page' => $page))); ?></th>
		<th title="Date of Transaction"><?php echo $paginator->sort('Transaction.date', 'TDate', array('url' => array('page' => $page))); ?></th>
		<th title="Transaction Quantity"><?php echo $paginator->sort('Transaction.count', 'TQty', array('url' => array('page' => $page))); ?></th>
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
					elseif ($type == 'purchase_product')
						$current_total -= $history['count'];
				}
			}
			echo "<tr>";
			echo "<td>".$transaction['Stock']['Product']['id']."</td>";
			echo "<td>".$transaction['Stock']['Product']['name']."</td>";
			echo "<td>".$transaction['Stock']['id']."</td>";
			echo "<td>".$transaction['Stock']['price_date']."</td>";
			echo "<td>".$current_total."</td>";
			echo "<td>".$transaction['Stock']['purchase_price']."</td>";
			echo "<td>".$transaction['Stock']['selling_price']."</td>";
			echo "<td>".$transaction['Transaction']['id']."</td>";
			echo "<td>".$transaction['Transaction']['type']."</td>";
			echo "<td>".$transaction['Transaction']['date']."</td>";
			echo "<td>".$transaction['Transaction']['count']."</td>";
			echo "</tr>";
		}
	?>
<?php else: ?>
	<tr class="danger"><td colspan="9">There are no records found.</td></tr>
<?php endif; ?>
</table>