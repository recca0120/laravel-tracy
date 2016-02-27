<style class="tracy-debug">
#Laravel-ViewPanel {
    width: 700px;
}

#Laravel-ViewPanel table {
    width: 100%;
}
</style>
<div id="Laravel-ViewPanel">
    <h1>View</h1>
    <div class="tracy-inner">
        <table>
            <thead>
                <th>
                    name
                </th>
                <th>
                    data
                </th>
            </thead>
            <tbody>
                <?php foreach ($logs as $key => $log): ?>
                    <tr>
                        <td>
                            <?php echo $log['name'] ?><br />
                            <?php
                                preg_match('/href=\"(.+)\"/', $log['path'], $m);
                                if (count($m) > 1) {
                                    echo '(<a href="'.$m[1].'">source</a>)';
                                }
                            ?>
                        </td>
                        <td>
                            <?php $value = $log['data']; ?>
                            <?php if ($dumpMethod === 'tracy'): ?>
                                <?php
                                    echo Tracy\Dumper::toHtml($value, $config)
                                ?>
                            <?php else: ?>
                                <div id="Laravel-ViewPanel-<?php echo $key; ?>"></div>
                                <script>
                                (function() {
                                    var el = document.getElementById("Laravel-ViewPanel-<?php echo $key; ?>");
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
