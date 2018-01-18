/**
 * Created by hashimoto on 6/27/2017.
 */

let clientSideData = (function () {
    function decodeCookie(name) {
        function getCookie(_name) {
            // private: fetch Cookie variable by name
            let value = "; " + document.cookie;
            let parts = value.split("; " + name + "=");
            if (parts.length === 2) return parts.pop().split(";").shift();
        }

        function decodeURLJSON(string) {//URI形式に変換されたJSON文字列をfetchできる
            return JSON.parse(decodeURIComponent(string));
        }

        return decodeURLJSON(getCookie(name));
    }

    return {
        username: decodeCookie('username'),
        times: decodeCookie('times'),
        hash: decodeCookie('hash'),
        type:'start'
    }
})();


let counter = (function () {
    const countInit = 3;
    let $ph0=$('#placeHolder0');
    $ph0.text(countInit.toString());
    let count = countInit;
    const countThreshold=-5;

    const interval = 1000;

    let reference = {
        init: function () {
            setTimeout(reference.step, interval)
        },
        step: function () {
            count = count -1;
            if(count>0) {
                $ph0.text(count.toString());
            }else {
                $ph0.text('keep smiling');
                if(count===0){
                    $.ajax({
                        url: '/another/start',
                        type: 'GET',
                        data: clientSideData
                    }).done(function (response) {
                        console.log(response);
                        if (response === "invalid hash") {
                            location.reload();
                        } else if (response === 'success') {
                            console.log("successfully sent");
                        }
                    });
                }
            }
            if (!(count > countThreshold)) {
                window.location = '/another/finish?'
                    + '&username=' + clientSideData.username
                    + '&hash=' + clientSideData.hash
                    + '&times='+clientSideData.times
                    + '&type=' + 'finish';
            }
            setTimeout(reference.step, interval);
        }
    };
    return reference;
})();
$('#readyButton').click(function (ev) {
    ev.preventDefault();
    $('#ready').hide();
    $('#placeHolder0').show();
    ev.stopPropagation();
    counter.init();
    console.log(clientSideData);
});
