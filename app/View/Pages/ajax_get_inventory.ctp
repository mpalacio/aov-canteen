<?php $paginator = $this->Paginator; ?>
<?php echo $this->element('paging_links', array('class' => 'pull-right')); ?>
<table class="table table-striped table-hover">
	<tr class="ajax-pagination">
		<th><?php echo $paginator->sort('Product.id', 'ID', array('url' => array('page' => $page))); ?></th>
		<th><?php echo $paginator->sort('Product.name', 'Product', array('url' => array('page' => $page))); ?></th>
		<th><?php echo $paginator->sort('CurrentStock.purchase_price', 'Purchase Price', array('url' => array('page' => $page))); ?></th>
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
			echo "<td class='add-stock'>".$product['CurrentStock']['available_count']."</td>";
			echo "<td>".$product['CurrentStock']['sold_count']."</td>";
			echo "<td class='total-count'>".$product['CurrentStock']['total_count']."</td>";
			echo "<td>".$product['CurrentStock']['price_date']."</td>";
			echo "<td>
				<a data-add-stock='{$product['CurrentStock']['id']}' data-product-id='{$product['Product']['id']}' title='Add Stocks'><i class='glyphicon glyphicon-plus'></i></a>
				<a data-delete='{$product['Product']['id']}' title='Delete Product' data-disabled='".($product['CurrentStock']['sold_count'] > 0 | $product[0]['total_sold'] > 0 ? "true" : "false")."'><i class='glyphicon glyphicon-trash'></i></a>
				<a data-new-price='{$product['Product']['id']}' title='New Price' data-disabled='".($product['CurrentStock']['available_count'] > 0 ? "true" : "false")."'><i class='glyphicon glyphicon-edit'></i></a>
				<a data-price-history='{$product['Product']['id']}' data-name='{$product['Product']['name']}' title='Price History' ><i class='glyphicon glyphicon-signal'></i></a>
			</td>";
			echo "</tr>";
		}
	?>
<?php else: ?>
	<tr class="danger"><td colspan="9">There are no records found.</td></tr>
<?php endif; ?>
</table>