<?php $paginator = $this->Paginator; ?>
<?php echo $this->element('paging_links', array('class' => 'pull-right')); ?>
<table class="table table-striped table-hover">
	<tr class="ajax-pagination">
		<th><?php echo $paginator->sort('Product.id', 'Product ID', array('url' => array('page' => $page))); ?></th>
		<th><?php echo $paginator->sort('Product.name', 'Product Name', array('url' => array('page' => $page))); ?></th>
		<th><?php echo $paginator->sort('CurrentStock.purchase_price', 'Original Price', array('url' => array('page' => $page))); ?></th>
		<th><?php echo $paginator->sort('CurrentStock.selling_price', 'Selling Price', array('url' => array('page' => $page))); ?></th>
		<th><?php echo $paginator->sort('CurrentStock.available_count', 'Available Stocks', array('url' => array('page' => $page))); ?></th>
		<th><?php echo $paginator->sort('CurrentStock.sold_count', 'Sold Stocks', array('url' => array('page' => $page))); ?></th>
		<th><?php echo $paginator->sort('CurrentStock.total_count', 'Total Stocks', array('url' => array('page' => $page))); ?></th>
		<th><?php echo $paginator->sort('CurrentStock.price_date', 'Last Price Update', array('url' => array('page' => $page))); ?></th>
		<th>Actions</th>
	</tr>
<?php if($products): ?>
	<?php
		foreach ($products as $product) {
			echo "<tr>";
			echo "<td>".$product['Product']['id']."</td>";
			echo "<td>".$product['Product']['name']."</td>";
			echo "<td>".$product['CurrentStock']['purchase_price']."</td>";
			echo "<td>".$product['CurrentStock']['selling_price']."</td>";
			echo "<td>".$product['CurrentStock']['available_count']."</td>";
			echo "<td>".$product['CurrentStock']['sold_count']."</td>";
			echo "<td>".$product['CurrentStock']['total_count']."</td>";
			echo "<td>".$product['CurrentStock']['price_date']."</td>";
			echo "<td>Actions</td>";
			echo "</tr>";
		}
	?>
<?php else: ?>
	<tr class="danger"><td colspan="9">There are no records found.</td></tr>
<?php endif; ?>
</table>