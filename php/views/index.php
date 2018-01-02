<h1><?php echo isset($title) ? $title : '' ?></h1>
<?php if (isset($error) && $error == "403"): ?>
<div class="panel forbidden" align="center">
    <div class="panel-heading">
        <h2 class="text-center panel-title">Zu viele Versuche</h2>
    </div>
    <div class="panel-body">
        <img class="img-responsive" src="/img/403.gif" />
        <a class="btn btn-default" href="/">Weiter</a>
    </div>
</div>
<?php else: ?>
    <div class="panel form" align="center">
        <div class="panel-heading">
            <h2 class="text-center panel-title">PIN</h2>
        </div>
        <div class="panel-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error ?></div>
            <?php endif; ?>
            <form method="POST" action="/login">
                <div class="form-group">
                <input class="form-control" placeholder="Bitte PIN eingeben" type="password" name="wlanpin" value="" tabindex="1" required />
                </div>
                <button class="btn btn-default form-control" name="acceptagb" value="yes">Nutzungsbedingungen akzeptieren &amp; anmelden</button>
            </form>
        </div>
    </div>
    <div class="panel terms">
        <div class="panel-heading">
            <h2 class="text-center panel-title">Nutzungsbedingungen Gäste-WLAN</h2>
        </div>
        <div class="panel-body">
            <?php include '_terms.php' ?>
        </div>
    </div>
<?php endif; ?>