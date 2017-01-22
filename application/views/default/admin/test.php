<ul>
    <?php foreach ($linkages as $node): ?>
        <li><?php echo $node->name; ?></li>
    <?php endforeach; ?>
</ul>
<?php echo \Bootphp\View::factory(SYS_PATH.'/profiler/Views/stats.html.php'); ?>