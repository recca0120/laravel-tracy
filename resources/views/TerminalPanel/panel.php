<div class="laravel-TerminalPanel">
    <h1>Terminal</h1>
    <div class="tracy-inner" style="width: 700px; overflow-y:hidden">
        <?php if (empty($src) === false): ?>
            <iframe src="<?php echo $src ?>" style="width: 700px; height: 600px;"></iframe>
        <?php else: ?>
            composer require recca0120/terminal
        <?php endif ?>
    </div>
</div>
