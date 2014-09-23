<ul class="pagination pagination-sm ajax-pagination<?php echo isset($class) ? " $class" : ""; ?>">
<?php
	$this->Paginator->options(
		array(
			'url' => array_merge(
				array(
					'controller' => $this->request->params['controller'],
					'action' => $this->request->params['action'],
				),
				$this->request->params['pass'],
				$this->request->params['named']
			)
		)
	);
	echo $this->Paginator->prev(
		'&laquo; Prev',
		array(
			'tag' => 'li',
			'escape' => false
		), null,
		array(
			'tag' => 'li',
			'class' => 'disabled',
			'disabledTag' => 'a',
			'escape' => false
		)
	), "\n";
	echo $this->Paginator->numbers(
		array(
			'modulus' => 2,
			'first' => 3,
			'last' => 3,
			'ellipsis' => '<li class="ellipsis"><a>&hellip;</a></li>',
			'separator' => "\n",
			'escape' => false,
			'tag' => 'li',
			'currentClass' => 'active',
			'currentTag' => 'a'
		)
	);
	echo $this->Paginator->next(
		'Next &raquo;',
		array(
			'tag' => 'li',
			'escape' => false
		), null,
		array(
			'tag' => 'li',
			'class' => 'disabled',
			'disabledTag' => 'a',
			'escape' => false
		)
	), "\n";
?>
</ul>
