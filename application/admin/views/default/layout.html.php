<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<meta name="robots" content="noindex, nofollow"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
		<title><?php echo $baseUrl; ?></title>
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo $baseUrl; ?>/favicon.ico"/>
		<link href="<?php echo $baseUrl; ?>/assets/admin/css/metro.css" rel="stylesheet"/>
		<link href="<?php echo $baseUrl; ?>/assets/admin/css/metro-icons.css" rel="stylesheet"/>
		<link href="<?php echo $baseUrl; ?>/assets/admin/css/metro-responsive.css" rel="stylesheet"/>
		<script src="<?php echo $baseUrl; ?>/assets/admin/js/jquery-2.2.1.min.js"></script>
		<script>window.METRO_CURRENT_LOCALE = 'znCN';</script>
		<script src="<?php echo $baseUrl; ?>/assets/admin/js/metro.js"></script>
		<style>
			html, body {
				height: 100%;
			}
			body {
			}
			.page-content {
				padding-top: 3.125rem;
				min-height: 100%;
				height: 100%;
			}
			.table .input-control.checkbox {
				line-height: 1;
				min-height: 0;
				height: auto;
			}
			@media screen and (max-width: 800px){
				#cell-sidebar {
					flex-basis: 52px;
				}
				#cell-content {
					flex-basis: calc(100% - 52px);
				}
			}
		</style>
		<script>
			function pushMessage(t){
				var mes = 'BootCMS 2.0|正在开发中……';
				$.Notify({
					caption: mes.split("|")[0],
					content: mes.split("|")[1],
					type: t
				});
				return false;
			}
			var baseUrl = '<?php echo $baseUrl; ?>';
			$(function(){
				$('.sidebar').on('click', 'li', function(){
					if ( !$(this).hasClass('active') ){
						$('.sidebar li').removeClass('active');
						$(this).addClass('active');
					}
				});
			});
		</script>
	</head>
	<body class="bg-steel">
		<div class="app-bar fixed-top darcula" data-role="appbar">
			<a href="<?php echo $baseUrl; ?>/admin" class="app-bar-element branding">网站管理</a>
			<span class="app-bar-divider"></span>
			<ul class="app-bar-menu">
				<?php foreach( $menus as $k => $menu ): ?>
					<li<?php if ( $k == $application ): ?> class="active"<?php endif; ?>><a href="<?php echo $baseUrl; ?>/<?php echo $k; ?>/<?php echo $menu['controller']; ?>/<?php echo $menu['action']; ?>"><?php echo $menu['name']; ?></a></li>
				<?php endforeach; ?>
				<li>
					<a href="javascript:void(0);" class="dropdown-toggle">帮助</a>
					<ul class="d-menu" data-role="dropdown">
						<li><a href="javascript:void(0);" onclick="pushMessage('info')">聊天</a></li>
						<li><a href="http://www.kilofox.net/forums" target="_blank">社区支持</a></li>
						<li class="divider"></li>
						<li><a href="javascript:void(0);" onclick="pushMessage('info')">关于</a></li>
					</ul>
				</li>
			</ul>
			<ul class="app-bar-menu place-right">
				<li>
					<a class="dropdown-toggle"><span class="mif-cog"></span> <?php echo $user->nickname; ?></a>
					<ul class="d-menu place-right" data-role="dropdown">
						<li><a href="<?php echo $baseUrl; ?>/admin/public/edit_user">个人资料</a></li>
						<li><a href="javascript:void(0);" onclick="pushMessage('info')">安全</a></li>
						<li><a href="<?php echo $baseUrl; ?>/admin/public/logout">退出</a></li>
					</ul>
				</li>
			</ul>
		</div>
		<div class="page-content">
			<div class="flex-grid" style="height: 100%;">
				<div class="row" style="min-height: 100%">
					<div class="cell size-x200" id="cell-sidebar" style="background-color: #71b1d1;">
						<ul class="sidebar">
							<?php foreach( $menus[$application]['child'] as $k => $menu ): ?>
								<li<?php if ( $k == $tab ): ?> class="active"<?php endif; ?>>
									<a href="<?php echo $baseUrl; ?>/<?php echo $application; ?>/<?php echo $menus[$application]['controller']; ?>/<?php echo $k; ?>">
										<span class="mif-apps icon"></span>
										<span class="title"><?php echo $menu['name']; ?></span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php echo $yield; ?>
				</div><?php \Bootphp\Profiler\Profiler::view(); ?>
			</div>
		</div>
		<div data-role="dialog" id="dialog_s" class="padding20 dialog" data-type="success">
			<h1>BootCMS消息</h1>
			<p></p>
		</div>
		<div data-role="dialog" id="dialog_i" class="padding20 dialog" data-type="info">
			<h1>BootCMS消息</h1>
			<p></p>
		</div>
	</body>
</html>