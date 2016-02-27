<div id="Laravel-RoutingPanel">
    <h1>Route</h1>
    <div class="tracy-inner">
        <table>
            <tbody>
                <?php foreach ($action as $key => $value): ?>
                    <tr>
                        <th><?php echo $key ?></th>
                        <td>
                            <?php if ($dumpMethod === 'tracy'): ?>
                                <?php
                                    echo Tracy\Dumper::toHtml($value, $config)
                                ?>
                            <?php else: ?>
                                <div id="Laravel-RoutingPanel-<?php echo $key; ?>"></div>
                                <script>
                                (function() {
                                    var el = document.getElementById("Laravel-RoutingPanel-<?php echo $key; ?>");
                                    el.innerHTML = TracyDump(<?php echo json_encode($value) ?>);
                                })();
                                </script>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
