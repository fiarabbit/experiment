<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
<div>{$MESSAGE}</div>
<form id="form" action="/admin/setPointer" method="post">
    <label>
        User Name:<input name={$USERNAME_ALIAS} size=40 id="username">
    </label>
    <label>
        Pointer:<input name={$POINTER_ALIAS} size=40 id="pointer">
    </label>
    <label>
        TimeLimit(optional):<input name={$TIMELIMIT_ALIAS} size=40 id="timeLimit">
    </label>
    <button type="submit">submit</button>
</form>
<ul>
    {foreach from=$arr item=v}
        <li>{$v}</li>
    {/foreach}
</ul>
</body>
</html>