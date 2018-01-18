<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title></title>
  <link href="/assets/css/tmt.css" type="text/css" rel="stylesheet">
</head>
<body>
<div id="div_data" style="display: none"  data-username="{$username}" data-times="{$times}" data-hash="{$hash}" data-targetnumber="{$targetnumber}" data-duration="{$duration}"></div>
<div id="div_canvas" style="position: relative">
    <canvas id="canvas_line" width="600" height="600" style="position:absolute"></canvas>
    <canvas id="canvas_copy" width="600" height="600" style="position: absolute"></canvas>

    <canvas id="canvas_emphasis" width="600" height="600" style="position: absolute"></canvas>
    <canvas id="canvas_maruji" width="600" height="600" style="position:absolute"></canvas>
</div>
<script src="/assets/js/jquery/3.2.1/jquery.min.js"></script>
<script src="/assets/js/tmt.js"></script>
</body>
</html>