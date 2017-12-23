<?php include '_header.php' ?>

    <div class="panel form" align="center">
        <div class="panel-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error ?></div>
            <?php endif; ?>
            <form method="POST" action="/login">
                <label class="control-label">PIN</label>
                <input class="form-control" type="password" name="wlanpin" value="" tabindex="1">
                <button class="btn btn-default form-control" name="acceptagb" value="yes">Nutzungsbedingungen akzeptieren &amp; anmelden</button>
            </form>
        </div>
    </div>
    <div class="panel terms">
        <div class="panel-body">
            <?php include '_terms.php' ?>
        </div>
    </div>

<?php include '_footer.php' ?>

