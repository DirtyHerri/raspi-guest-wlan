<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Herris Gast-WLAN Administrator</title>
    <meta http-equiv="Refresh" content="5" />
    <style>
        html, body {
            height: 100%;
        }
        body {
            font-family: Arial, sans-serif;
            font-weight: 700;
            background-color: black;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20vw;
            width: 100%;
        }
        body > div {
            flex: 1;
            text-align: center;
        }
    </style>
</head>
<body>
    <div>
        <?php echo !empty($pin) ? $pin : "Keine PIN verfügbar" ?>
    </div>
</body>
</html>