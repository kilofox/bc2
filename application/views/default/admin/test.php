<ul>
    <? foreach ($linkages as $node): ?>
        <li><?php echo $user->username; ?></li>
    <? endforeach; ?>
</ul>
<?php echo \Bootphp\View::factory(SYS_PATH.'/profiler/Views/stats.html.php'); ?>