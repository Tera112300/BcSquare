<?php 
class BcSquareCommonsSchema extends CakeSchema {
	public $name = 'BcSquareCommons';
	public $file = 'bc_square_commons.php';

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $bc_square_commons = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'application_id' => array('type' => 'string', 'null' => false, 'default' => null,'comment' => 'アプリケーションID'),
		'access_token' => array('type' => 'string', 'null' => false, 'default' => null,'comment' => 'アクセストークン'),
		'location_id' => array('type' => 'string', 'null' => false, 'default' => null,'comment' => 'ロケーションID'),
		'environment' => array('type' => 'integer', 'null' => false, 'default' => null,'comment' => '環境設定'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日時'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '作成日時'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
	);
}
