(function () {
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
            qid: decodeCookie('qid')||0,
            value: 0
        }
    })();
    let qaController = (function (initQid) {
        let qaLength = $('.qa').length;
        let qid = initQid;
        return {
            next: function () {
                qid+=1;
                if (qid < qaLength) {
                    return qid
                } else {
                    window.location = 'http://experiment.va/questionnaire/finish?hash=' + clientSideData.hash + '&times=' + clientSideData.times;
                }
            },
            getQLength: function () {
                return qaLength
            }
        }
    })(clientSideData.qid);

    let display = (function () {
        let $qas = $('.qa');
        return {
            show: function (qid) {
                $qas.hide();
                $('#qa-' + qid).show();
            }
        }
    })();

    let sender = (function (dataObj) {
        let counter = 0;
        return {
            sendData: function () {
                $.ajax({
                        url: "http://experiment.va/questionnaire/sendData",
                        type: "GET",
                        data: dataObj
                    })
                    .done(function (response) {
                        console.log(response);
                        if (response === "invalid hash") {
                            location.reload();
                        } else if (response === 'success') {
                            console.log("successfully sent");
                            counter = 0;
                        }
                    })
                    .fail(function () {
                        console.log('send failed');
                    })

            }
        };
    })(clientSideData);
    for (let qid = 0; qid < qaController.getQLength(); qid++) {
        $('#answer-' + qid).click(function(ev){
            ev.stopPropagation();
            clientSideData.value=ev.offsetX / $(ev.target).width();
            sender.sendData(clientSideData);
            clientSideData.qid=qaController.next();
            display.show(clientSideData.qid);
        })
    }
    display.show(clientSideData.qid);
})
();