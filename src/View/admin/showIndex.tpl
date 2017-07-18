<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title> </title>
</head>
<body>
<div>
    {$MESSAGE}
</div>
<ul>
    {foreach from=$ACTION item=v}
      <li><a href="{$PREFIX}/admin/{$v}">{$v}</a></li>
    {/foreach}
</ul>
</body>
</html>