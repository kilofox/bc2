<ul>
    <? foreach ($linkages as $node): ?>
        <li><?php echo isset($user->username) ? $user->username : $user->title; ?></li>
    <? endforeach; ?>
</ul>
<?php echo \Bootphp\View::factory(SYS_PATH.'/profiler/Views/stats.html.php'); ?>