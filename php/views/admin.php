<nav class="navbar navbar-default no-print">
    <div class="container-fluid">
        <ul class="nav navbar-nav">
            <?php if (isset($clearFailed) && $clearFailed): ?>
                <li class="pull-left">
                    <form action="/clear_failed" method="post">
                        <button class="btn btn-danger">Sperre aufheben</button>
                    </form>
                </li>
            <?php endif; ?>
            <li class="pull-left">
                <a href="/list" class="nav-link">PIN-Liste</a>
            </li>
            <li class="pull-right">
                <form class="toggle" action="/" method="post">
                    <input type="hidden" name="toggle" value="0" />
                    <input class="tgl tgl-skewed" id="toggle" type="checkbox" name="toggle" value="1" />
                    <label class="tgl-btn" data-tg-off="OFF" data-tg-on="ON" for="toggle"></label>
                </form>
            </li>
        </ul>
    </div>
</nav>

<div class="container-flex">
    <div class="pin" id="pin"><?php echo isset($pin) ? $pin : '' ?></div>
    <div id="shutdown-menu">
        <input type="checkbox" id="shutdown-menu-toggle" />
        <label for="shutdown-menu-toggle">  Shutdown</label>
        <ul>
            <li><button id="btn-reboot" class="btn btn-danger">Reboot</button></li>
            <li><button id="btn-halt" class="btn btn-danger">Halt</button></li>
        </ul>
    </div>
</div>

<script>
    (function(){

        const reboot = document.getElementById('btn-reboot');
        const halt   = document.getElementById('btn-halt');

        reboot.addEventListener('click', function(e){
            if (confirm('Neustart durchführen?')) {
                fetch('/reboot', {
                        method: 'post',
                        headers: new Headers()
                    })
                    .then(function(resp){
                        alert('Wird neu gestartet!');
                    })
                ;
            }
        });
        halt.addEventListener('click', function(e){
            if (confirm('Wirklich herunterfahren?')) {
                fetch('/halt', {
                        method: 'post',
                        headers: new Headers()
                    })
                    .then(function(resp){
                        alert('Wird heruntergefahren!');
                    })
                ;
            }
        });

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