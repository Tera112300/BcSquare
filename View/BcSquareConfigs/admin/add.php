<p style="color:red;">新規追加するとメールのフィールドに「受注ID、価格、受注ステータス」が追加されます。削除しないでください。<br>既に存在する場合は追加出来ません</p>
<?= $this->BcForm->create('BcSquareConfig', ['url' => ['action' => 'add']]); ?>
<div id="BcSquareConfigTable" class="section">
	<table id="FormTable" class="form-table bca-form-table">
		<tr>
			<th class="col-head bca-form-table__label">
				<?= $this->BcForm->label('BcSquareConfig.content_id', 'メールフォーム') ?>
			</th>
			<td class="col-input bca-form-table__input">
				<?= $this->BcForm->input('BcSquareConfig.content_id', ['type' => 'select', 'options' => $mailContentDatas]) ?>
				<?= $this->BcForm->error('BcSquareConfig.content_id') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label">
				<?= $this->BcForm->label('BcSquareConfig.price', '価格') ?>
			</th>
			<td class="col-input bca-form-table__input">
				<?= $this->BcForm->input('BcSquareConfig.price', ['type' => 'text']) ?>
				<?= $this->BcForm->error('BcSquareConfig.price') ?>
			</td>
		</tr>
	</table>
</div>


<!-- button -->
<div class="submit bca-actions">
	<div class="bca-actions__main">
		<?php $this->BcBaser->link('一覧に戻る', ['action' => 'index'], [
			'class' => 'button bca-btn',
			'data-bca-btn-type' => 'back-to-list'
		]) ?>
		<?= $this->BcForm->button(__d('baser', '保存'), [
			'div' => false, 'class' => 'button bca-btn bca-actions__item',
			'data-bca-btn-type' => 'save',
			'data-bca-btn-size' => 'lg',
			'data-bca-btn-width' => 'lg',
		]) ?>
	</div>
</div>
<?= $this->BcForm->end() ?>