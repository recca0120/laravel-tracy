<div class="Laravel-UserPanel">
    <h1><?php echo $logged === true ? 'Logged in' : 'Unlogged' ?></h1>
    <?php if ($logged === false): ?>
        <p>no identity</p>
    <?php else: ?>
        <table>
            <tbody>
                <?php foreach ($user as $key => $value): ?>
                    <tr>
                        <th><?php echo $key  ?></th>
                        <td>
                            <div id="Laravel-UserPanel-<?php echo $key; ?>">
                            </div>
                            <script>
                            (function() {
                                var el = document.getElementById("Laravel-UserPanel-<?php echo $key; ?>");
                                el.innerHTML = TracyDump(<?php echo json_encode($value) ?>);
                            })();
                            </script>
                            <?php
                                // echo Tracy\Dumper::toHtml($value, $dumpOption)
                            ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>
</div>
