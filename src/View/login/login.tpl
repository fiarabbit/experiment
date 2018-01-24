<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title></title>
    <link rel="stylesheet" type="text/css" href="/assets/css/login.css">
</head>
<body>
<div>{$msg}</div>
<form id="form" action="/login/newUser" method="get">
    <label>
        Your Name:<input name="{$USERNAME}" size="40" id="input" disabled="disabled">
    </label>
</form>
<div>
    javascript: <span id="jscheck">disabled</span>
</div>
<div>
    cookie: <span id="cookiecheck">disabled</span>
</div>
<div>
    video: <span id="videocheck">disabled</span>
</div>
<video id="video" autoplay style="width: 320px; height: 180px; border: 1px solid black;-moz-transform: scale(-1, 1); -webkit-transform: scale(-1, 1); -o-transform: scale(-1, 1); transform: scale(-1, 1);"></video>
<script type="text/javascript" src="assets/js/login.js"></script>
</body>
</html>