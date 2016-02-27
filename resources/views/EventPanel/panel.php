<div id="Laravel-EventPanel">
    <h1>Events: <?php echo round($totalTime * 100, 2) ?> ms</h1>
    <div class="tracy-inner">
        <table>
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Execute Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $key => $value): ?>
                    <tr>
                        <th>
                            <span class="tracy-dump-object"><?php echo array_get($value, 'firing') ?></span><br />
                            <?php echo array_get($value, 'editorLink') ?><br />
                        </th>
                        <td>
                            <?php if ($dumpMethod === 'tracy'): ?>
                                <?php
                                    echo Tracy\Dumper::toHtml(array_get($log, 'params'), array_merge((array) $config, [
                                        Tracy\Dumper::TRUNCATE => 50,
                                        Tracy\Dumper::COLLAPSE => true,
                                    ]));
                                ?>
                            <?php else: ?>
                                <div id="Laravel-EventPanel-<?php echo $key; ?>"></div>
                                <script>
                                (function() {
                                    var el = document.getElementById("Laravel-EventPanel-<?php echo $key; ?>");
                                    el.innerHTML = TracyDump(<?php echo json_encode($value) ?>);
                                })();
                                </script>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
