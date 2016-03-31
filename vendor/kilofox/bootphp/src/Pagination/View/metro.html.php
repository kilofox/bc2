<div class="dataTables_info">显示第 <?php echo $current_first_item ?> 到第 <?php echo $current_last_item ?> 条，共 <?php echo $total_items ?> 条</div>
<div class="dataTables_paginate paging_simple_numbers">
	<?php if ( $first_page !== false ): ?>
		<a href="<?php echo \Bootphp\HTML::chars($page->url($first_page)) ?>" class="paginate_button previous" tabindex="0">第一页</a>
	<?php else: ?>
		<a class="paginate_button previous disabled" tabindex="0">第一页</a>
	<?php endif ?>
	<?php if ( $previous_page !== false ): ?>
		<a class="paginate_button previous" tabindex="0" href="<?php echo \Bootphp\HTML::chars($page->url($previous_page)) ?>">上一页</a>
	<?php else: ?>
		<a class="paginate_button previous disabled" tabindex="0">上一页</a>
	<?php endif ?>
	<span>
		<?php for( $i = 1; $i <= $total_pages; $i++ ): ?>
			<?php if ( $i == $current_page ): ?>
				<a class="paginate_button current" tabindex="0"><?php echo $i ?></a>
			<?php else: ?>
				<a href="<?php echo \Bootphp\HTML::chars($page->url($i)) ?>" class="paginate_button" tabindex="0"><?php echo $i ?></a>
			<?php endif ?>
		<?php endfor; ?>
	</span>
	<?php if ( $next_page !== false ): ?>
		<a href="<?php echo \Bootphp\HTML::chars($page->url($next_page)) ?>" class="paginate_button next" tabindex="0">下一页</a>
	<?php else: ?>
		<a class="paginate_button next disabled" tabindex="0">下一页</a>
	<?php endif ?>
	<?php if ( $last_page !== false ): ?>
		<a href="<?php echo \Bootphp\HTML::chars($page->url($last_page)) ?>" class="paginate_button next" tabindex="0">最后一页</a>
	<?php else: ?>
		<a class="paginate_button next disabled" tabindex="0">最后一页</a>
	<?php endif ?>
</div>