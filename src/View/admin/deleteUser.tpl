<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title> </title>
</head>
<body>
<form id="form" action="{$PREFIX}/login/deleteUser" method="get">
  <label>
    Delete User Name:<input name="UID" size="40" id="input">
  </label>
</form>
<ul>
    {foreach from=$arr item=v}
      <li>{$v}</li>
    {/foreach}
</ul>
</body>
</html>