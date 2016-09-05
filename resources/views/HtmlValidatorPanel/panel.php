<style class="tracy-debug">
	#tracy-debug .Laravel-HtmlValidatorPanel {min-width: 700px;}
	#tracy-debug .Laravel-HtmlValidatorPanel table {width: 100%;max-width: 700px;}
	#tracy-debug .Laravel-HtmlValidatorPanel table pre {max-width: 670px;overflow: hidden;box-shadow: none !important;}
    #tracy-debug .Laravel-HtmlValidatorPanel td span.severenity-1 {color: #aaaa30 !important;font-weight: bold !important;}
    #tracy-debug .Laravel-HtmlValidatorPanel td span.severenity-2 {color: #aa832f !important;font-weight: bold !important;}
    #tracy-debug .Laravel-HtmlValidatorPanel td span.severenity-3 {color: #aa4c34 !important;font-weight: bold !important;}
    #tracy-debug .Laravel-HtmlValidatorPanel span.highlight {background: #cd1818;color: white;font-weight: bold;font-style: normal;display: block;padding: 0 .4em;margin: 0 -.4em;}
</style>

<h1>Document HTML validation (<?php echo $counter; ?> errors)</h1>

<div class="tracy-inner Laravel-HtmlValidatorPanel">
    <table>
        <?php foreach ($errors as $error): ?>
        <tr><td><span class="severenity-<?php echo (int) $error->level; ?>">
            <?php echo htmlspecialchars($severenity[$error->level].' on column '.$error->column.': '.$error->message); ?>
        </span></td></tr>
        <tr><td>
            <?php echo \Tracy\BlueScreen::highlightPhp($html, $error->line, 10); ?>
        </td></tr>
        <?php endforeach ?>
	</table>
</div>
