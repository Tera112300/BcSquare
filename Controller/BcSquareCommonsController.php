<?php
App::uses('BcSquareApp', 'BcSquare.Controller');

class BcSquareCommonsController extends BcSquareAppController
{

	public $uses = [
		'BcSquare.BcSquareCommon',
	];

	/**
	 * 管理画面タイトル
	 *
	 * @var string
	 */
	public $adminTitle = 'クレジット決済 共通設定';
	

	/**
	 * [ADMIN] 設定一覧
	 *
	 */
	public function admin_index()
	{
		$this->pageTitle = $this->adminTitle . '更新';
		$this->request->is('get') ? $this->request->data = $this->BcSquareCommon->find('first') : "";
		$this->set('environment',Configure::read("BcSquare")["environment"]);
		
		if ($this->request->is(['post','put'])) {
			$BcSquare_data = $this->BcSquareCommon->find('first');
			$this->{$this->modelClass}->set($this->request->data);
			
			if($this->{$this->modelClass}->validates($this->request->data)){
				if(isset($BcSquare_data["BcSquareCommon"]["id"])){
					$this->request->data["BcSquareCommon"]["id"] = $BcSquare_data["BcSquareCommon"]["id"];
				}

				if ($this->{$this->modelClass}->save($this->request->data)) {
					$message = $this->name . 'を追加しました。';
					$this->BcMessage->setSuccess($message);
					$this->redirect(['action' => 'index']);
				} else {
					$this->BcMessage->setError('入力エラーです。内容を修正して下さい。');
				}
			}
		}
	}
}
