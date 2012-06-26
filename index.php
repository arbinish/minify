<?php
    if (isset($_POST['action'])) {
        require_once('range.php');
        if (preg_match('/Compress/i', $_POST['action'])) {
            echo json_encode(compressHostRange(trim($_POST['hostlist'])));
        }
        if (preg_match('/Expand/i', $_POST['action'])) {
            echo json_encode(expandHostRange(trim($_POST['hostlist'])));
            error_log('expand ' . print_r(expandHostRange(trim($_POST['hostlist'])), 1));
        }
        error_log(print_r($_POST, 1));
        exit(0);
    }
?>
<!doctype html>
<html lang="en">
<head>
    <title>SEG Tools: Compress &amp; Expand range expressions</title>
    <script type="text/javascript" src="/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="minify.js"></script>
    <link rel="stylesheet" type="text/css" href="minify.css" />
</head>
<body>
    <article id="main">
        <div id="content">
        <textarea name="hostlist" id="hostlist">Enter range expression ...</textarea>
<!--        <button id="btn_cmprs" name="btn_cmprs" class="left">Compress</button>
        <button id="btn_clear" name="btn_clear">Clear</button>
        <button id="btn_exp" name="btn_exp" class="right">Expand</button> -->
            <div id="buttons">
                <ul>
                    <li><a href='#'>Compress</a></li>
                    <li><a href='#'>Clear</a></li>
                    <li><a href='#'>Expand</a></li>
                </ul>                
            </div>
        </div>
    </article>
<!--	<a class="feedback" href="mailto:arbinish@gmail.com">Feedback</a> -->
</body>
</html>
