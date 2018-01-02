<!DOCTYPE html>
<html lang="de">
<head>
    <title><?php echo isset($title) ? $title : '' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css?v=1.6">
</head>
<body class="<?php echo isset($bodyClass) ? $bodyClass : '' ?>">
    <?php echo isset($content) ? $content : '' ?>
</body>
</html>