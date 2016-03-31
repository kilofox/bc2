<p class="pagination">
	<?php if ( $first_page !== FALSE ): ?>
		<a href="<?php echo \Bootphp\HTML::chars($page->url($first_page)) ?>" rel="first">First</a>
	<?php else: ?>
		First
	<?php endif ?>
	<?php if ( $previous_page !== FALSE ): ?>
		<a href="<?php echo \Bootphp\HTML::chars($page->url($previous_page)) ?>" rel="prev">Previous</a>
	<?php else: ?>
		Previous
	<?php endif ?>
	<?php for( $i = 1; $i <= $total_pages; $i++ ): ?>

		<?php if ( $i == $current_page ): ?>
			<strong><?php echo $i ?></strong>
		<?php else: ?>
			<a href="<?php echo \Bootphp\HTML::chars($page->url($i)) ?>"><?php echo $i ?></a>
		<?php endif ?>
	<?php endfor ?>
	<?php if ( $next_page !== FALSE ): ?>
		<a href="<?php echo \Bootphp\HTML::chars($page->url($next_page)) ?>" rel="next">Next</a>
	<?php else: ?>
		Next
	<?php endif ?>
	<?php if ( $last_page !== FALSE ): ?>
		<a href="<?php echo \Bootphp\HTML::chars($page->url($last_page)) ?>" rel="last">Last</a>
	<?php else: ?>
		Last
	<?php endif ?>
</p>