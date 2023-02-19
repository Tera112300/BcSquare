<?php 
class BcSquareConfigsSchema extends CakeSchema {
	public $name = 'BcSquareConfigs';
	public $file = 'bc_square_configs.php';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $bc_square_configs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'content_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'コンテンツID'),
		'price' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '価格'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日時'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '作成日時'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
	);
}
