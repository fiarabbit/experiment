<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title> </title>
</head>
<body>
<span id="placeHolder1">13</span>×<span id="placeHolder2">7</span>=<form><input title="answer" type="text" id="input"></form>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
    (function(){
        function decodeCookie(nameArr) {
            function getCookie(name) { // private
                let value = "; " + document.cookie;
                let parts = value.split("; " + name + "=");
                if (parts.length === 2) return parts.pop().split(";").shift();
            }
            function decodeURLJSON(string) {
                return JSON.parse(decodeURIComponent(string));
            }
            let arr=[];
            for(let value of nameArr){
                arr.push(decodeURLJSON(getCookie(value)));
            }
            return arr;
        }
        let var1Arr=decodeCookie(['var1']);
        let var2Arr=decodeCookie(['var2']);
        // todo var1Arrにはvar1(#placeHolder1)に入るべき数字が入っている
        // todo Enterをfetchして，RTを計算し，inputに入っている数字をphpに投げる
        // これはcueingによって実装する．cueingは再帰関数によって実装する
        // onErrorで再送，onSuccessで次の送信を行う
        // php側では，来たデータを次々に登録していく
        // todo 同時に(receiveの確認を待たずに)inputをclearして，次の問題を表示する
    })()
</script>
</body>
</html>