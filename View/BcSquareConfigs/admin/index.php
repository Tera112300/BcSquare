<?php
$this->BcBaser->js([
	'admin/libs/jquery.baser_ajax_data_list',
	'admin/libs/jquery.baser_ajax_batch',
	'admin/libs/baser_ajax_data_list_config',
	'admin/libs/baser_ajax_batch_config'
]);
if ($this->BcBaser->isAdminUser()) {
	$this->BcAdmin->addAdminMainBodyHeaderLinks([
		'url' => ['action' => 'add'],
		'title' => __d('baser', '新規設定追加'),
	],);
}
?>
<script type="text/javascript">
$(document).ready(function(){
	$.baserAjaxDataList.init();
	$.baserAjaxBatch.init({ url: $("#AjaxBatchUrl").html()});
});
</script>



<div id="AjaxBatchUrl" style="display:none"><?php $this->BcBaser->url(['controller' => 'bc_square_configs', 'action' => 'ajax_batch']); ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="DataList">
<div class="bca-data-list__top">
	<div>
	<?php $this->BcBaser->link("クレジット決済 共通設定", ['controller' => 'bc_square_commons', 'action' => 'index'],['class' => 'bca-btn','data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'sm']); ?>
	</div>
</div>
<table class="list-table bca-table-listup" id="ListTable">
	<thead class="bca-table-listup__thead">
		<tr>
			<th class="bca-table-listup__thead-th">No</th>
			<th class="bca-table-listup__thead-th">コンテンツ名</th>
			<th class="bca-table-listup__thead-th">登録日</th>
			<th class="bca-table-listup__thead-th">削除</th>
		</tr>
	</thead>
<tbody class="bca-table-listup__tbody">
	<?php if (!empty($datas)): ?>
		<?php foreach ($datas as $data): ?>
		<tr>
		<td class="bca-table-listup__tbody-td"><?= $data["BcSquareConfig"]["id"]; ?></td>
		<td class="bca-table-listup__tbody-td"><?= $this->BcText->arrayValue($data['BcSquareConfig']['content_id'], $mailContentDatas,'') ?></td>
		
		<td class="bca-table-listup__tbody-td"><?= $this->BcTime->format('Y-m-d', $data['BcSquareConfig']['created']); ?></td>
		<td class="bca-table-listup__tbody-td">
		<?php
		$this->BcBaser->link('',
		[
			'action' => 'edit',
			$data['BcSquareConfig']['id']
		],
		[
			'title' => "編集",
			'class' => ' bca-btn-icon',
			'data-bca-btn-type' => 'edit',
			'data-bca-btn-size' => 'lg'
		]);
		?>
		<?php
		$this->BcBaser->link('',
		[
			'action' => 'ajax_delete',
			$data['BcSquareConfig']['id']
		],
		[
			'title' => "削除",
			'class' => 'btn-delete bca-btn-icon',
			'data-bca-btn-type' => 'delete',
			'data-bca-btn-size' => 'lg'
		]);
		?>
		</td>
		</tr>		
		<?php endforeach; ?>
		<?php else: ?>
			<tr>
				<td colspan="4" class="bca-table-listup__tbody-td">
					<p class="no-data">データが見つかりませんでした。</p>
				</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>
</div>
