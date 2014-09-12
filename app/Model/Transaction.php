<?php

	App::uses('AppModel', 'Model');

	class Transaction extends AppModel {
		public $belongsTo = array(
			'Stock' => array(
				'className' => 'Stock',
				'foreignKey' => 'stock_id'
			)
		);
	}

?>