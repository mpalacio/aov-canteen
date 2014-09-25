<?php
App::uses('AppController', 'Controller');

class PagesController extends AppController {
	var $uses = array('Product', 'Stock', 'Transaction' ,'User');

	public function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));
		$this->$page();

		try {
			$this->render(implode('/', $path));
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}

	private function pos() {
	}

	private function inventory() {
	}

	public function ajax_get_inventory() {
		$allowed = array('id', 'name', 'available_count');
		$params = $this->uniform_params($this->request->data, $allowed);
		$filters = array(
			'id' => array('Product.id' => $params['id']),
			'name' => array('Product.name' => $params['name']),
			1 => array('CurrentStock.available_count >' => 0),
			2 => array('CurrentStock.available_count' => 0)
		);
		$use_filters = array();
		foreach ($params as $key => $value) {
			if(trim($value) != '') {
				if($key == 'available_count')
					array_push($use_filters, $filters[$value]);
				else
					array_push($use_filters, $filters[$key]);
			}
		}

		$allowed = array('sort', 'direction', 'page');
		$params = $this->uniform_params($this->request->data, $allowed);
		foreach ($params as $key => $value)
			if($value == null)
				unset($params[$key]);

		$options = array(
			'limit' => 10,
			'fields' => array('MAX(CurrentStock.price_date) AS current_price_date', '*'),
			'conditions' => array('OR' => $use_filters),
			'group' => 'Product.id'
		);
		$this->paginate = array_merge($params, $options);

		$this->Product->recursive = 2;
		$this->Product->unbindModel(array('hasMany' => array('Stock')));
		$this->Product->CurrentStock->unbindModel(array('hasMany' => array('Transaction'), 'belongsTo' => array('Product')));
		$this->set('page', (isset($params['page']) ? $params['page'] : 1));
		$products = $this->paginate('Product');
		$this->set(compact('products'));
		$this->layout = 'ajax';
	}

	public function ajax_add_product() {
		$allowed = array('name', 'purchase_price', 'selling_price', 'available_count', 'total_count', 'notes');
		$params = $this->uniform_params($this->request->data, $allowed);
		$this->Product->recursive = 0;
		$product = $this->Product->findByName($params['name']);
		if(!$product) {
			$this->Product->create();
			$this->Product->save(array('Product' => $params), array('fieldList' => array('name')));
			unset($params['name']);
			$params['product_id'] = $this->Product->id;
			$params['price_date'] = date('Y-m-d h:i:s');
			$this->Stock->create();
			$this->Stock->save(array('Stock' => $params));
			$this->Transaction->create();
			$transaction = array('Transaction' => array(
				'date' => date('Y-m-d h:i:s'),
				'type' => 'new_stock',
				'stock_id' => $this->Stock->id,
				'count' => $params['available_count'],
				'customer_id' => $this->Auth->User('id')
			));
			$this->Transaction->save($transaction);
			$save = true;
		}
		else {
			$save = false;
		}

		$this->set('data', $save);
		$this->layout = 'ajax';
		$this->render('/Elements/serialize_json');
	}

	public function ajax_delete_product() {
		$allowed = array('id');
		$params = $this->uniform_params($this->request->data, $allowed);
		$this->Product->recursive = 2;
		$this->Product->unbindModel(array('hasOne' => array('CurrentStock')));
		$this->Product->Stock->unbindModel(array('hasMany' => array('Transaction'), 'belongsTo' => array('Product')));
		$product = $this->Product->findById($params['id']);
		if($product) {
			$sold = false;
			foreach ($product['Stock'] as $stock) {
				if($stock['sold_count'] > 0) {
					$sold = true;
					break;
				}
			}
			if(!$sold) {
				$this->Product->delete($params['id'], true);
				$this->set('data', 'Success');
			}
			else {
				$this->set('data', 'Sold');
			}
		}
		else
			$this->set('data', 'Invalid');
		$this->layout = 'ajax';
		$this->render('/Elements/serialize_json');
	}

	public function ajax_add_stock() {
		$allowed = array('id', 'available_count', 'total_count');
		$new_stock = $this->request->data['count'];
		$params = $this->uniform_params($this->request->data, $allowed);
		if($this->Stock->save($params)) {
			$this->Transaction->create();
			$transaction = array('Transaction' => array(
				'date' => date('Y-m-d h:i:s'),
				'type' => 'add_stock',
				'stock_id' => $params['id'],
				'count' => $new_stock,
				'customer_id' => $this->Auth->User('id')
			));
			$this->Transaction->save($transaction);
			$this->set('data', 'Success');
		}
		else
			$this->set('data', 'Invalid');
		$this->layout = 'ajax';
		$this->render('/Elements/serialize_json');
	}

	public function ajax_add_new_price() {
		$allowed = array('product_id', 'available_count', 'purchase_price', 'selling_price');
		$old_purchase_price = $this->request->data['old_purchase_price'];
		$old_selling_price = $this->request->data['old_selling_price'];
		$params = $this->uniform_params($this->request->data, $allowed);
		if($params['purchase_price'] != $old_purchase_price | $params['selling_price'] != $old_selling_price) {
			$this->Stock->create();
			$params['price_date'] = date('Y-m-d h:i:s');
			$params['total_count'] = $params['available_count'];
			if($this->Stock->save($params)) {
				$this->Transaction->create();
				$transaction = array('Transaction' => array(
					'date' => date('Y-m-d h:i:s'),
					'type' => 'new_stock',
					'stock_id' => $this->Stock->id,
					'count' => $params['available_count'],
					'customer_id' => $this->Auth->User('id')
				));
				$this->Transaction->save($transaction);
				$this->set('data', array('success' => true, 'price_date' => $params['price_date']));
			}
			else
				$this->set('data', array('success' => false));
		}
		else
				$this->set('data', array('success' => false));
		$this->layout = 'ajax';
		$this->render('/Elements/serialize_json');
	}

	private function transactions() {
	}
}
