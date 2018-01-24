document.getElementById("jscheck").textContent = "enabled";
if (document.cookie.search(/cookie=true/) !== -1) {
    document.getElementById("cookiecheck").textContent = "enabled";
}

navigator.mediaDevices = navigator.mediaDevices || ((navigator.mozGetUserMedia || navigator.webkitGetUserMedia) ? {
        getUserMedia: function (c) {
            return new Promise(function (y, n) {
                (navigator.mozGetUserMedia ||
                navigator.webkitGetUserMedia).call(navigator, c, y, n);
            });
        }
    } : null);

if (!navigator.mediaDevices) {
    console.log("getUserMedia() not supported.");
}

// Prefer camera resolution nearest to 1280x720.

var constraints = {audio: true, video: {width: {ideal: 1280}, height: {ideal: 720}}};

navigator.mediaDevices.getUserMedia(constraints)
    .then(function (stream) {
        var video = document.getElementById('video');
        video.src = window.URL.createObjectURL(stream);
        video.onloadedmetadata = function (e) {
            document.getElementById('videocheck').textContent = 'enabled';
            video.play();
        };
    })
    .catch(function (err) {
        console.log(err.name + ": " + err.message);
    });

var repeat = setInterval(
    function () {
        if (document.getElementById("jscheck").textContent === "enabled" && document.getElementById("cookiecheck").textContent === "enabled" && document.getElementById("videocheck").textContent === "enabled") {
            document.getElementById("input").disabled = "";
            clearInterval(repeat);
        }
    }, 1000);
