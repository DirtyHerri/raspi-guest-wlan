<nav class="navbar navbar-default no-print">
    <div class="container-fluid">
        <ul class="nav navbar-nav pull-left">
            <li class="pull-left"><a href="/admin" >Admin</a></li>
        </ul>
        <ul class="nav navbar-nav pull-right">
            <li class="pull-right"><a href="#print" type="button" onclick="print();">Drucken</a></li>
        </ul>
    </div>
</nav>
<h1><?php echo isset($title) ? $title : '' ?></h1>
<div class="panel">
    <div class="panel-body">
        <?php if (!isset($list) || count($list) == 0): ?>
            Keine PIN-Liste verfügbar
        <?php else: ?>
            <ul class="pin-list" id="pin-list">
                <?php foreach ($list as $pin): ?>
                    <li><span><?php echo $pin ?></span></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<div class="panel no-print">
    <div class="panel-body">
        <form method="post" action="/list" onsubmit="return confirm('Neue Liste erstellen?');">
            <button class="form-control">Neue Liste</button>
        </form>
    </div>
</div>