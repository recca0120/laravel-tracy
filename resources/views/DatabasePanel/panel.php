<style class="tracy-debug">
	#tracy-debug td.Laravel-DbConnectionPanel-sql { background: white !important }
	#tracy-debug .Laravel-DbConnectionPanel-source { color: #BBB !important }
    #tracy-debug .Laravel-DbConnectionPanel-hint code { color:#f00!important }
    #tracy-debug .Laravel-DbConnectionPanel-hint { margin-top: 15px }
    #tracy-debug .Laravel-DbConnectionPanel-explain { margin-top: 15px }
</style>

<h1>
    Queries: <?php echo $counter, ($totalTime ? sprintf(', time: %0.3f ms', $totalTime) : '') ?>
</h1>

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
                        <br /><a class="tracy-toggle tracy-collapsed" data-tracy-ref="^tr .Laravel-DbConnectionPanel-hint">hint</a>
                    <?php endif; ?>
                    <?php if (count($query['explains']) > 0): ?>
                        <br /><a class="tracy-toggle tracy-collapsed" data-tracy-ref="^tr .Laravel-DbConnectionPanel-explain">explain</a>
                    <?php endif; ?>
                </td>
                <td class="Laravel-DbConnectionPanel-sql">
                    <?php echo $query['formattedSql'] ?>
                    <?php if (count($query['hints']) > 0): ?>
                        <?php $i = 0 ?>
                        <table class="tracy-collapsed Laravel-DbConnectionPanel-hint" id="">
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
                        <table class="tracy-collapsed Laravel-DbConnectionPanel-explain">
                            <tr>
                                <?php foreach ($query['explains'][0] as $col => $foo): ?>
                                    <th><?php echo htmlSpecialChars($col, ENT_NOQUOTES, 'UTF-8') ?></th>
                                 <?php endforeach ?>
                            </tr>
                            <?php foreach ($query['explains'] as $row): ?>
                                <tr>
                                    <?php foreach ($row as $col): ?>
                                        <td><?php echo htmlSpecialChars($col, ENT_NOQUOTES, 'UTF-8') ?></td>
                                    <?php endforeach ?>
                                </tr>
                            <?php endforeach ?>
                        </table>
                    <?php endif ?>
                    <?php if ($query['editorLink']): ?>
                        <?php echo substr_replace($query['editorLink'], ' class="Laravel-DbConnectionPanel-source"', 2, 0) ?>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
</div>
