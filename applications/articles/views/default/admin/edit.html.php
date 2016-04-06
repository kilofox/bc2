<div class="cell auto-size padding20 bg-white" id="cell-content">
	<h1 class="text-light">网站设置 <span class="mif-drive-eta place-right"></span></h1>
	<hr class="thin bg-grayLighter">
	<form id="edit">
		<div class="tabcontrol" data-role="tabcontrol">
			<ul class="tabs">
				<li class="active"><a href="#frame_1">常规设置</a></li>
				<li><a href="#frame_2">公司设置</a></li>
				<li><a href="#frame_3">日期和时间</a></li>
			</ul>
			<div class="frames">
				<div class="frame" id="frame_1">
					<lable>网站标题</lable>
					<div class="input-control text full-size">
						<input type="text" name="title" value="<?php echo $node->title; ?>">
					</div>
					<lable>网站描述</lable>
					<div class="input-control textarea full-size">
						<textarea id="editor_id" name="content"/><?php echo $node->content; ?></textarea>
					</div>
					<lable>默认关键词</lable>
					<div class="input-control text full-size">
						<input type="text" name="keywords" value="<?php echo $node->keywords; ?>">
					</div>
					<lable>默认描述</lable>
					<div class="input-control textarea full-size">
						<textarea name="descript"><?php echo $node->descript; ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="nid" value="<?php echo $node->id; ?>"/>
		<br/>
		<input type="reset" class="button lighten primary" value="重置"/>
		<button type="submit" class="button lighten primary">提交</button>
	</form>
</div>
<script src="<?php echo $baseUrl; ?>/assets/admin/editor/kindeditor.js"></script>
<script src="<?php echo $baseUrl; ?>/assets/admin/editor/lang/zh_CN.js"></script>
<script>
	KindEditor.ready(function(K){
		window.editor = K.create('#editor_id', {
			width: '100%',
			height: '400px'
		});
	});
	var baseUrl = '<?php echo $baseUrl; ?>';
	$(function(){
		$('form').submit(function(){
			$('button[type="submit"]').attr('disabled', true);
			$('button[type="submit"]').addClass('loading-cube');
			$.ajax({
				url: baseUrl + '/articles/admin/<?php echo $node->id; ?>/edit',
				type: 'put',
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