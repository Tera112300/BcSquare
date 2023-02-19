<?php

class BcSquareAppController extends AppController
{

	/**
	 * Helper
	 *
	 * @var array
	 */
	public $helpers = ['Mail.Mail'];

	/**
	 * Component
	 *
	 * @var     array
	 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

	/**
	 * サブメニューエレメント
	 *
	 * @var array
	 */
	public $subMenuElements = ['petit_custom_field'];

	/**
	 * ぱんくずナビ
	 *
	 * @var string
	 */
	public $crumbs = [
		['name' => 'プラグイン管理', 'url' => ['plugin' => '', 'controller' => 'plugins', 'action' => 'index']]
	];

	/**
	 * 管理画面タイトル
	 *
	 * @var string
	 */
	public $adminTitle = '';

	/**
	 * ブログコンテンツデータ
	 *
	 * @var array
	 */
	public $mailContentDatas = [];

	/**
	 * Before Filter
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		// ブログ設定データを取得
		if (ClassRegistry::isKeySet('Content')) {
			$ContentModel = ClassRegistry::getObject('Content');
		} else {
			$ContentModel = ClassRegistry::init('Content');
		}
		$this->mailContentDatas = $ContentModel->find('list', [
			'fields' => [
				'entity_id',
				'title',
			],
			'conditions' => [
				'plugin' => 'Mail',
				'type' => 'MailContent',
			],
			'recursive' => -1,
		]);
	}

	public function admin_edit($id = null)
	{
		if (!$id) {
			$this->BcMessage->setError('無効な処理です。');
			$this->redirect(['action' => 'index']);
		}

		if (empty($this->request->data)) {
			$this->{$this->modelClass}->id = $id;
			$this->request->data = $this->{$this->modelClass}->read();
		} else {
			if ($this->{$this->modelClass}->save($this->request->data)) {
				$message = $this->name . ' ID:' . $this->request->data[$this->modelClass]['id'] . '」を更新しました。';
				$this->BcMessage->setSuccess($message);
				$this->redirect(['action' => 'index']);
			} else {
				$this->BcMessage->setError('入力エラーです。内容を修正して下さい。');
			}
		}

		$this->set('mailContentDatas', ['0' => '指定しない'] + $this->mailContentDatas);
	}

	

	/**
	 * [ADMIN] 削除処理　(ajax)
	 *
	 * @param int $id
	 */
	public function admin_ajax_delete($id = null)
	{
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		// 削除実行
		if ($this->_delete($id)) {
			clearViewCache();
			exit(true);
		}
		exit();
	}

	/**
	 * データを削除する
	 *
	 * @param int $id
	 * @return boolean
	 */
	protected function _delete($id)
	{
		// メッセージ用にデータを取得
		$data = $this->{$this->modelClass}->read(null, $id);
		// 削除実行
		if ($this->{$this->modelClass}->delete($data[$this->modelClass]['id'])) {
			$this->{$this->modelClass}->saveDbLog($this->name . ' ID:' . $data[$this->modelClass]['id'] . ' を削除しました。');
			return true;
		} else {
			return false;
		}
	}

	
}
