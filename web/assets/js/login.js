document.getElementById("jscheck").textContent = "enabled";
if (document.cookie.search(/cookie=true/) != -1) {
    document.getElementById("cookiecheck").textContent = "enabled";
    document.getElementById("input").disabled = "";
}
