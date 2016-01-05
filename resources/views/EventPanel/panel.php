<h1>Events: <?php echo round($totalTime * 100, 2) ?> ms</h1>
<div class="tracy-inner laravel-EventPanel">
    <table>
        <thead>
            <tr>
                <th>Event</th>
                <th>Arguments</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <th>
                        <span class="tracy-dump-object"><?php echo array_get($log, 'firing') ?></span><br />
                        <?php echo array_get($log, 'editorLink') ?><br />
                        <span class="tracy-dump-string"><?php echo round(array_get($log, 'execTime', 0) * 100, 2) ?> ms</span>
                    </th>
                    <td>
                        <?php echo Tracy\Dumper::toHtml(array_get($log, 'params'), array_merge((array) $dumpOption, [
                            Tracy\Dumper::TRUNCATE => 50,
                            Tracy\Dumper::COLLAPSE => true,
                        ])) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
