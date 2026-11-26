<?php
$document_root = isset($_SERVER['DOCUMENT_ROOT'])
    ? rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/')
    : '';
$application_root = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');
$asset_base = '';

if ($document_root !== '' && stripos($application_root, $document_root) === 0) {
    $asset_base = rtrim(substr($application_root, strlen($document_root)), '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $page_title; ?> — Livestock Vaccination Reminder System</title>
<!-- new: the two typefaces -->
 <link rel="stylesheet" 
      href="https://fonts.googleapis.com/css2?
family=Source+Sans+3:wght@400;600&amp;family=Source+Serif+4:wght@6
00&amp;display=swap">
<link rel="stylesheet" href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/css/style.css">
<link rel="icon" type="image/jpeg"
 href="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/img/cattle-animal-farm-livestock-logo-icon-and-template-illustration-vector.jpg">
<script src="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/js/main.js" defer></script>
</head>
<body>
<header>
<h1>
<img class="site-logo" src="<?php echo htmlspecialchars($asset_base, ENT_QUOTES, 'UTF-8'); ?>/img/cattle-animal-farm-livestock-logo-icon-and-template-illustration-vector.jpg" alt="">
<span class="site-title-text"><?php echo $page_title; ?></span>
</h1>
<p>Keep your livestock healthy with our vaccination reminders.</p>
</header>