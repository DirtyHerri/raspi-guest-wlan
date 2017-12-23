<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Herris Gast-WLAN Administrator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css?v=2">
    <link rel="manifest" href="/manifest.json?v=4">
</head>
<body class="admin">
    <div class="toggle">
        <form action="/" method="post">
            <input type="hidden" name="toggle" value="0" />
            <input class="tgl tgl-skewed" id="toggle" type="checkbox" name="toggle" value="1" />
            <label class="tgl-btn" data-tg-off="OFF" data-tg-on="ON" for="toggle"></label>
        </form>
    </div>

    <div class="pin" id="pin">
        <?php echo isset($pin) ? $pin : '' ?>
    </div>
    <script>
        (function(){

            var fetchPin = function() {
                fetch('/pin')
                    .then(function(resp){
                        return resp.text();
                    })
                    .then(function(text){
                        document.getElementById('pin').innerHTML = text;
                        setTimeout(fetchPin, 5000);
                    })
                ;
            };

            setTimeout(fetchPin, 5000);

            const toggle = document.getElementById('toggle');

            fetch('/status')
                .then(function(resp){
                    return resp.text();
                })
                .then(function(text){
                    toggle.checked = parseInt(text, 10) === 1;
                })
            ;

            toggle.addEventListener('change', function(e){
                e.preventDefault();
                const data = new URLSearchParams();
                for (const pair of new FormData(this.form)) {
                    data.append(pair[0], pair[1]);
                }
                fetch('/toggle', {
                        method: 'post',
                        body: data,
                        headers: new Headers()
                    })
                    .then(function(resp){
                        return resp.text();
                    })
                    .then(function(text){
                        toggle.checked = parseInt(text, 10) === 1;
                    })
                ;
            });

        })();
    </script>
</body>
</html>