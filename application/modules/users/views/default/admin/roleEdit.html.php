<div class="cell auto-size padding20 bg-white" id="cell-content">
    <h1 class="text-light">编辑角色 <span class="mif-drive-eta place-right"></span></h1>
    <hr class="thin bg-grayLighter">
    <form id="edit">
        <div class="tabcontrol" data-role="tabcontrol">
            <ul class="tabs">
                <li class="active"><a href="#frame_1">编辑角色</a></li>
            </ul>
            <div class="frames">
                <div class="frame" id="frame_1">
                    <lable>角色名</lable>
                    <div class="input-control text full-size">
                        <input type="text" name="name" value="<?php echo $node->name; ?>"/>
                    </div>
                    <lable>描述</lable>
                    <div class="input-control text full-size">
                        <input type="text" name="description" value="<?php echo $node->description; ?>"/>
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
                url: baseUrl + '/users/admin/role/<?php echo $node->id; ?>/edit',
                type: 'put',
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