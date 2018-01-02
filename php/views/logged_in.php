<h1><?php echo isset($title) ? $title : '' ?></h1>
<div class="panel">
    <div class="panel-heading">
        <h2 class="text-center panel-title">Sie sind angemeldet</h2>
    </div>
    <div class="panel-body">
    <form method="POST" action="/logout">
        <button class="btn btn-default form-control" name="logout" value="yes">Abmelden</button>
    </form>
    </div>
</div>