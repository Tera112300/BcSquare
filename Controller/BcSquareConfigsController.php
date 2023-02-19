<?php
App::uses('BcSquareApp', 'BcSquare.Controller');
App::uses('MailMessage', 'Mail.Model');
class BcSquareConfigsController extends BcSquareAppController
{
	public $uses = [
		'BcSquare.BcSquareConfig',
		'Mail.MailMessage',
		'Mail.MailField',
	];

	public $mail_message;	
	public $adminTitle = 'クレジット決済 設定';

	/**
	 * beforeFilter
	 *
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
	}

	/**
	 * [ADMIN] 設定一覧
	 *
	 */
	public function admin_index()
	{
		$this->pageTitle = $this->adminTitle . '一覧';
		
		$this->paginate = [
			'limit' => 15
		];

		$this->set('datas', $this->paginate('BcSquareConfig'));
		$this->set('mailContentDatas', ['0' => '指定しない'] + $this->mailContentDatas);
	}

	
	/**
	 * [ADMIN] 新規登録
	 *
	 */
	public function admin_add()
	{
		$this->pageTitle = $this->adminTitle . '追加';

		if ($this->request->is('post')) {
			$data = [
			"MailField" => [
				0 => [
					"mail_content_id" => $this->request->data("BcSquareConfig.content_id"),
					"type" => "hidden",
					"use_field" => 1,
					"field_name" => "square_checkout_id",
					"name" => "受注ID",
					"head" => "受注ID",
					"valid_ex" => "VALID_REGEX",//正規表現バリデーション未入力にする
					"options" => "regex|\?$"
				],
				1 => [
					"mail_content_id" => $this->request->data("BcSquareConfig.content_id"),
					"type" => "hidden",
					"use_field" => 1,
					"field_name" => "square_price",
					"name" => "価格",
					"head" => "価格",
					"valid_ex" => "VALID_REGEX",
					"options" => "regex|\?$"
				],
				2 => [
					"mail_content_id" => $this->request->data("BcSquareConfig.content_id"),
					"type" => "hidden",
					"use_field" => 1,
					"field_name" => "square_status",
					"name" => "受注ステータス",
					"head" => "受注ステータス",
					"valid_ex" => "VALID_REGEX",
					"options" => "regex|\?$"
				]
			]
			];
	
			if($this->{$this->modelClass}->validates($this->request->data)){
				$ret1 = $this->MailMessage->addMessageField(
					$this->request->data("BcSquareConfig.content_id"),
					$data['MailField'][0]['field_name']
				);

				$ret2 = $this->MailMessage->addMessageField(
					$this->request->data("BcSquareConfig.content_id"),
					$data['MailField'][1]['field_name']
				);

				$ret3 = $this->MailMessage->addMessageField(
					$this->request->data("BcSquareConfig.content_id"),
					$data['MailField'][2]['field_name']
				);
			

				if($ret1 && $ret2 && $ret3){
					if ($this->MailField->saveAll($data["MailField"]) && $this->{$this->modelClass}->save($this->request->data)) {
						$message = $this->name . 'を追加しました。';
						$this->BcMessage->setSuccess($message);
						$this->redirect(['action' => 'index']);
					}
				}else{
					$this->BcMessage->setError(
						__d('baser', 'データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。「square_checkout_id・square_price・square_status」が既に作成されているか場合は作成出来ません。')
					);
				}

			}
			
		} else {
			$this->request->data[$this->modelClass]['model'] = 'MailContent';
		}

		// 設定データがあるメールは選択リストから除外する
		$dataList = $this->{$this->modelClass}->find('all');
		if ($dataList) {
			foreach($dataList as $data) {
				unset($this->mailContentDatas[$data[$this->modelClass]['content_id']]);
			}
		}

		$this->set('mailContentDatas', $this->mailContentDatas);
	}

	/**
	 * [ADMIN] 編集
	 *
	 * @param int $id
	 */
	public function admin_edit($id = null)
	{
		$this->pageTitle = $this->adminTitle . '編集';

		
		parent::admin_edit($id);
	}

}
