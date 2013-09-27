<!DOCTYPE html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Octopod Init</title>
    <meta name="description" content="">

    <link rel="stylesheet" href="//yandex.st/bootstrap/3.0.0/css/bootstrap.min.css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin,cyrillic" rel="stylesheet" type="text/css">

    <style>

        .container {
            max-width: none !important;
            width: 980px;
        }

        .container-init {
            width: 550px;
            margin: 0 auto;
            text-align: center;
        }

        .container-init .progress {
            margin-bottom: 0;
        }

        body, h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
            font-family: 'Open Sans';
        }

        .progress {
            height: 30px;
            border-radius: 500px;
        }

        .progress-bar {
            border-radius: 500px;
        }

        .panel {
            margin-top: 150px;
        }
        h1 {
            margin: 0;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="container-init">
    <input id="url" name="url" value="img" type="hidden">
    <input id="offset" name="offset" value="0" type="hidden">

    <div class="panel panel-default">
        <div class="panel-heading"><h1>Octopod Init</h1></div>
        <div class="panel-body">
            <!--<p>Init is a description.</p>-->
            <div class="progress progress-striped active">
                <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
            </div>
        </div>
        <div class="panel-footer">
            <a href="#" class="btn btn-primary btn-lg" id="runScript" data-action="run">Start Init</a>
        </div>
    </div>
</div>

<script src="//yandex.st/jquery/1.10.1/jquery.min.js"></script>

<script src="//yandex.st/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<script type="text/javascript">

    function setCookie (url, offset){
        var ws=new Date();
        if (!offset && !url) {
            ws.setMinutes(10-ws.getMinutes());
        } else {
            ws.setMinutes(10+ws.getMinutes());
        }
        document.cookie="scriptOffsetUrl="+url+";expires="+ws.toGMTString();
        document.cookie="scriptOffsetOffset="+offset+";expires="+ws.toGMTString();
    }

    function getCookie(name) {
        var cookie = " " + document.cookie;
        var search = " " + name + "=";
        var setStr = null;
        var offset = 0;
        var end = 0;
        if (cookie.length > 0) {
            offset = cookie.indexOf(search);
            if (offset != -1) {
                offset += search.length;
                end = cookie.indexOf(";", offset)
                if (end == -1) {
                    end = cookie.length;
                }
                setStr = unescape(cookie.substring(offset, end));
            }
        }
        return(setStr);
    }

    function showProcess (url, success, offset, action) {
        $('.progress-bar').css('width', success * 100 + '%');
        setCookie(url, offset);
        scriptOffset(url, offset, action);
    }

    function scriptOffset (url, offset, action) {
        $.ajax({
            url: "init/scriptoffset",
            type: "POST",
            data: {
                "action":action
                , "url":url
                , "offset":offset
            },
            success: function(data){
                data = $.parseJSON(data);
                if(data.success < 1) {
                    showProcess(url, data.success, data.offset, action);
                } else {
                    // 100% Ready
                    setCookie();
                    $('.progress-bar').css('width','100%').parent().removeClass('active');
                }
            }
        });
    }

    $(document).ready(function() {

        var url = getCookie("scriptOffsetUrl");
        var offset = getCookie("scriptOffsetOffset");

        if (url && url != 'undefined') {
            $('#refreshScript').show();
            $('#runScript').text('Continue');
            $('#url').val(url);
            $('#offset').val(offset);
        }

        $('#runScript').click(function() {
            var action = $('#runScript').data('action');
            var offset = $('#offset').val();
            var url = $('#url').val();

            if ($('#url').val() != getCookie("scriptOffsetUrl")) {
                setCookie();
                scriptOffset(url, 0, action);
            } else {
                scriptOffset(url, offset, action);
            }
            return false;
        });

    });

</script>
</body>
</html>
