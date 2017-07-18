<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title></title>
    <link href="/assets/css/questionnaire.css" type="text/css" rel="stylesheet">
</head>
<body>
<div id="container">
    {foreach from=$arrArr key=key item=value}
        <div class="qa" id="qa-{$key}">
            <div class="question" id="question-{$key}" style="">{$value}</div>
            <div class="answer" id="answer-{$key}" style=""></div>
        </div>
    {/foreach}
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdn.rawgit.com/ccampbell/mousetrap/825ce50c/mousetrap.min.js"></script>
<script src="/assets/js/questionnaire.js"></script>
</body>
</html>