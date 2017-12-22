<?php include '_header.php' ?>

    <div class="buttonbox" align="center">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error ?></div>
        <?php endif; ?>
        <form method="POST" action="/login">
            <p align="center">
                PIN: <input class="form-control" type="password" name="wlanpin" value="" tabindex="1"><br>
                <small>Ihre PIN kann von einem Administartor über <code>portal.fritz.box</code> abgerufen werden.</small>
            </p>
            <button class="btn btn-default" name="acceptagb" value="yes">Nutzungsbedingungen akzeptieren &amp; anmelden</button>
        </form>
    </div>
    <div class="hidebox">
        <input id="nutzungsbedingungen" class="toggle" type="checkbox">
        <label for="nutzungsbedingungen"></label>
        <cite>Nutzungsbedingungen</cite>
        <div class="panel">
            <div class="panel-body eula">
                <?php include '_eula.php' ?>
            </div>
        </div>
    </div>

<?php include '_footer.php' ?>

