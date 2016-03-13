<?php
use Bootphp\Profiler\Profiler;
use Bootphp\Html;
$groupStats = Profiler::groupStats();
$groupCols = array('最小' => 'min', '最大' => 'max', '平均' => 'average', '总计' => 'total');
$applicationCols = array('min', 'max', 'average', 'current');
?>
<style>
	.bootphp table.profiler { width: 99%; margin: 0 auto 1em; border-collapse: collapse; }
	.bootphp table.profiler th,
	.bootphp table.profiler td { padding: 0.2em 0.4em; background: #ffffff; border: solid 1px #999; border-width: 1px 0; text-align: left; font-weight: normal; font-size: 1em; color: #111; vertical-align: top; text-align: right; }
	.bootphp table.profiler th.name { text-align: left; }
	.bootphp table.profiler tr.group th { font-size: 1.4em; background: #222222; color: #eee; border-color: #222222; vertical-align: middle;}
	.bootphp table.profiler tr.group td { background: #222222; color: #777777; border-color: #222222; }
	.bootphp table.profiler tr.group td.time { padding-bottom: 0; }
	.bootphp table.profiler tr.headers th { background: #ddd; color: #777777; }
	.bootphp table.profiler tr.mark th.name { width: 40%; font-size: 1.2em; background: #ffffff; vertical-align: middle; }
	.bootphp table.profiler tr.mark td { padding: 0; }
	.bootphp table.profiler tr.mark.final td { padding: 0.2em 0.4em; }
	.bootphp table.profiler tr.mark td > div { position: relative; padding: 0.2em 0.4em; }
	.bootphp table.profiler tr.mark td div.value { position: relative; z-index: 2; }
	.bootphp table.profiler tr.mark td div.graph { position: absolute; top: 0; bottom: 0; right: 0; left: 100%; background: #71bdf0; z-index: 1; }
	.bootphp table.profiler tr.mark.memory td div.graph { background: #acd4f0; }
	.bootphp table.profiler tr.mark td.current { background: #eddecc; }
	.bootphp table.profiler tr.mark td.min { background: #d2f1cb; }
	.bootphp table.profiler tr.mark td.max { background: #ead3cb; }
	.bootphp table.profiler tr.mark td.average { background: #ddd; }
	.bootphp table.profiler tr.mark td.total { background: #d0e3f0; }
	.bootphp table.profiler tr.time td { border-bottom: 0; font-weight: bold; }
	.bootphp table.profiler tr.memory td { border-top: 0; }
	.bootphp table.profiler tr.final th.name { background: #222222; color: #ffffff; }
	.bootphp table.profiler abbr { border: 0; color: #777777; font-weight: normal; }
	.bootphp table.profiler:hover tr.group td { color: #cccccc; }
	.bootphp table.profiler:hover tr.mark td div.graph { background: #1197f0; }
	.bootphp table.profiler:hover tr.mark.memory td div.graph { background: #7cc1f0; }
</style>
<div class="bootphp">
	<?php foreach( Profiler::groups() as $group => $benchmarks ): ?>
		<table class="profiler">
			<tr class="group">
				<th class="name" rowspan="2"><?php echo ucfirst($group); ?></th>
				<td class="time" colspan="4"><?php echo number_format($groupStats[$group]['total']['time'], 4); ?> <abbr>秒</abbr></td>
			</tr>
			<tr class="group">
				<td class="memory" colspan="4"><?php echo number_format($groupStats[$group]['total']['memory'] / 1024, 2); ?> <abbr>KB</abbr></td>
			</tr>
			<tr class="headers">
				<th class="name">基准</th>
				<?php foreach( $groupCols as $alias => $key ): ?>
					<th class="<?php echo $key; ?>"><?php echo ucfirst($alias); ?></th>
				<?php endforeach ?>
			</tr>
			<?php foreach( $benchmarks as $name => $tokens ): ?>
				<tr class="mark time">
					<?php $stats = Profiler::stats($tokens) ?>
					<th class="name" rowspan="2" scope="rowgroup"><?php echo Html::chars($name), '（', count($tokens), '）'; ?></th>
					<?php foreach( $groupCols as $key ): ?>
						<td class="<?php echo $key ?>">
							<div>
								<div class="value"><?php echo number_format($stats[$key]['time'], 4); ?> <abbr>秒</abbr></div>
								<?php if ( $key === 'total' ): ?>
									<div class="graph" style="left: <?php echo max(0, 100 - $stats[$key]['time'] / $groupStats[$group]['max']['time'] * 100); ?>%"></div>
								<?php endif ?>
							</div>
						</td>
					<?php endforeach ?>
				</tr>
				<tr class="mark memory">
					<?php foreach( $groupCols as $key ): ?>
						<td class="<?php echo $key ?>">
							<div>
								<div class="value"><?php echo number_format($stats[$key]['memory'] / 1024, 2) ?> <abbr>KB</abbr></div>
								<?php if ( $key === 'total' ): ?>
									<div class="graph" style="left: <?php echo max(0, 100 - $stats[$key]['memory'] / $groupStats[$group]['max']['memory'] * 100); ?>%"></div>
								<?php endif ?>
							</div>
						</td>
					<?php endforeach ?>
				</tr>
			<?php endforeach ?>
		</table>
	<?php endforeach ?>
	<table class="profiler">
		<?php $stats = Profiler::application(); ?>
		<tr class="final mark time">
			<th class="name" rowspan="2" scope="rowgroup">应用执行（<?php echo $stats['count']; ?>）</th>
			<?php foreach( $applicationCols as $key ): ?>
				<td class="<?php echo $key ?>"><?php echo number_format($stats[$key]['time'], 4); ?> <abbr>秒</abbr></td>
			<?php endforeach ?>
		</tr>
		<tr class="final mark memory">
			<?php foreach( $applicationCols as $key ): ?>
				<td class="<?php echo $key ?>"><?php echo number_format($stats[$key]['memory'] / 1024, 2); ?> <abbr>KB</abbr></td>
			<?php endforeach ?>
		</tr>
	</table>
</div>