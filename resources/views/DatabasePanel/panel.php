<style class="tracy-debug">
	#tracy-debug td.Laravel-DatabasePanel-sql { background: white !important }
	#tracy-debug .Laravel-DatabasePanel-source { color: #BBB !important }
    #tracy-debug .Laravel-DatabasePanel-hint code { color:#f00!important }
    #tracy-debug .Laravel-DatabasePanel-hint { margin-top: 15px }
    #tracy-debug .Laravel-DatabasePanel-explain { margin-top: 15px }
</style>

<h1>Queries: <?php echo $counter, ($totalTime ? sprintf(', time: %0.3f ms', $totalTime) : '') ?></h1>

<div class="tracy-inner">
    <table>
        <tr>
            <th>Time&nbsp;ms / Name</th>
            <th>SQL Query</th>
        </tr>
        <?php foreach ($queries as $query): ?>
            <tr>
                <td>
                    <?php echo sprintf('%0.3f', $query['time']) ?> / <?php echo $query['name'] ?>
                    <?php if (count($query['hints']) > 0): ?>
                        <br /><a class="tracy-toggle tracy-collapsed" data-tracy-ref="^tr .Laravel-DatabasePanel-hint">hint</a>
                    <?php endif; ?>
                    <?php if (count($query['explains']) > 0): ?>
                        <br /><a class="tracy-toggle tracy-collapsed" data-tracy-ref="^tr .Laravel-DatabasePanel-explain">explain</a>
                    <?php endif; ?>
                </td>
                <td class="Laravel-DatabasePanel-sql">
                    <?php echo $query['hightlight'] ?>
                    <?php if (count($query['hints']) > 0): ?>
                        <?php $i = 0 ?>
                        <table class="tracy-collapsed Laravel-DatabasePanel-hint" id="">
                            <thead>
                                <tr>
                                    <th colspan="2">Hints</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($query['hints'] as $hint): ?>
                                <tr>
                                    <td><?php echo ++$i; ?></td><td><?php echo $hint ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif ?>

                    <?php if ($query['explains']): ?>
                        <table class="tracy-collapsed Laravel-DatabasePanel-explain">
                            <tr>
                                <?php foreach ($query['explains'][0] as $col => $value): ?>
                                    <th><?php echo htmlspecialchars($col, ENT_NOQUOTES, 'UTF-8') ?></th>
                                 <?php endforeach ?>
                            </tr>
                            <?php foreach ($query['explains'] as $row): ?>
                                <tr>
                                    <?php foreach ($row as $col): ?>
                                        <td><?php echo htmlspecialchars($col, ENT_NOQUOTES, 'UTF-8') ?></td>
                                    <?php endforeach ?>
                                </tr>
                            <?php endforeach ?>
                        </table>
                    <?php endif ?>
                    <?php if ($query['editorLink']): ?>
                        <?php echo substr_replace($query['editorLink'], ' class="Laravel-DatabasePanel-source"', 2, 0) ?>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
</div>
