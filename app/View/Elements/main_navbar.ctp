<?php
	$current_page = isset($page) ? $page : null;
	$pages = array(
		array('action' => 'pos', 'page' => 'POS'),
		array('action' => 'inventory', 'page' => 'Inventory'),
		array('action' => 'transactions', 'page' => 'Transactions'),
		array('action' => 'sales', 'page' => 'Sales')
	);
?>
<ul class="nav nav-pills">
<?php
	foreach ($pages as $page)
		echo "<li".($current_page == $page['action'] ? " class='active'" : "").">".
			$this->Html->link($page['page'], "/".$page['action'], array('escape' => false)).
			"</li>";
	if($auth)
		echo "<li>".$this->Html->link('Logout', array('controller' => 'users', 'action' => 'logout'))."</li>";
?>
</ul>