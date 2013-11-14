<?php

$logsPath = App::path('log');
$log_info = include($logsPath . "/" . $_GET['name']);

?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/html">

<head>
    <meta charset="UTF-8">
    <title>Octopod log panel</title>

    <style type="text/css">
        body .syntaxhighlighter .line {
            white-space: pre-wrap !important; /* make code wrap */
        }

    </style>

    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/>
    <link rel="stylesheet" href="//tech.octopod.com/import/logger/logger.css"/>
    <script src="//tech.octopod.com/import/logger/sorttable.js"></script>
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

    <script type="text/javascript" src="//tech.octopod.com/import/logger/highlighter/scripts/shCore.js"></script>
    <script type="text/javascript" src="//tech.octopod.com/import/logger/highlighter/scripts/shBrushXml.js"></script>
    <link type="text/css" rel="stylesheet" href="//tech.octopod.com/import/logger/highlighter/styles/shCoreDefault.css"/>
    <script type="text/javascript">
        SyntaxHighlighter.all();

        var wrap = function () {
            var elems = document.getElementsByClassName('syntaxhighlighter');
            for (var j = 0; j < elems.length; ++j) {
                var sh = elems[j];
                var gLines = sh.getElementsByClassName('gutter')[0].getElementsByClassName('line');
                var cLines = sh.getElementsByClassName('code')[0].getElementsByClassName('line');
                var stand = 15;
                for (var i = 0; i < gLines.length; ++i) {
                    var h = $(cLines[i]).height();
                    if (h != stand) {
                        console.log(i);
                        gLines[i].setAttribute('style', 'height: ' + h + 'px !important;');
                    }
                }
            }
        };
        var whenReady = function () {
            if ($('.syntaxhighlighter').length === 0) {
                setTimeout(whenReady, 800);
            } else {
                wrap();
            }
        };

        whenReady();
    </script>

</head>

<body>

<div id="dialog" title="Dialog Title" style="width: 900px; height: 400px"><pre class="brush: xml; ">
        asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd
        asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd
        asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd asd

    </pre></div>

<script>
    $("#dialog").dialog({ autoOpen: false, width: 900, height: 600 });
    $( "#opener" ).click(function() {
        $( "#dialog" ).dialog( "open" );
    });
</script>

<a href="log"><< Back to log list</a>

<div class="container">

    <h2>Request info</h2>

    <b>Request uri:</b>
    <input style="width: 300px" id="uri" value="<?= $log_info['requestUri'] ?>"> <br><br>

    <b>Octopod request body:</b><br/>
    <textarea id="body" style="width: 400px; height: 250px"><?= $log_info['requestRawData'] ?></textarea>
    <br><br>
    <input type="button" onclick="$.post($('#uri').val(), $('#body').val(), function(data){ $( '#dialog' ).html('<script type=\'syntaxhighlighter\' class=\'syntaxhighlight brush: xml\'><![CDATA[' + data + '</script>'); SyntaxHighlighter.highlight(); $( '#dialog' ).dialog( 'open' ); whenReady(); });" value="Run request from browser" />

    <!--alert(data);-->
    <br>
    <br>
</div>
<br><br>
<div class="container">
    <br><br>
    <h2>Log text</h2> <script type='syntaxhighlighter' class='syntaxhighlight brush: xml'><![CDATA[<?= $log_info['log'] ?></script>
    <br><br>

</div>
</body>
</html>
