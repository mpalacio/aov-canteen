<?php

	App::uses('AppModel', 'Model');

	class Product extends AppModel {
		public $hasMany = array(
			'Stock' => array(
				'className' => 'Stock',
				'foreignKey' => 'product_id',
				'dependent' => true
			)
		);

		public $hasOne = array(
			'CurrentStock' => array(
				'className' => 'Stock',
				'type' => 'inner',
				'foreignKey' => 'product_id',
				'order' => array('CurrentStock.product_id ASC', 'CurrentStock.price_date DESC'),
				'dependent' => true
			)
		);
	}

?>