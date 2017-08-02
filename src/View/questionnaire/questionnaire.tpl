<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title></title>
    <link href="/assets/css/questionnaire.css" type="text/css" rel="stylesheet">
</head>
<body>
<div id="container">
    {foreach from=$arrArr key=keyQ item=valueQ}
        <div class="qa" id="qa-{$keyQ}">
            <div class="question" id="question-{$keyQ}" style="">{$valueQ}</div>
            <div class="answer" id="answer-{$keyQ}" style="">
                {foreach from=$answerChoice key=keyA item=valueA}
                    <div class="choice choice-{$keyA}" id="choice-{$keyA}-answer-{$keyQ}">{$valueA}</div>
                {/foreach}
            </div>
        </div>
    {/foreach}
</div>
<script src="/assets/js/jquery/3.2.1/jquery.min.js"></script>
<script src="/assets/js/KeyboardJS/master/keyboard.js" type="text/javascript"></script>
<script src="/assets/js/questionnaire.js"></script>
</body>
</html>