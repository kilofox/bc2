<div class="cell auto-size padding20 bg-white" id="cell-content">
	<h1 class="text-light">文章分类 <span class="mif-drive-eta place-right"></span></h1>
	<hr class="thin bg-grayLighter">
	<button class="button primary" data-btn="create"><span class="mif-plus"></span> 创建</button>
	<hr class="thin bg-grayLighter">
	<table class="dataTable border bordered">
		<thead>
			<tr>
				<td class="sortable-column sort-asc">ID</td>
				<td class="sortable-column">名称</td>
				<td class="sortable-column">别名</td>
				<td class="sortable-column">描述</td>
				<td class="align-center">操作</td>
			</tr>
		</thead>
		</thead>
		<tbody>
			<?php foreach( $nodes as $node ): ?>
				<tr>
					<td><?php echo $node->id; ?></td>
					<td><?php echo $node->title; ?></td>
					<td><?php echo $node->slug; ?></td>
					<td><?php echo $node->description; ?></td>
					<td class="align-center">
						<a href="<?php echo $baseUrl; ?>/articles/admin/category/<?php echo $node->id; ?>/edit">编辑</a>
						<a href="<?php echo $baseUrl; ?>/articles/admin/category/<?php echo $node->id; ?>/delete">删除</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<script>
	$(function(){
		$('button[data-btn="create"]').click(function(){
			window.location.href = baseUrl + '/articles/admin/category/create';
		});
		$('a[data-del]').click(function(){
			alert('您确定要删除 ID #' + $(this).attr('data-del') + '：' + $(this).parent().siblings().eq(1).text() + ' 这个角色吗？');
			$.ajax({
				url: baseUrl + '/articles/admin/category/' + $(this).attr('data-del') + '/delete',
				type: 'delete',
				dataType: 'json',
				data: $('#edit').serialize(),
				success: function(r){
					if ( r.status === 1 ){
						$('button[type="submit"]').attr('disabled', false);
						$('button[type="submit"]').removeClass('loading-cube');
						$('#dialog_s > p').text(r.info);
						var dialog = $('#dialog_s').data('dialog');
						dialog.open();
					}else{
						$('button[type="submit"]').attr('disabled', false);
						$('button[type="submit"]').removeClass('loading-cube');
						$('#dialog_i > p').text(r.info);
						var dialog = $('#dialog_i').data('dialog');
						dialog.open();
					}
				}
			});
			return false;
		});
	});
</script>
