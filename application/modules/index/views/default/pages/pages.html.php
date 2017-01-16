<div id="site_content">
    <div id="content">
        <h1>产品列表</h1>
        <?php if (count($products)): ?>
            <?php foreach ($products as $node): ?>
                <h2><a href="<?php echo $baseUrl; ?>product/entry/<?php echo $node->id; ?>/"><?php echo $node->node_title; ?></a></h2>
                <?php
                if (mb_strlen($node->node_content) > 56)
                    $node->node_content = mb_substr($node->node_content, 0, 56) . '...';
                ?>
                <p><?php echo $node->node_content; ?></p>
            <?php endforeach; ?>
        <?php else: ?>
            <p>该分类下暂无产品</p>
        <?php endif; ?>
    </div>
</div>
