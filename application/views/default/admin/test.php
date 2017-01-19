<ul>
    <?php foreach ($linkages as $node): ?>
        <li><?php echo $node->name; ?></li>
    <?php endforeach; ?>
</ul>
<? echo \Bootphp\View::factory('profiler/stats'); ?>