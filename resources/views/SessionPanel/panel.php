<div id="Laravel-SessionPanel">
    <h1>Session #<?php echo $sessionId  ?> (Lifetime: <?php echo array_get($config, 'lifetime') ?>)</h1>
    <div class="tracy-inner">
        <?php if (empty($laravelSession) === true): ?>
            <p><i>empty</i></p>
        <?php else: ?>
            <table>
                <tbody>
                    <?php foreach ($laravelSession as $key => $value): ?>
                        <tr>
                            <th><?php echo $key  ?></th>
                            <td>
                                <?php if ($dumpMethod === 'tracy'): ?>
                                    <?php
                                        echo Tracy\Dumper::toHtml($value, $config)
                                    ?>
                                <?php else: ?>
                                    <div id="Laravel-SessionPanel-<?php echo $key; ?>"></div>
                                    <script>
                                    (function() {
                                        var el = document.getElementById("Laravel-SessionPanel-<?php echo $key; ?>");
                                        el.innerHTML = TracyDump(<?php echo json_encode($value) ?>);
                                    })();
                                    </script>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>
</div>
