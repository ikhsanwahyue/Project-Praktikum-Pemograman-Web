<?php
if(isset($_GET['file'])) {
    $filename = basename($_GET['file']);
    $filepath = '../uploads/covers/' . $filename;
    
    // Check if file exists
    if(file_exists($filepath) && is_file($filepath)) {
        // Get file extension
        $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
        
        // Set appropriate content type
        $content_types = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        
        $content_type = $content_types[$extension] ?? 'image/jpeg';
        
        header('Content-Type: ' . $content_type);
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit();
    }
}

// Default image if file not found
header('Content-Type: image/png');
$im = imagecreate(200, 300);
$bg = imagecolorallocate($im, 240, 240, 240);
$text_color = imagecolorallocate($im, 180, 180, 180);
imagestring($im, 5, 50, 140, 'No Cover', $text_color);
imagepng($im);
imagedestroy($im);
?>