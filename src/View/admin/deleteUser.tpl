<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title> </title>
</head>
<body>
<div>{$MESSAGE}</div>
<form id="form" action="/admin/deleteUser" method="post">
  <label>
    Delete User Name:<input name={$USERNAME_ALIAS} size="40" id="input">
  </label>
</form>
<ul>
    {foreach from=$arr item=v}
      <li>{$v}</li>
    {/foreach}
</ul>
</body>
</html>