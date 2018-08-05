<style class="tracy-debug">
	#tracy-debug .Laravel-TerminalPanel {background: #000; padding-bottom:15px; overflow: hidden}
    #tracy-debug .tracy-inner-container {min-width: 700px; min-height: 500px;}
</style>

<h1>Terminal</h1>

<div class="tracy-inner Laravel-TerminalPanel">
    <div class="tracy-inner-container">
        <?php if (empty($terminal) === false): ?>
            <?php echo $terminal; ?>
        <?php else: ?>
            <span style="color: #fff;">Terminal is Disabled</span>
        <?php endif ?>
    </div>
</div>
