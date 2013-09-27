
<html>
<head>
    <title>Octopod launcher</title>
    <script type="text/javascript" src="http://yandex.st/jquery/1.7.1/jquery.min.js"></script>
    <script type="text/javascript" src="scriptoffset.js"></script>
    <link rel="stylesheet" type="text/css" href="scriptoffset.css">
</head>
<body>
<div class="form">
    <input id="url" name="url" value="img" type="hidden">
    <input id="offset" name="offset" value="0" type="hidden">

    <div class="progress" style="display: inline-block;">
        <div class="bar" style="width: 0%;"></div>
    </div>

    <a href="#" id="runScript" class="btn" data-action="run">Deploy application</a>
    <a href="#" id="refreshScript" class="btn" style="display: none;">Redeploy application</a>
</div>
</body>
</html>