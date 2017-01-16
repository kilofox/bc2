<div class="cell auto-size padding20 bg-white" id="cell-content">
    <h1 class="text-light">用户 <span class="mif-drive-eta place-right"></span></h1>
    <hr class="thin bg-grayLighter">
    <button class="button success" onclick="pushMessage('success')"><span class="mif-play"></span> 开始</button>
    <button class="button warning" onclick="pushMessage('warning')"><span class="mif-loop2"></span> 重启</button>
    <hr class="thin bg-grayLighter">
    <div class="dataTables_length">
        <label>显示 <select name="DataTables_Table_0_length" class="">
                <option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select> 条
        </label>
    </div>
    <div class="dataTables_filter">
        <label>搜索：<input type="search" class="" placeholder="" aria-controls="DataTables_Table_0"></label>
    </div>
    <table class="dataTable border bordered">
        <thead>
            <tr>
                <td></td>
                <td class="sortable-column sort-asc" style="width: 100px">ID</td>
                <td class="sortable-column">昵称</td>
                <td class="sortable-column">E-mail</td>
                <td class="sortable-column">注册时间</td>
                <td class="sortable-column align-center">状态</td>
                <td class="align-center">操作</td>
            </tr>
        </thead>
        </thead>
        <tbody>
            <?php foreach ($nodes as $node): ?>
                <tr>
                    <td class="align-center">
                        <label class="input-control checkbox small-check no-margin">
                            <input type="checkbox">
                            <span class="check"></span>
                        </label>
                    </td>
                    <td><?php echo $node->id; ?></td>
                    <td><?php echo $node->nickname; ?></td>
                    <td><?php echo $node->email; ?></td>
                    <td><?php echo $node->created; ?></td>
                    <td class="align-center"></td>
                    <td class="align-center">
                        <a href="<?php echo $baseUrl; ?>/admin/user/<?php echo $node->id; ?>/edit">编辑</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="dataTables_info">显示第 1 到第 10 条，共 11 条</div>
    <div class="dataTables_paginate paging_simple_numbers">
        <a class="paginate_button previous disabled" tabindex="0">上一页</a>
        <span>
            <a class="paginate_button current" tabindex="0">1</a>
            <a class="paginate_button" tabindex="0">2</a>
        </span>
        <a class="paginate_button next" tabindex="0">下一页</a>
    </div>
</div>

