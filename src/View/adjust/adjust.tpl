<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title></title>
    <link rel="stylesheet" type="text/css" href="/assets/css/experiment.css">
</head>
<body>
<div id="container">
    <div id="ready" class="font-large">
        <form>
            <button id="readyButton">Are you ready?</button>
        </form>
        <span id="placeHolder0">3</span></div>
    <div id="question" class="font-large" style="display:none">
  <span id="placeHolder1">
    13
  </span>
        ×
        <span id="placeHolder2">
    7
  </span>
        =
        <form id="form" style="display:inline" autocomplete="off">
            <input title="answer" type="text" id="input">
        </form>
    </div>
</div>
<div id="gaugeGroup">
    {assign var=colorArr value=['red','orange','yellow','yellowgreen','green']}
    {for $itr=0 to 4}
        <div class="gauge" style="background-color: {$colorArr[{$itr}]}"></div>
    {/for}
</div>
<script src="/assets/js/jquery/3.2.1/jquery.min.js"></script>
<script src="/assets/js/adjust.js"></script>
</body>
</html>