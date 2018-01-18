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
            qid: decodeCookie('qid') || 0,
            value: 0
        }
    })();

    let qaController = (function (initQid) {
        let qLength = $('.qa').length;
        let aLength = $('.choice').length / qLength;
        let qid = initQid;
        return {
            next: function () {
                qid += 1;
                if (qid < qLength) {
                    return qid
                } else {
                    window.location = '/questionnaire/finish?hash=' + clientSideData.hash + '&times=' + clientSideData.times;
                }
            },
            getQLength: function () {
                return qLength
            },
            getALength: function () {
                return aLength
            }
        }
    })(clientSideData.qid);

    let display = (function () {
        let $qas = $('.qa');
        let $choice = $('.choice');
        return {
            show: function (qid) {
                $qas.hide();
                $('#qa-' + qid).show();
            },
            pikapika: function (str) {
                if (str === '1') {
                    $choice.each(function (i, elem) {
                        if ($(elem).hasClass('choice-1') || $(elem).hasClass('choice-10')) {
                            $(elem).css('background-color', 'lime');
                        } else {
                            $(elem).css('background-color', 'transparent')
                        }
                    });
                } else {
                    $choice.each(function (i, elem) {
                        if ($(elem).hasClass(`choice-${str}`)) {
                            $(elem).css('background-color', 'lime');
                        } else {
                            $(elem).css('background-color', 'transparent');
                        }
                    });
                }
            },
            pikapika2: function (str) {
                $choice.each(function (i, elem) {
                    if ($(elem).hasClass(`choice-${str}`)) {
                        $(elem).css('background-color', 'red');
                    } else {
                        $(elem).css('background-color', 'transparent');
                    }
                });
            },
            pikapikaclean: function(){
                $choice.each(function(i, elem){
                    $(elem).css('background-color', 'transparent')
                })
            }
        }
    })();

    let sender = (function (dataObj) {
        let counter = 0;
        return {
            sendData: function () {
                $.ajax({
                    url: "/questionnaire/sendData",
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

    let stringCue = (function () {
        const DELAY = 300;
        const DELAY2 = 150;
        let cue = '';
        const ACCEPT = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        let timeout;
        let timeout2;
        return {
            check: function (str, pikapikafunc, pikapikafunc2, pikapikacleanfunc, senderfunc) {
                if (ACCEPT.includes(str)) {
                    if ((cue === '1' && str === '0') || (cue === '0' && str === '1')) {
                        cue = '10'
                    } else {
                        cue = str;
                    }
                    pikapikafunc(cue);
                    if (timeout !== undefined) {
                        clearTimeout(timeout);
                    }
                    if (timeout2 !== undefined) {
                        clearTimeout(timeout2);
                    }
                    timeout = setTimeout(function () {
                        pikapikafunc2(cue);
                        timeout2 = setTimeout(function () {
                            senderfunc(cue);
                            pikapikacleanfunc();
                            cue='';
                        }, DELAY2);
                    }, DELAY);
                }
            }
        }
    })();
    for (let key = 0; key < qaController.getALength(); key++) {
        keyboardJS.on(key.toString(), function () {
            stringCue.check(key.toString(),display.pikapika,display.pikapika2,display.pikapikaclean,function(str){
                clientSideData.value=parseInt(str);
                sender.sendData(clientSideData);
                clientSideData.qid=qaController.next();
                display.show(clientSideData.qid);
            });
        })
    }
    for (let qid = 0; qid < qaController.getQLength(); qid++) {
        for (let aid = 0; aid < qaController.getALength(); aid++) {
            $(`#choice-${aid}-answer-${qid}`).click(function (ev) {
                ev.preventDefault();
                ev.stopPropagation();
                clientSideData.value = parseInt($(ev.target).text());
                sender.sendData(clientSideData);
                clientSideData.qid = qaController.next();
                display.show(clientSideData.qid)
            });
        }
    }
    display.show(clientSideData.qid);
})();