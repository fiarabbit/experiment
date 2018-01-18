Random = (function () {
    var x_1 = 123456789;
    var x_2 = 362436069;
    var x_3 = 521288629;
    var x_4 = 88675123;

    const BIT_MAX = 2147483647;
    const BIT_MIN = -2147483648;

    return {
        next: function () {
            var t;
            t = x_1 ^ (x_1 << 11);
            x_1 = x_2;
            x_2 = x_3;
            x_3 = x_4;
            x_4 = (x_4 ^ (x_4 >>> 19)) ^ (t ^ (t >>> 8));
            return (x_4 - BIT_MIN) / (BIT_MAX - BIT_MIN)
        }
    }
})();
externalData = document.getElementById("div_data").dataset;
view = (function () {
    return {
        maruji: (function () {
            var size = 20;
            var font_family = "Meiryo";
            var canvas = document.getElementById("canvas_maruji");
            var context = canvas.getContext("2d");
            context.font = String(size) + "px " + font_family;
            context.textAlign = "center";
            context.textBaseline = "middle";

            return {
                getSize: function () {
                    return size;
                },
                draw: function (center_xy, text) {
                    var center_x = center_xy[0];
                    var center_y = center_xy[1];
                    context.beginPath();
                    context.arc(center_x, center_y, size, 0, 2 * Math.PI, false);
                    context.fillStyle = "white";
                    context.fill();
                    context.stroke();
                    context.closePath();
                    context.beginPath();
                    context.fillStyle = "black";
                    context.fillText(text, center_x, center_y);
                },
                clear: function () {
                    context.clearRect(0, 0, canvas.width, canvas.height);
                }
            }
        })(),
        emphasis: (function () {
            var size = 24;
            var canvas = document.getElementById("canvas_emphasis");
            var context = canvas.getContext("2d");

            return {
                draw: function (center_xy) {
                    var center_x = center_xy[0];
                    var center_y = center_xy[1];
                    context.beginPath();
                    context.arc(center_x, center_y, size, 0, 2 * Math.PI, false);
                    context.fillStyle = "yellow";
                    context.fill();
                    context.closePath();
                    context.beginPath();
                },
                clear: function () {
                    context.clearRect(0, 0, canvas.width, canvas.height);
                }
            }
        })(),
        line: (function () {
            var canvas = document.getElementById("canvas_line");
            var context = canvas.getContext("2d");

            return {
                draw: function (start_xy, end_xy) {
                    var start_x = start_xy[0];
                    var start_y = start_xy[1];
                    var end_x = end_xy[0];
                    var end_y = end_xy[1];
                    context.beginPath();
                    context.moveTo(start_x, start_y);
                    context.lineTo(end_x, end_y);
                    context.stroke();
                    context.closePath();
                },
                clear: function () {
                    context.clearRect(0, 0, canvas.width, canvas.height);
                },
                getImage: function () {
                    return canvas
                }
            }
        })(),
        copy: (function () {
            var canvas = document.getElementById("canvas_copy");
            var context = canvas.getContext("2d");

            return {
                clear: function () {
                    console.log('copy cleared');
                    context.clearRect(0, 0, canvas.width, canvas.height);
                },
                pasteImage: function (image) {
                    console.log('image pasted');
                    context.drawImage(image, 0, 0);
                }
            }
        })(),
        div: (function () {
            var div = document.getElementById("div_canvas");
            var x_cache;
            var y_cache;
            var refresh_interval = 20;
            var refresh_interval_object = undefined;

            function refresh(ev) {
                x_cache = ev.offsetX;
                y_cache = ev.offsetY;
                model.event.dispatcher.reportXY([x_cache, y_cache]);
                div.removeEventListener("mousemove", refresh, false);
            }

            div.addEventListener("click", function (ev) {
                model.event.dispatcher.divClick([ev.offsetX, ev.offsetY]);
            }, false);

            return {
                startListen: function () {
                    if (refresh_interval_object === undefined) {
                        console.log("startListen");
                        div.addEventListener("mousemove", refresh, false);
                        clearInterval(refresh_interval_object);
                        refresh_interval_object = setInterval(function () {
                            div.addEventListener("mousemove", refresh, false);
                        }, refresh_interval);
                    } else {
                        console.log("already listening")
                    }
                },
                stopListen: function () {
                    if (refresh_interval_object !== undefined) {
                        clearInterval(refresh_interval_object);
                        refresh_interval_object = undefined;
                        console.log("stop listening");
                    }
                    x_cache = undefined;
                    y_cache = undefined;
                    div.removeEventListener("mousemove", refresh, false);
                },
                isListening: function () {
                    return (refresh_interval_object !== undefined)
                }
            }
        })(),
        util: (function () {
            var canvas_width = document.getElementById("canvas_maruji").width;
            var canvas_height = document.getElementById("canvas_maruji").height;
            return {
                getCanvasCoordinate: function (standard_xy) {
                    var x = Math.round(standard_xy[0] * canvas_width);
                    var y = Math.round(standard_xy[1] * canvas_height);
                    return [x, y]
                },
                getStandardCoordinate: function (canvas_xy) {
                    var standard_x = canvas_xy[0] / canvas_width;
                    var standard_y = canvas_xy[1] / canvas_height;
                    return [standard_x, standard_y]
                }
            }
        })()
    }
})();


model = (function () {
    return {
        timer: (function(){
            var timer;
            var startTime;
            return {
                set: function(){
                    if (timer === undefined) {
                        timer = setTimeout(model.state.setIsLast, externalData["duration"]);
                        startTime = Date.now();
                    }
                },
                getRemaining: function(){
                    console.log((externalData["duration"]-Date.now()+startTime)/1000/60 + "minutes")
                }
            }
        })(),
        drawer: { // only model.drawer can draw something via view
            line: (function () {
                var drawing = false;
                var last_xy = [undefined, undefined];
                var current_xy = [undefined, undefined];
                return {
                    isDrawing: function () {
                        return drawing;
                    },
                    refreshXY: function (xy) {
                        last_xy = current_xy;
                        current_xy = xy;
                    },
                    startDrawing: function () {
                        console.log("startDrawingLine");
                        if (!view.div.isListening()) {
                            view.div.startListen();
                        }
                        drawing = true;
                    },
                    stopDrawing: function (stop_listening = false) {
                        console.log("stopDrawingLine");
                        console.log(stop_listening);
                        if (stop_listening && view.div.isListening()) {
                            view.div.stopListen();
                        }
                        drawing = false;
                    },
                    drawStep: function () {
                        if (last_xy[0] !== undefined && last_xy[1] !== undefined && current_xy[0] !== undefined && current_xy[1] !== undefined) {
                            view.line.draw(last_xy, current_xy);
                        }
                    },
                    temporalSave: function () {
                        var image = view.line.getImage();
                        view.copy.pasteImage(image);
                        view.line.clear();
                    },
                    clearLine: function () {
                        view.line.clear()
                    },
                    clearAll: function () {
                        view.line.clear();
                        view.copy.clear();
                        last_xy = [undefined, undefined];
                        current_xy = [undefined, undefined];
                    }

                }
            })(),
            question: (function () {
                var list_target = undefined;
                return {
                    drawQuestion: function (question) {
                        list_target = question;
                        for (var i = 0; i < list_target.length; i++) {
                            var target = list_target[i];
                            var xy = [target[0], target[1]];
                            var label = target[2];
                            view.maruji.draw(xy, label);
                        }
                    },
                    drawEmphasis: function (target) {
                        view.emphasis.draw(target.slice(0, 2))
                    },
                    clearEmphasis: function () {
                        view.emphasis.clear();
                    },
                    clearAll: function () {
                        view.maruji.clear();
                        view.emphasis.clear();
                    }
                }
            })(),
            clearAll: function () {
                model.drawer.line.clearAll();
                model.drawer.question.clearAll();
            }
        },
        hoverChecker: (function () {
            var maruji_size = view.maruji.getSize();
            return {
                check: function (xy, question) {
                    var x = xy[0];
                    var y = xy[1];
                    for (var i = 0; i < question.length; i++) {
                        var xy_target = question[i];
                        var x_target = xy_target[0];
                        var y_target = xy_target[1];
                        var distance = Math.hypot(y_target - y, x_target - x);
                        if (distance < maruji_size) {
                            model.event.dispatcher.hover(i);
                        }
                    }
                }
            }
        })(),
        clickChecker: (function () {
            var maruji_size = view.maruji.getSize();
            return {
                isTarget: function (xy, xy_target) {
                    var x = xy[0];
                    var y = xy[1];
                    var x_target = xy_target[0];
                    var y_target = xy_target[1];
                    var distance = Math.hypot(y_target - y, x_target - x);
                    if (distance < maruji_size) {
                        model.event.dispatcher.clickCorrect();
                    }
                }
            }
        })(),
        questionGenerator: {
            MAX_INDEX: parseInt(externalData["targetnumber"]),
            newQuestionAndType: (function () {
                const japanese = "あいうえおかきくけこさしす";
                var labels_A = [];
                var labels_B = [];
                for (var i = 0; i < 26; i++) {
                    if (i % 2 === 0) {
                        labels_B.push(String(i / 2 + 1));
                    } else {
                        labels_B.push(japanese[(i - 1) / 2]);
                    }
                    labels_A.push(String(i + 1))
                }
                var maruji_size = view.maruji.getSize();
                var offset_standard_xy = view.util.getStandardCoordinate([maruji_size, maruji_size]);
                var offset_standard_x = offset_standard_xy[0];
                var offset_standard_y = offset_standard_xy[1];
                return function (question_type) {
                    var question = [];
                    for (var i = 0; i < model.questionGenerator.MAX_INDEX; i++) {
                        while (true) {
                            var standard_x = offset_standard_x + (1 - 2 * offset_standard_x) * Random.next();
                            var standard_y = offset_standard_y + (1 - 2 * offset_standard_y) * Random.next();
                            var xy = view.util.getCanvasCoordinate([standard_x, standard_y]);
                            var x = xy[0];
                            var y = xy[1];
                            var flag = true;
                            for (var j = 0; j < i - 1; j++) {
                                //check yoko
                                if (flag) {
                                    var existing_x = question[j][0];
                                    var existing_y = question[j][1];
                                    var distance = Math.hypot(existing_x - x, existing_y - y);
                                    console.log(`_: ${labels_B[j]}, : ${labels_B[i]}`);
                                    console.log(`_x: ${existing_x}, _y: ${existing_y}`);
                                    console.log(`x: ${x}, y: ${y}`);
                                    console.log(`distance: ${distance}, maruji_size: ${maruji_size}`);
                                    flag = (distance > 2 * maruji_size);
                                }
                            }
                            if (flag) {
                                break
                            }
                        }
                        if (question_type === "A") {
                            question.push([x, y, labels_A[i]])
                        } else if (question_type === "B") {
                            question.push([x, y, labels_B[i]])
                        } else {
                            console.error('invalid input argument');
                        }
                    }
                    return [question, question_type]
                }
            })()
        },
        event: {
            functions: {
                onReportXY: function (ev) {
                    var current_xy = ev.detail;
                    model.drawer.line.refreshXY(current_xy);
                    if (model.drawer.line.isDrawing()) {
                        model.drawer.line.drawStep();
                    }
                    var question = model.state.getQuestion();
                    model.hoverChecker.check(current_xy, question);
                },
                onNewQuestion: function (ev) {
                    var question = ev.detail.question;
                    var question_type = ev.detail.question_type;
                    model.timer.set();
                    model.timer.getRemaining();
                    model.state.setIndex(0);
                    model.state.setQid(model.state.getQid() + 1);
                    model.state.setQuestion(question);
                    model.state.setType(question_type);
                    model.state.setStart(Date.now());
                    model.state.setMistake(0);
                    model.state.setMistakeStatus(false);
                    model.drawer.clearAll();
                    model.drawer.question.drawQuestion(question); // draw maruji
                },
                onDivClick: function (ev) {
                    if (model.state.getIndex() === 0) {
                        var xy = ev.detail;
                        var question = model.state.getQuestion();
                        var xy_target = question[0];
                        if (model.clickChecker.isTarget(xy, xy_target)) {
                            model.dispatcher.clickCorrect();
                        }
                    }
                },
                onClickCorrect: function (ev) {
                    model.state.setIndex(model.state.getIndex() + 1);
                    model.state.setMistakeStatus(false);
                    model.drawer.line.startDrawing();
                },
                onHover: function (ev) {
                    var index_hover = ev.detail;
                    var index_target = model.state.getIndex();
                    if (index_hover === index_target - 1) {
                        if (model.state.getMistakeStatus()) {
                            model.drawer.question.clearEmphasis();
                            model.drawer.line.startDrawing();
                            model.state.setMistakeStatus(false);
                        }
                    }
                    else if (index_hover === index_target) {
                        if (!model.state.getMistakeStatus()) {
                            model.state.setMistakeStatus(false);
                            // if correct
                            if (index_target + 1 !== model.questionGenerator.MAX_INDEX) {
                                // if not last
                                //noinspection JSAnnotator
                                console.log(`${index_target + 1}th target was hovered)`);
                                model.state.setIndex(index_target + 1);
                                model.drawer.line.temporalSave();
                            } else {
                                // if last
                                var type_last_question = model.state.getType();
                                model.state.setEnd(Date.now());
                                model.state.save();
                                var to_be_sent = model.state.dump();
                                console.log(to_be_sent);
                                model.sender.send(to_be_sent);
                                model.drawer.line.clearAll();
                                model.drawer.line.stopDrawing(true);
                                console.log("saved");
                                var question_and_type;
                                if (type_last_question === "A") {
                                    question_and_type = model.questionGenerator.newQuestionAndType("B");
                                } else {
                                    question_and_type = model.questionGenerator.newQuestionAndType("B");
                                }
                                model.event.dispatcher.newQuestion(question_and_type[0], question_and_type[1])
                            }
                        }
                    } else {
                        if (!model.state.getMistakeStatus()) {
                            //if wrong
                            model.state.setMistake(model.state.getMistake() + 1);
                            model.drawer.line.clearLine();
                            var question = model.state.getQuestion();
                            model.drawer.question.drawEmphasis(question[index_target - 1]);
                            model.drawer.line.stopDrawing(false);
                            model.state.setMistake(model.state.getMistake() + 1);
                            model.state.setMistakeStatus(true);
                        }
                    }
                }
            },
            dispatcher: (function () {
                return {
                    reportXY: function (xy) {
                        document.dispatchEvent(new CustomEvent("reportXY", {detail: xy}))
                    },
                    newQuestion: function (question, question_type) {
                        document.dispatchEvent(new CustomEvent("newQuestion", {
                            detail: {
                                question: question,
                                question_type: question_type
                            }
                        }))
                    },
                    startDrawing: function () {
                        document.dispatchEvent(new CustomEvent("startDrawing", {detail: null}))
                    },
                    stopDrawing: function () {
                        document.dispatchEvent(new CustomEvent("stopDrawing", {detail: null}))
                    },
                    clickCorrect: function () {
                        document.dispatchEvent(new CustomEvent("clickCorrect", {detail: null}));
                        console.log("clickCorrect")
                    },
                    hover: function (index) {
                        document.dispatchEvent(new CustomEvent("hover", {detail: index}));
                    },
                    divClick: function (xy) {
                        document.dispatchEvent(new CustomEvent("divClick", {detail: xy}))
                    }

                }
            })()
        },
        state: (function () {
            var history = [];
            var current_qid = 0;
            var current_question;
            var current_index;
            var current_question_start_timestamp;
            var current_question_end_timestamp;
            var current_question_mistake;
            var current_question_type;
            var current_mistake_status;
            var is_last = false;
            return {
                setQid: function (qid) {
                    current_qid = qid;
                },
                getQid: function () {
                    return current_qid;
                },
                setQuestion: function (question) {
                    current_question = question;
                },
                getQuestion: function () {
                    return current_question;
                },
                setIndex: function (index) {
                    current_index = index;
                },
                getIndex: function () {
                    return current_index
                },
                setStart: function (timestamp) {
                    current_question_start_timestamp = timestamp
                },
                getStart: function () {
                    return current_question_start_timestamp
                },
                setEnd: function (timestamp) {
                    current_question_end_timestamp = timestamp
                },
                getEnd: function () {
                    return current_question_end_timestamp
                },
                setMistake: function (number) {
                    current_question_mistake = number
                },
                getMistake: function () {
                    return current_question_mistake
                },
                setType: function (str) {
                    current_question_type = str;
                },
                getType: function () {
                    return current_question_type
                },
                setMistakeStatus: function (bool) {
                    current_mistake_status = bool
                },
                getMistakeStatus: function () {
                    return current_mistake_status
                },
                getIsLast: function() {
                    return is_last
                },
                setIsLast: function(){
                    is_last = true
                },
                save: function () {
                    history.push({
                        question: current_question,
                        start: current_question_start_timestamp,
                        end: current_question_end_timestamp,
                        mistake: current_question_mistake,
                        type: current_question_type
                    })
                },
                dump: function () {
                    return {
                        qid: current_qid,
                        start: current_question_start_timestamp,
                        end: current_question_end_timestamp,
                        mistake: current_question_mistake,
                        type: current_question_type
                    }
                },
                getHistory: function () {
                    return history;
                }
            }
        })(),
        sender: (function () {
            var url = "/tmt/sendData";
            return {
                send: function (data) {
                    console.log(data);
                    console.log("start sending data");
                    var req = new XMLHttpRequest();
                    req.onreadystatechange = function () {
                        console.log(req.readyState);
                        if (req.readyState === 4) {
                            if (req.status === 200) {
                                var response = req.responseText;
                                console.log(response);
                                if (response === "invalid hash") {
                                    location.reload();
                                } else if (response === "success") {
                                    console.log("successfully sent");
                                    if (model.state.getIsLast()){
                                        var query = "username=" + externalData["username"] + "&hash=" + externalData["hash"] + "&times=" + externalData["times"];
                                        window.location.href = "/tmt/finish?" + query
                                    }
                                }
                            }
                        }
                    };
                    var _ = [];
                    var key;
                    for (key in data) {
                        _.push(key + '=' + encodeURIComponent(data[key]));
                    }
                    var encoded_string = _.join('&');
                    _ = [];
                    for (key in externalData){
                        _.push(key + '=' + encodeURIComponent(externalData[key]));
                    }
                    encoded_string = encoded_string + "&" + _.join('&');
                    console.log(encoded_string);
                    req.open('GET', url+ "?" + encoded_string, true);
                    req.send(null);
                }
            }
        })()
    }
})();


question_and_type = model.questionGenerator.newQuestionAndType("B");
question = question_and_type[0];
type = question_and_type[1];

document.addEventListener("newQuestion", model.event.functions.onNewQuestion);
document.addEventListener("reportXY", model.event.functions.onReportXY);
document.addEventListener("hover", model.event.functions.onHover);
document.addEventListener("divClick", model.event.functions.onDivClick);
document.addEventListener("clickCorrect", model.event.functions.onClickCorrect);

model.event.dispatcher.newQuestion(question, type);