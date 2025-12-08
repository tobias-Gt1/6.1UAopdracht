<?php
include 'connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(400); exit; }

$stmt = $conn->prepare("SELECT `image` FROM `gegevens` WHERE `gegevens_id`=? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($imageField);
$has = $stmt->fetch();
$stmt->close();

if (!$has || !$imageField) { http_response_code(404); exit; }

$data = null;
$mime = 'image/jpeg';

// If looks like a file name with extension, try filesystem
if (preg_match('/^[A-Za-z0-9._-]+\.(jpg|jpeg|png|gif|webp)$/i', $imageField)) {
    $path = __DIR__ . '/uploads/' . $imageField;
    if (is_file($path)) {
        $data = file_get_contents($path);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = $ext === 'jpg' ? 'image/jpeg' : ($ext === 'jpeg' ? 'image/jpeg' : ($ext === 'png' ? 'image/png' : ($ext === 'gif' ? 'image/gif' : ($ext === 'webp' ? 'image/webp' : 'application/octet-stream'))));
    }
}

// Else assume DB contains raw/base64 image data
if ($data === null) {
    $raw = $imageField;
    if (strpos($raw, 'base64,') !== false) {
        $parts = explode('base64,', $raw, 2);
        if (stripos($parts[0], 'image/png') !== false) { $mime = 'image/png'; }
        elseif (stripos($parts[0], 'image/gif') !== false) { $mime = 'image/gif'; }
        elseif (stripos($parts[0], 'image/webp') !== false) { $mime = 'image/webp'; }
        else { $mime = 'image/jpeg'; }
        $raw = $parts[1];
        $data = base64_decode($raw, true);
    } else {
        $maybe = base64_decode($raw, true);
        $data = $maybe !== false ? $maybe : $raw;
        if (function_exists('finfo_open')) {
            $fi = finfo_open(FILEINFO_MIME_TYPE);
            $det = finfo_buffer($fi, $data);
            finfo_close($fi);
            if ($det) { $mime = $det; }
        }
    }
}

if (!$data) { http_response_code(404); exit; }

header('Content-Type: ' . $mime);
header('Cache-Control: public, max-age=604800, immutable');
echo $data;
exit;
