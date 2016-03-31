<div class="cell auto-size padding20 bg-white" id="cell-content">
	<h1 class="text-light">用户 <span class="mif-drive-eta place-right"></span></h1>
	<hr class="thin bg-grayLighter">
	<button class="button success" onclick="pushMessage('success')"><span class="mif-play"></span> 开始</button>
	<button class="button warning" onclick="pushMessage('warning')"><span class="mif-loop2"></span> 重启</button>
	<hr class="thin bg-grayLighter">
	<div class="dataTables_length">
		<label>显示
			<select name="DataTables_Table_0_length" class="">
				<option value="10">10</option>
				<option value="25">25</option>
				<option value="50">50</option>
				<option value="100">100</option>
			</select> 条
		</label>
	</div>
	<div class="dataTables_filter">
		<label>搜索：<input type="search" class="" placeholder="" aria-controls="DataTables_Table_0"></label>
	</div>
	<table class="dataTable border bordered">
		<thead>
			<tr>
				<td></td>
				<?php foreach( $keyList as $kv ): ?>
					<td class="sortable-column<?php if ( isset($kv['align']) ): ?> align-<?php echo $kv['align']; ?><?php endif; ?>"><?php echo $kv['alias']; ?></td>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $list['data'] as $node ): ?>
				<tr>
					<td class="align-center">
						<label class="input-control checkbox small-check no-margin">
							<input type="checkbox">
							<span class="check"></span>
						</label>
					</td>
					<?php foreach( $keyList as $kk => $kv ): ?>
						<td<?php if ( isset($kv['align']) ): ?> class="align-<?php echo $kv['align']; ?>"<?php endif; ?>><?php echo $node->$kk; ?></td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php echo $list['pager']; ?>
</div>

