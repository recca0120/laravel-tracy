<style class="tracy-debug">
    #tracy-debug td.laravel-DatabasePanel-sql{
        background:white!important
    }
    #tracy-debug .laravel-DatabasePanel-source{
        color:#BBB!important
    }
    #tracy-debug .laravel-DatabasePanel code {
        color:#f00!important;
    }
</style>

<h1>Queries: <?php echo $count ?>, time: <?php echo $totalTime ?></h1>
<div class="tracy-inner laravel-DatabasePanel">
    <table>
        <thead>
            <tr>
                <th>Database / Time&nbsp;ms</th>
                <th>SQL Query</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $key => $log): ?>
                <?php
                    $name = array_get($log, 'name');
                    $time = array_get($log, 'time');
                    $formattedSql = array_get($log, 'formattedSql');
                    $editorLink = array_get($log, 'editorLink');
                    $hints = array_get($log, 'hints', []);
                    $explains = array_get($log, 'explains', []);
                    $explainId = 'tracy-connection-'.$key;
                ?>
                <tr>
                    <td>
                        <?php echo $name ?> / <?php echo $time ?> ms
                        <?php if (count($explains) > 0): ?>
                            <br /><a class="tracy-toggle tracy-collapsed" data-ref="#<?php echo $explainId ?>" data-tracy-ref="#<?php echo $explainId ?>">explain</a>
                        <?php endif ?>
                    </td>
                    <td class="laravel-DatabasePanel-sql">
                        <?php echo $formattedSql ?>

                        <?php if (count($hints) > 0): ?>
                            <br />
                            <?php $i = 0 ?>
                            <table class="" id="">
                                <thead>
                                    <tr>
                                        <th colspan="2">Hints</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($hints as $hint): ?>
                                    <tr>
                                        <td><?php echo ++$i; ?></td><td><?php echo $hint ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif ?>
                        <?php if (count($explains) > 0): ?>
                            <br />
                            <table class="tracy-collapsed laravel-DatabasePanel-explain" id="<?php echo $explainId ?>">
                                <thead>
                                    <tr>
                                        <?php foreach ($explains[0] as $col => $foo): ?>
                                            <th><?php echo $col ?></th>
                                        <?php endforeach ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($explains as $row): ?>
                                        <tr>
                                            <?php foreach ($row as $col): ?>
                                                <td><?php echo $col ?></td>
                                            <?php endforeach ?>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                            <br />
                        <?php endif ?>
                        <?php echo (empty($editorLink)) ?: $editorLink ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
