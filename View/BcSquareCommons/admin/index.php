<?= $this->BcForm->create('BcSquareCommon', ['url' => ['action' => 'index']]); ?>
<div id="BcSquareCommonTable" class="section">
	<table id="FormTable" class="form-table bca-form-table">
	
		<tr>
			<th class="col-head bca-form-table__label">
				<?= $this->BcForm->label('BcSquareCommon.application_id', 'アプリケーションID') ?>
			</th>
			<td class="col-input bca-form-table__input">
				<?= $this->BcForm->input('BcSquareCommon.application_id', ['type' => 'text']) ?>
				<?= $this->BcForm->error('BcSquareCommon.application_id') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label">
				<?= $this->BcForm->label('BcSquareCommon.access_token', 'アクセストークン') ?>
			</th>
			<td class="col-input bca-form-table__input">
				<?= $this->BcForm->input('BcSquareCommon.access_token', ['type' => 'text']) ?>
				<?= $this->BcForm->error('BcSquareCommon.access_token') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label">
				<?= $this->BcForm->label('BcSquareCommon.location_id', 'ロケーションID') ?>
			</th>
			<td class="col-input bca-form-table__input">
				<?= $this->BcForm->input('BcSquareCommon.location_id', ['type' => 'text']) ?>
				<?= $this->BcForm->error('BcSquareCommon.location_id') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label">
				<?= $this->BcForm->label('BcSquareCommon.environment', '環境') ?>
			</th>
			<td class="col-input bca-form-table__input">
				<?= $this->BcForm->input('BcSquareCommon.environment', ['type' => 'select', 'options' => $environment]) ?>
				<?= $this->BcForm->error('BcSquareCommon.environment') ?>
			</td>
		</tr>
	</table>
</div>


<!-- button -->
<div class="submit bca-actions">
	<div class="bca-actions__main">
		<?php $this->BcBaser->link('一覧に戻る', ['controller' => 'bc_square_configs','action' => 'index'], [
			'class' => 'button bca-btn',
			'data-bca-btn-type' => 'back-to-list'
		]) ?>
		<?php echo $this->BcForm->button(__d('baser', '更新'), [
			'div' => false, 'class' => 'button bca-btn bca-actions__item',
			'data-bca-btn-type' => 'save',
			'data-bca-btn-size' => 'lg',
			'data-bca-btn-width' => 'lg',
		]) ?>
	</div>
</div>
<?php echo $this->BcForm->end() ?>