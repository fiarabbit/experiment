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
        Your Name:<input name="{$USERNAME}" size="40" id="input">
    </label>
</form>
<div>
    javascript: <span id="jscheck">disabled</span>
</div>
<div>
    cookie: <span id="cookiecheck">disabled</span>
</div>
<script type="text/javascript">document.getElementById("jscheck").textContent="enabled"</script>
<script type="text/javascript">if(document.cookie.search(/cookie=true/)){document.getElementById("cookiecheck").textContent="enabled"}</script>
</body>
</html>