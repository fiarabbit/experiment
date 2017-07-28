/**
 * Created by hashimoto on 6/7/2017.
 */


// データ受信側
// Cookieにうめこんで計算データを送信するモジュールの一部
// これで受け取って，コロコロ変えるのに使う
/**
 * global object of data
 * @type {{qid: number, displayTime: number, answerTime: number, var1: number, var2: number, username, validate: function}}
 */
(function () {
    let clientSideData=(function () {
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
            qid: -1,
            timeOver: false,
            timeLimit: decodeCookie('timeLimit'),
            displayTime: 0,
            answerTime: 0,
            var1: 0,
            var2: 0,
            answer: 0,
            times: decodeCookie('times'),
            username: decodeCookie('username'),
            hash: decodeCookie('hash'),
            validate: function (dataObj) {
                let _qid = (dataObj.qid >= 0);
                let _timeLimit = (dataObj.timeLimit !== 0);
                let _displayTime = (dataObj.displayTime !== 0);
                let _answerTime = (dataObj.answerTime !== 0);
                let _var1 = (dataObj.var1 !== 0);
                let _var2 = (dataObj.var2 !== 0);
                let _answer = (Number.isInteger(dataObj.answer));
                let _username = (typeof(dataObj.username) !== 'undefined');
                let _bool = _qid && _timeLimit && _displayTime && _answerTime && _var1 && _var2 && _answer && _username;
                if (!_bool) {
                    console.log(`qid:${_qid},timeLimit:${_timeLimit},_displayTime:${_displayTime},answerTime:${_answerTime},var1:${_var1},var2:${_var2},answer:${_answer},username:${_username}`)
                }
                return _bool;
            },
            nanValue: 99999
        };
    })();


    /**
     * a module for sending data which defines ajax transaction
     * @module sender
     */
    let sender = (/** @lends sender */function () {
        let $input = $('#input');

        let counter = 0;
        let reference = {
            pruneData: function (dataObj) {
                return {
                    qid: dataObj.qid,
                    timeLimit: Math.round(dataObj.timeLimit),
                    timeOver: dataObj.timeOver,
                    displayTime: dataObj.displayTime,
                    answerTime: dataObj.answerTime,
                    var1: dataObj.var1,
                    var2: dataObj.var2,
                    answer: dataObj.answer,
                    times: dataObj.times,
                    username: dataObj.username,
                    hash: dataObj.hash
                }
            },
            sendData: /**
             * function to send Data
             * @param {object} dataObj
             * @returns {string} - Success or not
             */
                function (dataObj) {
                let _dataObj = reference.pruneData(dataObj);
                console.log(_dataObj.timeOver);
                $.ajax({
                    url: "http://experiment.va/experiment/sendData",
                    type: "GET",
                    data: _dataObj
                })
                    .done(function (response) {
                        console.log(response);
                        if (response === 'resend') {
                            if (counter < 3) {
                                console.log('failed to send Obj');
                                // reference.sendData(dataObj);<- dataObjの実体は参照なので，違うデータを送ってしまう可能性がある
                                // counter += 1;
                            }
                        }
                        else if (response === 'success') {
                            console.log("successfully sent");
                            counter = 0;
                        }
                        else {
                            console.log(response);
                            counter = 0;
                        }
                    })
                    .fail(function () {
                        if (counter < 3) {
                            console.error('send failed.');
                            // reference.sendData(dataObj); <- dataObjの実体は参照なので，違うデータを送ってしまう可能性がある
                            // counter += 1;
                        }
                    });
            },
            reportTimeout: function (displayModule, dataObj) {
                dataObj['answer'] = isNaN(parseInt($input.val())) ? dataObj.nanValue : parseInt($input.val());
                dataObj['answerTime'] = dataObj.nanValue;
                dataObj['timeOver'] = true;
                if (dataObj.validate(dataObj)) {
                    reference.sendData(dataObj);
                }
                return dataObj.validate(dataObj)
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
        let $gaugeGroup = $('#gaugeGroup');
        let previous_var1 = 99999;
        let previous_var2 = 99999;
        let questionGenerator = (function () {
            return {
                /**
                 * @returns {{var1: *, var2: *}}
                 */
                generate: function () {
                    const var1 = {
                        min: 11,//min: 11,
                        max: 49//max: 49
                    };
                    const var2 = {
                        min: 6,//min: 6,
                        max: 9//max: 9
                    };

                    function randi(minMaxObj) {
                        return Math.floor(Math.random() * (minMaxObj.max - minMaxObj.min)) + minMaxObj.min + 1;
                    }

                    function constraint(var1, var2) {
                        return ((var1 * var2) % 10 !== 0) && (var1 % 10 !== 1) && (var2 % 10 !== 1) && ((var1 * var2) > 100) && !((var1 === previous_var1) && (var2 === previous_var2));
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
                    previous_var1 = question.var1;
                    previous_var2 = question.var2;
                    return question;
                }
            }

        })();
        return {
            show: /**
             * show var1 and 2, refresh qid, timeOver and display Time (not answerTime)
             * @param dataObj
             */
                function (dataObj) {
                let question = questionGenerator.generate();
                dataObj.qid += 1;
                dataObj.timeOver = false;
                dataObj.displayTime = (new Date).getTime();
                dataObj.answerTime = 0;
                dataObj.var1 = question.var1;
                dataObj.var2 = question.var2;
                $ph1.text(dataObj.var1);
                $ph2.text(dataObj.var2);
                $input.val('');
            },
            setGauge: function (number) {
                function color(i, N) {
                    let RGB;
                    if (2 * i < N) {
                        RGB = [255, Math.round(i / ((N - 1) / 2) * 255), 0];
                    } else {
                        RGB = [Math.round(255 * ((N - 1) - i) / ((N - 1) / 2)), 255, 0];
                    }
                    return `rgb(${RGB})`
                }

                function left(i, N) {
                    return `${i / N * 100}%`
                }

                function width(N) {
                    return `${1 / N * 100}%`
                }

                $gaugeGroup.empty();
                for (let i = 0; i < number; i++) {
                    $gaugeGroup.append(`
                    <div 
                        class="gauge gauge-${i}"
                        style="background-color: ${color(i, number)};}"> 
                    </div>`);
                }
            },
            reduceGauge: function () {
                $('.gauge:last').remove();
            }
        }
    })();
    let timer = (function () {
        // counter part
        const Default = 5;
        let counter = Default;
        // timer part
        let interval = clientSideData.timeLimit;

        let intervalObj;

        let displayModule;
        let senderModule;
        let dataObj;
        let adjusterModule;

        let initialized = false;
        let ended = false;

        let reference = {
            counter: {
                reset: function () {
                    counter = Default;
                    if (dataObj.var1 * dataObj.var2 === dataObj.answer) {
                        interval = adjusterModule.step('correct', interval);
                    } else {
                        interval = adjusterModule.step('incorrect', interval);
                    }
                    displayModule.show(dataObj);
                    displayModule.setGauge(Default);
                    reference.timer.reset();
                },
                reduce: function () {
                    counter -= 1;
                    if (counter === 0) {
                        if (senderModule.reportTimeout(displayModule, dataObj)) {
                            reference.counter.reset();
                        } else {
                            console.error('report failed');
                        }
                    } else {
                        displayModule.reduceGauge();
                    }
                },
                getCount: function () {
                    return counter;
                }
            },
            timer: {
                set: function (display, sender, data, adjuster) {
                    if (!initialized) {
                        displayModule = display;
                        senderModule = sender;
                        dataObj = data;
                        adjusterModule = adjuster;
                        initialized = true;
                    }
                    if (!ended) {
                        displayModule.setGauge(Default);
                        dataObj.timeLimit = interval;
                        intervalObj = setInterval(function () {
                            reference.counter.reduce();
                        }, interval / Default);
                        $(document).on('experimentEnd', function (ev) {
                            if (!ended) {
                                ended = true;
                                clearInterval(intervalObj);
                                $('#form').submit(function (ev) {
                                    ev.preventDefault();
                                    ev.stopPropagation();
                                });
                                $('#input').prop('disabled', true);
                            }
                        })
                    }
                },
                reset: function () {
                    clearInterval(intervalObj);
                    reference.timer.set();
                }
            }
        };
        return reference;
    })();

    let adjuster = (function (data) {
        let dataObj=data;
//        const StepStepSize = 0;//invariant
//         const stepSize = 0;//invariant
//         let incorrectCounter = 0;
//         let correctCounter = 0;
//         const incorrectThreshold = 0; //0
//         const correctThreshold = 0; //1
//         let stepCounter = 0;
//         let formerStep;
        let experimentLength=1000 * 60 * 30; //milliseconds // 1000*60*30
        let endDate;
        let nextValue;
        let ended = false;
        // const stepCounterThreshold = 1  ;//
        let reference = {
            init: function(){
                endDate=new Date((new Date()).getTime()+experimentLength);
                console.log(endDate);
            }
            ,
            // downStep: function () {
            //     stepSize = stepSize * (1 - StepStepSize);
            // },
            //correctが4回以上キタ状態でcorrectが来たら'down'，incorrectが0回以上続いている状態でincorrectが来たら'up'
            step: function (string, value) {
                if (!ended) {
                    switch (string) {
                        case 'correct':
                            console.log('keep(correct)');
                            nextValue=value;
                            // correctCounter += 1;
                            // if (correctCounter > correctThreshold) {
                            //     if (formerStep === 'up') {
                            //         reference.downStep();
                            //         stepCounter += 1;
                            //     }
                            //     nextValue = value * (1 - stepSize);
                            //     console.log('down(correct)');
                            //     formerStep = 'down';
                            //     correctCounter = 0;
                            // } else {
                            //     console.log(`keep(correct):${correctCounter}/${correctThreshold}`);
                            //     nextValue = value;
                            // }
                            break;
                        case 'incorrect':
                            console.log('keep(incorrect)');
                            nextValue=value;
                            // incorrectCounter += 1;
                            // if (incorrectCounter > incorrectThreshold) {
                            //     if (formerStep === 'down') {
                            //         reference.downStep();
                            //         stepCounter += 1;
                            //     }
                            //     nextValue = value * (1 + stepSize);
                            //     console.log('up(incorrect)');
                            //     formerStep = 'up';
                            //     incorrectCounter = 0;
                            // } else {
                            //     console.log(`keep(incorrect):${incorrectCounter}/${incorrectThreshold}`);
                            //     nextValue = value;
                            // }
                            break;
                    }

                    if ((new Date()) > endDate) {
                        $(document).trigger('experimentEnd');
                        alert("end");
                        window.location.href = 'http://experiment.va/experiment/finish/?hash='+dataObj.hash+'&times='+dataObj.times+'&username='+dataObj.username;
                        ended = true;
                    }
                    return nextValue;
                } else {
                    return false
                }
            }
        };
        return reference;
    })(clientSideData);

    let buttonFuncSetter = (function () {
        let dataObj;
        let $input = $('#input');
        let $form = $('#form');

        return {
            set: function (obj, senderModule, displayModule, timerModule) {
                dataObj = obj;
                $form.submit(function (ev) {
                    ev.preventDefault();
                    if ((new Date()).getTime() - dataObj.displayTime > 500) {
                        if ($input.val() !== '') {
                            dataObj['answer'] = parseInt($input.val());
                            dataObj['answerTime'] = (new Date).getTime();
                            if (dataObj.validate(dataObj)) {
                                senderModule.sendData(dataObj);
                            }
                            timerModule.counter.reset();
                        }
                    }
                })
            }
        }
    })();

    let ready = (function () {
        let $ready = $('#ready');
        let $question = $('#question');
        let $ps0 = $('#placeHolder0');
        const counterInit = 3;
        let counter = counterInit;

        let $readyButton = $('#readyButton');


        let intervalHandle;
        return {
            set: function (dataObj, displayModule, timerModule, senderModule, adjusterModule) {
                $ps0.hide();
                $readyButton.on('click', function (ev) {
                    $readyButton.hide();
                    ev.preventDefault();
                    $ps0.show();
                    let next = function () {
                        counter -= 1;
                        if (counter > 0) {
                            $ps0.text(counter);
                        } else {
                            $ready.hide();
                            $question.show();
                            adjusterModule.init();
                            displayModule.show(dataObj);
                            clearInterval(intervalHandle);
                            timerModule.timer.set(displayModule, senderModule, dataObj, adjusterModule);
                        }
                    };
                    intervalHandle = setInterval(next, 1000);
                });
            }
        };
    })();

/////////////////////////////////////////////
    buttonFuncSetter.set(clientSideData, sender, display, timer);
    ready.set(clientSideData, display, timer, sender, adjuster);

})();