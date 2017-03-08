<style class="tracy-debug">
	#tracy-debug .Laravel-TerminalPanel {background: #000; min-width: 700px; min-height: 500px; padding-bottom:15px;}
</style>

<h1>Terminal</h1>

<div class="tracy-inner Laravel-TerminalPanel">
    <?php if (empty($terminal) === false): ?>
        <?php echo $terminal; ?>
    <?php else: ?>
        <span style="color: #fff;">Terminal is Disabled</span>
    <?php endif ?>
</div>
