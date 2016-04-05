<div class="cell auto-size padding20 bg-white" id="cell-content">
	<h1 class="text-light"><?php echo $title; ?> <span class="mif-drive-eta place-right"></span></h1>
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
					<?php foreach( $keyList as $kk => $kv ) : ?>
						<lable><?php echo $kv['alias']; ?></lable>
						<?php if ( !isset($kv['tag']) ): ?>
							<div class="input-control text full-size">
								<input type="text" name="<?php echo $kk; ?>" value="<?php echo $node->$kk; ?>"<?php if ( isset($kv['disabled']) ): ?> disabled="disabled"<?php endif; ?>/>
							</div>
						<?php elseif ( $kv['tag'] == 'textarea' ): ?>
							<div class="input-control textarea full-size">
								<textarea name="<?php echo $kk; ?>"><?php echo $node->$kk; ?></textarea>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<input type="hidden" name="nid" value="<?php echo $node->id; ?>"/>
		<br/>
		<input type="reset" class="button lighten primary" value="重置"/>
		<button type="submit" class="button lighten primary">提交</button>
	</form>
</div>
<script src="<?php echo $baseUrl; ?>/assets/system/editor/kindeditor.js"></script>
<script src="<?php echo $baseUrl; ?>/assets/system/editor/lang/zh_CN.js"></script>
<script>
	KindEditor.ready(function(K){
		window.editor = K.create('#editor_id', {
			width: '100%',
			height: '400px'
		});
	});
	var baseUrl = '<?php echo $baseUrl; ?>';
	$(function(){
		$('#edit').submit(function(){
			$('button[type="submit"]').attr('disabled', true);
			$('button[type="submit"]').addClass('loading-cube');
			$.ajax({
				url: baseUrl + '<?php echo $updateUrl; ?>',
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