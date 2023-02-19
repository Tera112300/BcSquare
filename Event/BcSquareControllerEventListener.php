<?php
require_once dirname(__FILE__) . '/../vendor/autoload.php';
App::uses('BcSquareCommon', 'BcSquare.Model');
App::uses('BcSquareConfig', 'BcSquare.Model');

use Square\Models\CreateOrderRequest;
use Square\Models\CreateCheckoutRequest;
use Square\Models\Order;
use Square\Models\OrderLineItem;
use Square\Models\Money;
use Square\Exceptions\ApiException;
use Square\SquareClient;

class BcSquareControllerEventListener extends BcControllerEventListener
{
	public $events = [
		'Mail.Mail.initialize',
		'Mail.Mail.beforeSendEmail',
	];
	private $mail_id;
	private $square_mail_data;
	private $BcSquareCommon;
	private $BcSquareConfig;
	private $client;

	public $dbDatas = null;

	public function mailMailinitialize(CakeEvent $event)
	{
		
		$Controller = $event->subject();
		
		$this->BcSquareCommon = new BcSquareCommon();
		$this->BcSquareConfig = new BcSquareConfig();
		$this->mail_id = (int)$Controller->request->entityId;

		//そのままだとメール・メッセージのテンプレートテーブルになるのでsetup実行
		$Controller->MailMessage->setup($this->mail_id);

		$this->dbDatas['mailContent'] = $Controller->MailMessage->mailContent;
		$this->dbDatas['mailFields'] = $Controller->MailMessage->mailFields;
		$this->dbDatas['mailConfig'] = $Controller->MailConfig->find();

		$this->square_mail_data = $this->BcSquareConfig->findByContentId($this->mail_id);

	
		if (isset($this->square_mail_data["BcSquareConfig"])) {
			$BcSquareCommon_data = $this->BcSquareCommon->find("first");

			$access_token = $BcSquareCommon_data["BcSquareCommon"]["access_token"];
			$environment = (int)$BcSquareCommon_data["BcSquareCommon"]["environment"] === 2 ? "sandbox" : "production ";
			$this->client = new SquareClient([
				'accessToken' => $access_token,
				'environment' => $environment,
			]);

			
			if ($Controller->request->params["action"] === "thanks") {
				$Checkout = $Controller->Session->consume('BcSquare.Checkout');
				$checkoutId = $Controller->request->query('checkoutId');
				$transactionId = $Controller->request->query('transactionId');

				if (isset($checkoutId) && $Checkout === $checkoutId && isset($transactionId)) {
					try {
						$orders_api = $this->client->getOrdersApi();
						$response = $orders_api->retrieveOrder($transactionId);
					} catch (ApiException $e) {
					
					$Exception_message = "例外処理です。\n\n";
					$Exception_message = "応答本文:".var_dump($e->getResponseBody())."\n\n";
					$Exception_message = "各種情報:".var_dump($e->getContext());
					throw new NotFoundException($Exception_message);
					exit;
					}
					if ($response->isError()) {
						//エラー
						$error_message = "API 応答にエラーがあります。\n\n";
						$errors = $response->getErrors();
						if(isset($errors)){
							foreach ($errors as $error) {
								$error_message .= $error->getDetail()."\n";
							}
						}
						throw new NotFoundException($error_message);
						exit;
					}else{
						$order = $response->getResult()->getOrder();
						$Controller->Session->write('Mail.MailContent', true);

						//主キー以外で更新するためupdateAll
						$state = $order->getState() === "COMPLETED" ? "受注完了" : $order->getState();

						$Controller->MailMessage->updateAll([
							"square_status" => "'".$state."'",
							"square_price" => "'".$order->getTotalMoney()->getAmount()."'"
						],[
							"square_checkout_id" => $Checkout
						]);
						$Controller->Session->delete('Mail.valid');

						if(!$this->_sendEmail($Controller->MailMessage->findBySquareCheckoutId($Checkout),$Controller)){
							throw new NotFoundException("エラー : 送信中にエラーが発生しました。");
							exit;
						}
					}
				} else {
					$Controller->Session->delete('Mail.MailContent');
					$Controller->redirect($Controller->request->param('Content.url') . '/index');
				}
			}
		}
	}


	public function mailMailBeforeSendEmail(CakeEvent $event)
	{
		if (isset($this->square_mail_data["BcSquareConfig"])) {
			$Controller = $event->subject();
			//再送信できるためセッション削除
			$Controller->Session->delete('Mail.valid');

			$MailMessageId = $Controller->request->data["MailMessage"]["id"];


			$BcSquareCommon_data = $this->BcSquareCommon->find("first");
			$location_id =  $BcSquareCommon_data["BcSquareCommon"]["location_id"];


			try {
				$checkout_api = $this->client->getCheckoutApi();

				$currency = $this->client->getLocationsApi()->retrieveLocation($location_id)->getResult()->getLocation()->getCurrency();
				$money1 = new Money();
				$money1->setCurrency($currency);
				$money1->setAmount($this->square_mail_data["BcSquareConfig"]["price"]);

				$item1 = new OrderLineItem(1);
				$item1->setName($Controller->request["Content"]["title"]);
				$item1->setBasePriceMoney($money1);

				$order = new Order($location_id);
				$order->setLineItems([$item1]);

				$create_order_request = new CreateOrderRequest();
				$create_order_request->setOrder($order);

				$checkout_request = new CreateCheckoutRequest(uniqid(), $create_order_request);
				$checkout_request->setRedirectUrl(Router::url('/', true) . $Controller->request->param('Content.url') . '/thanks');

				$response = $checkout_api->createCheckout($location_id, $checkout_request);
				
			} catch (ApiException $e) {
				//例外

				$Controller->MailMessage->save([
					"id" => $MailMessageId,
					"square_status" => "受注エラー例外",
					"square_price" => $this->square_mail_data["BcSquareConfig"]["price"],
				], false);

				$Exception_message = "例外処理です。\n\n";
				$Exception_message = "応答本文:".var_dump($e->getResponseBody())."\n\n";
				$Exception_message = "各種情報:".var_dump($e->getContext());
				throw new NotFoundException($Exception_message);
				exit;
			}

			if ($response->isError()) {
				//エラー処理
				$Controller->MailMessage->save([
					"id" => $MailMessageId,
					"square_status" => "受注エラー",
					"square_price" => $this->square_mail_data["BcSquareConfig"]["price"],
				], false);
				$error_message = "API 応答にエラーがあります。\n\n";

				$errors = $response->getErrors();
				if(isset($errors)){
					foreach ($errors as $error) {
						$error_message .= $error->getDetail()."\n";
					}
				}
				throw new NotFoundException($error_message);
				exit;
			}


			if (!$Controller->MailMessage->save([
				"id" => $MailMessageId,
				"square_status" => "未受注",
				"square_price" => $this->square_mail_data["BcSquareConfig"]["price"],
				"square_checkout_id" => $response->getResult()->getCheckout()->getID()
			], false)) {
				$Controller->log("受注ID「" . $response->getResult()->getCheckout()->getID() . "」の記入処理に失敗しました。");
			}

			$Controller->Session->write('BcSquare.Checkout', $response->getResult()->getCheckout()->getID());
			$Controller->redirect($response->getResult()->getCheckout()->getCheckoutPageUrl());
			exit;
		} else {
			//クレジット決済処理を挟まなメールプラグインのコンテンツの場合はそのまま進める
			return true;
		}
	}


	protected function _sendEmail($options,$Controller)
	{
		$options = array_merge(
			[
				'toUser' => [],
				'toAdmin' => [],
			],
			$options
		);

		$mailConfig = $this->dbDatas['mailConfig']['MailConfig'];
		$mailContent = $this->dbDatas['mailContent']['MailContent'];
		$userMail = '';

		// データを整形
		$data = $Controller->MailMessage->convertToDb($options);

		$data['message'] = $data['MailMessage'];
		unset($data['MailMessage']);

		$data['content'] = $Controller->request->param('Content');
		
		$data['mailFields'] = $this->dbDatas['mailFields'];
		$data['mailContents'] = $this->dbDatas['mailContent']['MailContent'];
		$data['mailConfig'] = $this->dbDatas['mailConfig']['MailConfig'];
		$data['other']['date'] = date('Y/m/d H:i');
		$data = $Controller->MailMessage->convertDatasToMail($data);

		// 管理者メールを取得
		if ($mailContent['sender_1']) {
			$adminMail = $mailContent['sender_1'];
		} else {
			$adminMail = $Controller->siteConfigs['email'];
		}
		if (strpos($adminMail, ',') !== false) {
			list($fromAdmin) = explode(',', $adminMail);
		} else {
			$fromAdmin = $adminMail;
		}

		// 送信先名を取得
		if ($mailContent['sender_name']) {
			$fromName = $mailContent['sender_name'];
		} else {
			$fromName = $Controller->siteConfigs['name'];
		}

		$attachments = [];
		$settings = $Controller->MailMessage->Behaviors->BcUpload->BcFileUploader['MailMessage']->settings;
		foreach($this->dbDatas['mailFields'] as $mailField) {
			$field = $mailField['MailField']['field_name'];
			if (!isset($data['message'][$field])) {
				continue;
			}
			$value = $data['message'][$field];
			// ユーザーメールを取得
			if ($mailField['MailField']['type'] === 'email' && $value) {
				$userMail = $value;
			}
			// 件名にフィールドの値を埋め込む
			// 和暦など配列の場合は無視
			if (!is_array($value)) {
				$mailContent['subject_user'] = str_replace(
					'{$' . $field . '}',
					$value,
					$mailContent['subject_user']
				);
				$mailContent['subject_admin'] = str_replace(
					'{$' . $field . '}',
					$value,
					$mailContent['subject_admin']
				);
			}
			if ($mailField['MailField']['type'] === 'file' && $value) {
				$attachments[] = WWW_ROOT . 'files' . DS . $settings['saveDir'] . DS . $value;
			}
			// パスワードは入力値をマスクした値を表示
			if ($mailField['MailField']['type'] === 'password' && $value && !empty($options['maskedPasswords'][$field])) {
				$data['message'][$field] = $options['maskedPasswords'][$field];
			}
		}

		// 前バージョンとの互換性の為 type が email じゃない場合にも取得できるようにしておく
		if (!$userMail) {
			if (!empty($data['message']['email'])) {
				$userMail = $data['message']['email'];
			} elseif (!empty($data['message']['email_1'])) {
				$userMail = $data['message']['email_1'];
			}
		}

		// 管理者に送信
		if (!empty($adminMail)) {
			$data['other']['mode'] = 'admin';
			$sendResult = $Controller->sendMail(
				$adminMail,
				$mailContent['subject_admin'],
				$data,
				array_merge(
					[
						'fromName' => $fromName,
						// カンマ区切りで複数設定されていた場合先頭のアドレスをreplayToに利用
						'replyTo' => strpos($userMail, ',') === false? $userMail : strstr($userMail, ',', true),
						'from' => $fromAdmin,
						'template' => 'Mail.' . $mailContent['mail_template'],
						'bcc' => $mailContent['sender_2'],
						'agentTemplate' => false,
						'attachments' => $attachments,
						// 'additionalParameters' => '-f ' . $fromAdmin,
					],
					$options['toAdmin']
				)
			);
			if (!$sendResult) {
				return false;
			}
		}

		// ユーザーに送信
		if (!empty($userMail)) {
			$site = BcSite::findCurrent();
			$data['other']['mode'] = 'user';
			$sendResult = $Controller->sendMail(
				$userMail,
				$mailContent['subject_user'],
				$data,
				array_merge(
					[
						'fromName' => $mailContent['sender_name'],
						'from' => $fromAdmin,
						'template' => 'Mail.' . $mailContent['mail_template'],
						'replyTo' => $fromAdmin,
						'agentTemplate' => ($site && $site->device)? true : false,
						// 'additionalParameters' => '-f ' . $fromAdmin,
					],
					$options['toUser']
				)
			);
			if (!$sendResult) {
				return false;
			}
		}
		return true;
	}
}
