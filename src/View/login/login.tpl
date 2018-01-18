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
</body>
</html>