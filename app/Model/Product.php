<?php

	App::uses('AppModel', 'Model');

	class Product extends AppModel {
		public $hasMany = array(
			'Product' => array(
				'className' => 'Stock',
				'foreignKey' => 'product_id'
			)
		);
	}

?>