<?php

	App::uses('AppModel', 'Model');

	class Stock extends AppModel {
		public $belongsTo = array(
			'Product' => array(
				'className' => 'Product',
				'foreignKey' => 'product_id'
			)
		);

		public $hasMany = array(
			'Transaction' => array(
				'className' => 'Transaction',
				'foreignKey' => 'stock_id',
				'dependent' => true
			)
		);
	}

?>