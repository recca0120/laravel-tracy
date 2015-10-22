<div class="laravel-UserPanel">
    <h1><?php echo $isLoggedIn===true?'Logged in':'Unlogged' ?></h1>
    <?php if ($isLoggedIn===false): ?>
        <p>no identity</p>
    <?php else: ?>
        <?php echo Tracy\Dumper::toHtml($user, $dumpOption) ?>
    <?php endif ?>
</div>
