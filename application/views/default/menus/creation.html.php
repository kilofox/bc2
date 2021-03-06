<div class="cell auto-size padding20 bg-white" id="cell-content">
    <h1 class="text-light">编辑菜单 <span class="mif-drive-eta place-right"></span></h1>
    <hr class="thin bg-grayLighter">
    <form id="edit">
        <div class="tabcontrol" data-role="tabcontrol">
            <ul class="tabs">
                <li class="active"><a href="#frame_1">常规设置</a></li>
            </ul>
            <div class="frames">
                <div class="frame" id="frame_1">
                    <lable>名称</lable>
                    <div class="input-control text full-size">
                        <input type="text" name="title"/>
                    </div>
                    <lable>图标</lable>
                    <div class="input-control text full-size">
                        <input type="text" name="icon"/>
                    </div>
                    <lable>上级菜单</lable>
                    <div class="input-control text full-size">
                        <input type="text" name="parent_id"/>
                    </div>
                    <lable>排序</lable>
                    <div class="input-control textarea full-size">
                        <input type="text" name="sort"/>
                    </div>
                    <lable>应用</lable>
                    <div class="input-control textarea full-size">
                        <input type="text" name="application"/>
                    </div>
                    <lable>控制器</lable>
                    <div class="input-control textarea full-size">
                        <input type="text" name="controller"/>
                    </div>
                    <lable>动作</lable>
                    <div class="input-control textarea full-size">
                        <input type="text" name="action"/>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <input type="reset" class="button lighten primary" value="重置"/>
        <button type="submit" class="button lighten primary">提交</button>
    </form>
</div>
<script>
    var baseUrl = '<?php echo $baseUrl; ?>';
    $(function () {
        $('#edit').submit(function () {
            $('button[type="submit"]').attr('disabled', true);
            $('button[type="submit"]').addClass('loading-cube');
            $.ajax({
                url: baseUrl + '/system/menus',
                type: 'post',
                dataType: 'json',
                data: $('#edit').serialize(),
                success: function (r) {
                    if (r.status === 1) {
                        $('button[type="submit"]').attr('disabled', false);
                        $('button[type="submit"]').removeClass('loading-cube');
                        $('#dialog_s > p').text(r.info);
                        var dialog = $('#dialog_s').data('dialog');
                        dialog.open();
                    } else {
                        $('button[type="submit"]').attr('disabled', false);
                        $('button[type="submit"]').removeClass('loading-cube');
                        $('#dialog_i > p').text(r.info);
                        var dialog = $('#dialog_i').data('dialog');
                        dialog.open();
                    }
                }
            });
            return false;
        });
    });
</script>