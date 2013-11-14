<?php

$log_types = Config::get("logTypes");

$levels = array(
    'debug',
    'info',
    'notice',
    'warning',
    'error',
    'critical',
    'alert',
    'emergency',
);

$logsPath = App::path('log');
//print App::make('uri')->create('app/generated/images/');
function pretty_filesize($file)
{
    $size = filesize($file);
    if ($size < 1024) {
        $size = $size . " Bytes";
    } elseif (($size < 1048576) && ($size > 1023)) {
        $size = round($size / 1024, 1) . " KB";
    } elseif (($size < 1073741824) && ($size > 1048575)) {
        $size = round($size / 1048576, 1) . " MB";
    } else {
        $size = round($size / 1073741824, 1) . " GB";
    }
    return $size;
}

$availableDates = array();

foreach (glob($logsPath . "/**.**.****", GLOB_ONLYDIR) as $filename) {
    list(, $availableDate) = explode(Config::get('paths.log') . "/", $filename);
    $availableDates[] = '"' . $availableDate . '"';
}

$availableDatesString = "[" . implode(",", $availableDates) . "]";

$selectedDate = (isset ($_GET["date"]) ? $_GET["date"] : date('d.m.Y', time()));

//$dirArray = glob('logs/' . $selectedDate . '/' . $filterString . '*.log', GLOB_BRACE);

?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/html">

<head>
    <meta charset="UTF-8">
    <title>Octopod log panel</title>

    <style type="text/css">
        * {
            padding: 0;
            margin: 0;
        }

        body {
            color: #333;
            font: 14px Sans-Serif;
            padding: 50px;
            background: #eee;
        }

        h1 {
            text-align: center;
            padding: 20px 0 12px 0;
            margin: 0;
        }

        h2 {
            font-size: 16px;
            text-align: center;
            padding: 0 0 12px 0;
        }

        #container {
            box-shadow: 0 5px 10px -5px rgba(0, 0, 0, 0.5);
            position: relative;
            background: white;
        }

        table {
            background-color: #F3F3F3;
            border-collapse: collapse;
            width: 100%;
            margin: 15px 0;
        }

        th {
            background-color: #FE4902;
            color: #FFF;
            cursor: pointer;
            padding: 5px 10px;
        }

        th small {
            font-size: 9px;
        }

        td, th {
            text-align: left;
        }

        a {
            text-decoration: none;
        }

        td a {
            color: #663300;
            display: block;
            padding: 5px 10px;
        }

        th a {
            padding-left: 0
        }

        td:first-of-type a {
            padding-left: 35px;
        }

        th:first-of-type {
            padding-left: 35px;
        }

        td:not(:first-of-type) a {
            background-image: none !important;
        }

        tr:nth-of-type(odd) {
            background-color: #E6E6E6;
        }

        tr:hover td {
            background-color: #CACACA;
        }

        tr:hover td a {
            color: #000;
        }

    </style>

    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/>
    <script src="//tech.octopod.com/import/sorttable.js"></script>
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>


    <script>
        var availableDates = <?=$availableDatesString?>;


        function pad(num, size) {
            return ('000000000' + num).substr(-size);
        }

        function available(date) {
            dmy = pad(date.getDate(), 2) + "." + pad((date.getMonth() + 1), 2) + "." + date.getFullYear();
            //console.log(dmy + ' : ' + ($.inArray(dmy, availableDates)));
            if ($.inArray(dmy, availableDates) != -1) {
                return [true, "", "Available"];
            } else {
                return [false, "", "unAvailable"];
            }
        }

        $(function () {
            $("#date").datepicker({
                defaultDate: "-1m",
                changeMonth: true,
                numberOfMonths: 1,
                dateFormat: "dd.mm.yy",
                beforeShowDay: available
            });
        });

        // $('#date').datepicker({  });


        $(document).ready(function () {
            $('.all').click(function () {
                var $checkboxes = $(this).parent().parent().find('input[type=checkbox]');
                $checkboxes.prop('checked', $(this).is(':checked'));
            });
        });

    </script>
</head>

<body>


<div id="container">
    <h1>Octopod log panel</h1>

    <form method="get" action="">
        <input type="hidden" name="form_submitted" value="true"/>


        <div style="width: 100%; overflow:hidden; text-align: center;">
            <div style="width: 20%; min-width: 200px; float: left; margin: 25px; background-color: #EEEEEE; text-align: left">
                <div style="margin: 25px;margin-top: 10px">
                    <label for="date"><b>Select date: </b></label><br><br>
                    <input type="text" id="date" name="date" value="<?= $selectedDate ?>"/>
                </div>
                <input type="submit" style="border:thin #EEEEEE; cursor:pointer; font-family:Verdana,Arial,Helvetica; font-size:22px; font-weight:normal; padding:4px; margin: 25px" value="Show logs">
            </div>
            <div class="check" style="width: 40%; min-width: 200px; float: left; margin: 25px; background-color: #EEEEEE; text-align: left">
                <h2 style="margin-top: 10px">Log types</h2>

                <label style="float: left; width: 200px; margin: 3px; margin-left: 10px"><input type="checkbox" class="all" checked="checked">
                    <b>Check/Uncheck all</b></input></label><br/><br/>
                <?php
                foreach ($log_types as $type => $typeArray) {
                    ?>

                    <label style="float: left; width: 200px; margin: 3px; margin-left: 10px"><input type="checkbox" name="type[<?= $type ?>]" <?php if (isset($_GET['type'][$type]) || !isset($_GET['form_submitted'])) echo "checked='checked'"; ?>>
                        <?= $type ?></input></label>

                <?php
                }
                ?>
            </div>

            <div style="width: 20%; min-width: 200px; float: left; margin: 25px; background-color: #EEEEEE; text-align: left">
                <h2 style="margin-top: 10px">Log levels</h2>

                <label style="float: left; width: 200px; margin: 3px; margin-left: 10px"><input type="checkbox" class="all" checked="checked">
                    <b>Check/Uncheck all</b></input></label><br/><br/>
                <?php
                foreach ($levels as $level) {
                    ?>

                    <label style="float: left; width: 200px; margin: 3px; margin-left: 10px"><input type="checkbox" name="level[<?= $level ?>]" <?php if (isset($_GET['level'][$level]) || !isset($_GET['form_submitted'])) echo "checked='checked'"; ?>>
                        <?= $level ?></input></label>

                <?php
                }
                ?>
            </div>
        </div>

    </form>

    <table class="sortable" width="100%">
        <thead>
        <tr>
            <th width="100px">Level</th>
            <!--            <th>File</th>-->
            <th width="100px">Type</th>
            <th>Log message</th>
            <!--            <th>Size</th>-->
            <th width="180px">Date Created</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // Adds pretty filesizes

        $filterWords = array();

        if (isset($_GET['form_submitted'])) {

            foreach ($log_types as $type => $typeArray) {
                if (isset($_GET['type'][$type])) {
                    $filterWords[] = $type;
                }
            }
            $filterString = "{" . implode(",", $filterWords) . "}";
        } else
            $filterString = "*";

        if (isset($_GET['form_submitted'])) {
            foreach ($levels as $logLevel) {
                if (isset($_GET['level'][$logLevel])) {
                    $filterLevels[] = $logLevel;
                }
            }
            $filterLevelString = "{" . implode(",", $filterLevels) . "}";
        } else
            $filterLevelString = "*";

        $filesListMask = $logsPath . '/' . $selectedDate . '/' . $filterLevelString . '_' . $filterString . '*.log';

        $dirArray = glob($filesListMask, GLOB_BRACE);

        usort($dirArray, function ($file_1, $file_2) {
            $file_1 = filectime($file_1);
            $file_2 = filectime($file_2);
            if ($file_1 == $file_2) {
                return 0;
            }
            return $file_1 < $file_2 ? 1 : -1;
        });

        // Counts elements in array
        $indexCount = count($dirArray);

        if ($indexCount) {
// Loops through the array of files
            for ($index = 0; $index < $indexCount; $index++) {

                preg_match('/\/(([A-Za-z]*?)_([A-Za-z]*?)_\d+\.log)/', $dirArray[$index], $res);

                $name = $res[1];
                $level = $res[2];
                $extn = $res[3];

                $logInfo = include($dirArray[$index]);

                $message = htmlentities(substr($logInfo['log'], 0, 200) . "...");

                // Gets Date Modified
                $modtime = date("H:i:s (M j Y)", filectime($dirArray[$index]));
                $timekey = date("YmdHis", filectime($dirArray[$index]));


                // Gets and cleans up file size
                $size = pretty_filesize($dirArray[$index]);
                $sizekey = filesize($dirArray[$index]);


                // Output
                echo("
    <tr>
        <td><a href='logFile?name=$selectedDate/$name'>$level</a></td>
        <!--td><a href='./$name' class='name'>$name</a></td-->
        <td><a href='logFile?name=$selectedDate/$name' class='name'>$extn</a></td>
        <td><a href='logFile?name=$selectedDate/$name' class='name'>$message</a></td>
        <!--td sorttable_customkey='$sizekey'><a href='logFile?name=$selectedDate/$name'>$size</a></td-->
        <td sorttable_customkey='$timekey'><a href='logFile?name=$selectedDate/$name'>$modtime</a></td>
    </tr>");

            }
        } else {
            ?>
            <tr>
            <td colspan="4"><br>

                <h2>No logs for selected types and levels found on date <?= $selectedDate ?></h2></td></tr><?
        }
        ?>

        </tbody>
    </table>
</div>
</body>
</html>
