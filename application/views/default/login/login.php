<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <title>登录 - BootCMS</title>
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo $baseUrl; ?>/favicon.ico"/>
        <link href="<?php echo $baseUrl; ?>/assets/system/css/metro.css" rel="stylesheet"/>
        <link href="<?php echo $baseUrl; ?>/assets/system/css/metro-icons.css" rel="stylesheet"/>
        <link href="<?php echo $baseUrl; ?>/assets/system/css/metro-responsive.css" rel="stylesheet"/>
        <script src="<?php echo $baseUrl; ?>/assets/system/js/jquery-2.2.1.min.js"></script>
        <script src="<?php echo $baseUrl; ?>/assets/system/js/metro.js"></script>
        <script>var baseUrl = '<?php echo $baseUrl; ?>';</script>
        <style>
            .login-form {
                width: 25rem;
                height: 18.80rem;
                position: fixed;
                left: calc(50% - 12.5rem);
                top: calc(50% - 9.4rem);
                opacity: 0;
                transform: scale(.8);
            }
        </style>
    </head>
    <body class="bg-darkTeal">
        <div class="login-form padding20 block-shadow bg-white">
            <form id="login">
                <h1 class="text-light">登录BootCMS</h1>
                <hr class="thin"/>
                <br/>
                <div class="input-control text full-size" data-role="input">
                    <label for="username">用户名：</label>
                    <input type="text" name="username" id="username"/>
                    <button class="button helper-button clear"><span class="mif-cross"></span></button>
                </div>
                <br/>
                <br/>
                <div class="input-control password full-size" data-role="input">
                    <label for="password">密码：</label>
                    <input type="password" name="password" id="password"/>
                    <button class="button helper-button reveal"><span class="mif-looks"></span></button>
                </div>
                <br/>
                <br/>
                <div class="form-actions">
                    <button type="submit" class="button lighten primary">登录</button>
                    <button type="button" class="button link" onclick="location.href = '<?php echo $baseUrl; ?>'">取消</button>
                </div>
            </form>
        </div>
        <div data-role="dialog" id="dialog" class="padding20 dialog warning" data-type="info">
            <h1></h1>
            <p></p>
        </div>
        <script>
            $(document).ajaxStart(function () {
                $('button[type="submit"]').prop('disabled', true).addClass('loading-cube');
            });
            $(document).ajaxStop(function () {
                $('button[type="submit"]').prop('disabled', false).removeClass('loading-cube');
            });
            $(function () {
                $('.login-form').css({
                    'opacity': 1,
                    'transform': 'scale(1)',
                    'transition': '.5s'
                });
                $('#login').submit(function () {
                    var pass = true;
                    $('input').each(function () {
                        if (!$(this).val()) {
                            $(this).focus();
                            pass = false;
                            return false;
                        }
                    });
                    if (!pass)
                        return false;
                    $.ajax({
                        url: baseUrl + 'system/login',
                        type: 'post',
                        dataType: 'json',
                        data: $('#login').serialize(),
                        success: function (r) {
                            if (r.status === 1) {
                                location.href = baseUrl + 'system';
                            } else {
                                $('#dialog > h1').text(r.info[0]);
                                $('#dialog > p').text(r.info[1]);
                                var dialog = $('#dialog').data('dialog');
                                dialog.open();
                            }
                        }
                    });
                    return false;
                });
            });
        </script>
    </body>
</html>