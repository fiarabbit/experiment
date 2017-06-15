<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title> </title>
</head>
<body>
<div id="ready"><span id="placeHolder0">3</span></div>
<div id="question" style="display:none">
  <span id="placeHolder1">
    13
  </span>
  ×
  <span id="placeHolder2">
    7
  </span>
  =
  <form id="form">
    <input title="answer" type="text" id="input">
  </form>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="/adjust.js">
{*必要なことはajax通信が始まったときと終わったときにカウンタを増やしたり減らしたりすることだけ
で，カウンタがゼロのときにだけページ遷移を許可するようにすればよい(i.e. )
せっかく仕込んだsession系の黒魔術がダメになってとても悲しいが，sessionには今どこにいるかだけ仕込んで，それを頼りに再開してもらえばよいかな
*}

</script>
</body>
</html>