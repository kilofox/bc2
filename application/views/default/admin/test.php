<ul>
    <? foreach ($linkages as $node): ?>
        <li><?php echo isset($user->username) ? $user->username : $user->title; ?></li>
    <? endforeach; ?>
</ul>
<?php echo new \Bootphp\View(SYS_PATH.'/profiler/Views/stats.html.php'); ?>