<div class="cell auto-size padding20 bg-white" id="cell-content">
    <h1 class="text-light">欢迎使用BootCMS！ <span class="mif-drive-eta place-right"></span></h1>
    <hr class="thin bg-grayLighter">
    <div class="grid">
        <div class="row cells2">
            <div class="cell">
                <div class="panel">
                    <div class="heading">
                        <span class="title">概览</span>
                    </div>
                    <div class="content">
                        <ul class="simple-list">
                            <li><?php echo $articles; ?> 篇文章</li>
                            <li><?php echo $comments; ?> 条评论</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="cell">
                <div class="panel">
                    <div class="heading">
                        <span class="title">系统</span>
                    </div>
                    <div class="content">
                        <dl class="horizontal">
                            <dt>BootCMS版本</dt>
                            <dd>2.0.0</dd>
                            <dt>BootPHP版本</dt>
                            <dd><?php echo 'VERSION'; ?></dd>
                            <dt>服务器</dt>
                            <dd><?php echo $_SERVER['SERVER_SOFTWARE']; ?></dd>
                            <dt>操作系统</dt>
                            <dd><?php echo PHP_OS; ?></dd>
                            <dt>数据库版本</dt>
                            <dd><?php echo $dbVersion; ?></dd>
                            <dt>最大上传</dt>
                            <dd><?php echo ini_get('upload_max_filesize'); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>