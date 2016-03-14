<div class="cell auto-size padding20 bg-white" id="cell-content">
	<h1 class="text-light">网站设置 <span class="mif-drive-eta place-right"></span></h1>
	<hr class="thin bg-grayLighter">
	<form>
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
						<input type="text" name="site_title" value="<?php echo $site->site_title; ?>">
					</div>
					<lable>网站描述</lable>
					<div class="input-control text full-size">
						<input type="text" name="site_description" value="<?php echo $site->site_description; ?>">
					</div>
					<lable>默认关键词</lable>
					<div class="input-control text full-size">
						<input type="text" name="meta_keywords" value="<?php echo $site->meta_keywords; ?>">
					</div>
					<lable>默认描述</lable>
					<div class="input-control textarea full-size">
						<textarea name="meta_description"><?php echo $site->meta_description; ?></textarea>
					</div>
					<lable>管理员 E-mail 地址</lable>
					<div class="input-control text full-size">
						<input type="text" name="admin_email" value="<?php echo $site->admin_email; ?>">
					</div>
				</div>
				<div class="frame" id="frame_2">
					<lable>公司名称</lable>
					<div class="input-control text full-size">
						<input type="text" name="company" value="<?php echo $site->company; ?>">
					</div>
					<lable>电话号码</lable>
					<div class="input-control text full-size">
						<input type="text" name="phone" value="<?php echo $site->phone; ?>">
					</div>
					<lable>地址</lable>
					<div class="input-control textarea full-size">
						<textarea name="address"><?php echo $site->address; ?></textarea>
					</div>
				</div>
				<div class="frame" id="frame_3">
					<lable>日期格式</lable>
					<div class="input-control text full-size">
						<input type="text" name="date_format" value="<?php echo $site->date_format; ?>">
					</div>
					<lable>时区（UTC/GMT）</lable>
					<div class="input-control select full-size">
						<select name="timezone">
							<option value="-12"<?php if ( $site->timezone == -12 ): ?> selected="selected"<?php endif; ?>>-12:00</option>
							<option value="-11"<?php if ( $site->timezone == -11 ): ?> selected="selected"<?php endif; ?>>-11:00</option>
							<option value="-10"<?php if ( $site->timezone == -10 ): ?> selected="selected"<?php endif; ?>>-10:00</option>
							<option value="-9"<?php if ( $site->timezone == -9 ): ?> selected="selected"<?php endif; ?>>-9:00</option>
							<option value="-8"<?php if ( $site->timezone == -8 ): ?> selected="selected"<?php endif; ?>>-8:00</option>
							<option value="-7"<?php if ( $site->timezone == -7 ): ?> selected="selected"<?php endif; ?>>-7:00</option>
							<option value="-6"<?php if ( $site->timezone == -6 ): ?> selected="selected"<?php endif; ?>>-6:00</option>
							<option value="-5"<?php if ( $site->timezone == -5 ): ?> selected="selected"<?php endif; ?>>-5:00</option>
							<option value="-4"<?php if ( $site->timezone == -4 ): ?> selected="selected"<?php endif; ?>>-4:00</option>
							<option value="-3"<?php if ( $site->timezone == -3 ): ?> selected="selected"<?php endif; ?>>-3:00</option>
							<option value="-2"<?php if ( $site->timezone == -2 ): ?> selected="selected"<?php endif; ?>>-2:00</option>
							<option value="-1"<?php if ( $site->timezone == -1 ): ?> selected="selected"<?php endif; ?>>-1:00</option>
							<option value="0"<?php if ( $site->timezone == 0 ): ?> selected="selected"<?php endif; ?>>-0:00</option>
							<option value="1"<?php if ( $site->timezone == 1 ): ?> selected="selected"<?php endif; ?>>+1:00</option>
							<option value="2"<?php if ( $site->timezone == 2 ): ?> selected="selected"<?php endif; ?>>+2:00</option>
							<option value="3"<?php if ( $site->timezone == 3 ): ?> selected="selected"<?php endif; ?>>+3:00</option>
							<option value="4"<?php if ( $site->timezone == 4 ): ?> selected="selected"<?php endif; ?>>+4:00</option>
							<option value="5"<?php if ( $site->timezone == 5 ): ?> selected="selected"<?php endif; ?>>+5:00</option>
							<option value="6"<?php if ( $site->timezone == 6 ): ?> selected="selected"<?php endif; ?>>+6:00</option>
							<option value="7"<?php if ( $site->timezone == 7 ): ?> selected="selected"<?php endif; ?>>+7:00</option>
							<option value="8"<?php if ( $site->timezone == 8 ): ?> selected="selected"<?php endif; ?>>+8:00</option>
							<option value="9"<?php if ( $site->timezone == 9 ): ?> selected="selected"<?php endif; ?>>+9:00</option>
							<option value="10"<?php if ( $site->timezone == 10 ): ?> selected="selected"<?php endif; ?>>+10:00</option>
							<option value="11"<?php if ( $site->timezone == 11 ): ?> selected="selected"<?php endif; ?>>+11:00</option>
							<option value="12"<?php if ( $site->timezone == 12 ): ?> selected="selected"<?php endif; ?>>+12:00</option>
						</select>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="sid" value="<?php echo $site->id; ?>"/>
		<br/>
		<input type="reset" class="button lighten primary" value="重置"/>
		<button type="submit" class="button lighten primary">提交</button>
	</form>
</div>
<script>
	var baseUrl = '<?php echo $baseUrl; ?>';
	$(function(){
		$('form').submit(function(){
			$('button[type="submit"]').attr('disabled', true);
			$('button[type="submit"]').addClass('loading-cube');
			$.post(baseUrl + '/admin/index/settings', $('form').serialize(), function(r){
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
			}, 'json');
			return false;
		});
	});
</script>