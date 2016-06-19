<h1>Events: <?php echo round($totalTime * 100, 2) ?> ms</h1>
<div class="tracy-inner">
    <table>
        <tr>
            <th>Event</th>
            <th>Execute Time</th>
        </tr>
        <?php foreach ($events as $key => $value): ?>
            <tr>
                <th>
                    <span class="tracy-dump-object"><?php echo array_get($value, 'firing') ?></span><br />
                    <?php echo array_get($value, 'editorLink') ?><br />
                </th>
                <td>
                    <?php
                        echo Tracy\Dumper::toHtml(array_get($value, 'params'), [
                            Tracy\Dumper::LIVE     => true,
                            Tracy\Dumper::TRUNCATE => 50,
                            Tracy\Dumper::COLLAPSE => true,
                        ]);
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
