<style class="tracy-debug">
	#tracy-debug .Laravel-TerminalPanel {background: #000; min-width: 700px; padding-bottom:15px;}
</style>

<h1>Terminal</h1>

<div class="tracy-inner Laravel-TerminalPanel">
    <?php if (empty($html) === false): ?>
        <?php echo $html; ?>
    <?php else: ?>
        <span style="color: #fff;">Terminal is Disabled</span>
    <?php endif ?>
</div>
