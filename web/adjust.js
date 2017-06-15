/**
 * Created by hashimoto on 6/7/2017.
 */


// データ受信側
// Cookieにうめこんで計算データを送信するモジュールの一部
// これで受け取って，コロコロ変えるのに使う
/**
 * global object of data
 * @type {{qid: number, displayTime: number, answerTime: number, var1: number, var2: number, uid, validate: function}}
 */
let clientSideData={
    qid: -1,
    displayTime:0,
    answerTime:0,
    var1: 0,
    var2: 0,
    answer: 0,
    uid: (function(){
        let util={
            getCookie: function (name) { // private: fetch Cookie variable by name
                let value = "; " + document.cookie;
                let parts = value.split("; " + name + "=");
                if (parts.length === 2) return parts.pop().split(";").shift();
            },
            decodeURLJSON: function (string) {
                return JSON.parse(decodeURIComponent(string));
            }
        };
        console.log(util.getCookie('uid'));
        return util.decodeURLJSON((util.getCookie('uid')));
    })(),
    validate: function(){
        return (this.qid >= 0) && (this.displayTime!==0) && (this.var1 !== 0) && (this.var2 !==0) && (this.answer!=='') && (typeof(this.uid)!=='undefined')
    }
};

/**
 * a module for sending data which defines ajax transaction
 * @module sender
 */
let sender = (/** @lends sender */function () {
    let counter=0;
    let reference={
        sendData: /**
         * function to send Data
         * @param {object} dataObj
         * @returns {string} - Success or not
         */
            function (dataObj) {
            $.ajax({
                url: "http://experiment.va/experiment/adjust/sendData",
                type: "GET",
                data: dataObj
            })
                .done(function (response) {
                    console.log(response);
                    if (response==='resend') {
                        if (counter<3) {
                            reference.sendData(dataObj);
                            counter += 1;
                        }
                    }
                    else if (response==='success') {
                        console.log(dataObj);
                        counter=0;
                    }
                    else{
                        console.log(response);
                        counter=0;
                    }
                })
                .fail(function () {
                    if (counter<3) {
                        console.error('send failed.');
                        reference.sendData(dataObj);
                        counter += 1;
                    }
                });
        }
    };
    return reference;
})();

/**
 * a module used in $('#form').submit()
 * @module display
 */
let display = (/** @lends display */function () {
    let $ph1 = $('#placeHolder1');
    let $ph2 = $('#placeHolder2');
    let $input = $('#input');
    let questionGenerator = (function () {
        return {
            /**
             * @returns {{var1: *, var2: *}}
             */
            generate: function () {
                let var1 = {
                    min: 17,
                    max: 99
                };
                let var2 = {
                    min: 6,
                    max: 9
                };

                function randi(minMaxObj) {
                    return Math.floor(Math.random() * (minMaxObj.max-minMaxObj.min)) + minMaxObj.min+1;
                }

                function constraint(var1, var2) {
                    return (var1 * var2) % 10 !== 0;
                }

                let question = {
                    var1: randi(var1),
                    var2: randi(var2)
                };
                while (!constraint(question.var1, question.var2)) {
                    question = {
                        var1: randi(var1),
                        var2: randi(var2)
                    };
                }
                return question;
            }
        }
    })();

    return {
        show: /**
         * show var1 and 2, refresh qid and display Time (not answerTime)
         * @param dataObj
         */
            function (dataObj) {
            let question = questionGenerator.generate();
            dataObj.qid += 1;
            dataObj.displayTime= (new Date).getTime();
            dataObj.answerTime=0;
            dataObj.var1 = question.var1;
            dataObj.var2 = question.var2;
            $ph1.text(dataObj.var1);
            $ph2.text(dataObj.var2);
            $input.val('');
        }
    }
})();

let buttonFuncSetter = (function () {
    let dataObj;
    let $input = $('#input');
    let $form = $('#form');

    return {
        set: function (obj,senderModule,displayModule) {
            dataObj = obj;
            $form.submit(function(ev){
                ev.preventDefault();
                dataObj['answer']=parseInt($input.val());
                dataObj['answerTime']=(new Date).getTime();
                console.log(dataObj);
                if (dataObj.validate()){
                    senderModule.sendData(dataObj);
                    displayModule.show(clientSideData);
                }
            })
        }
    }
})();

let ready = (function(){
    let $ready=$('#ready');
    let $question=$('#question');
    let $ps0=$('#placeHolder0');
    let counter=1;

    let intervalHandle;
    return {
        set: function(clientSideData,displayModule){
            let next = function(){
                counter-=1;
                if (counter>0) {
                    $ps0.text(counter);
                }else{
                    $ready.hide();
                    $question.show();
                    displayModule.show(clientSideData);
                    clearInterval(intervalHandle);
                }
            };
            intervalHandle=setInterval(next,1000);
        }
    }
})();

/////////////////////////////////////////////
buttonFuncSetter.set(clientSideData,sender,display);
ready.set(clientSideData,display);