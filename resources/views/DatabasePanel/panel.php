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
            <?php foreach ($queries as $key => $query): ?>
                <?php
                    $name = array_get($query, 'name');
                    $time = array_get($query, 'time');
                    $dumpSql = array_get($query, 'dumpSql');
                    $editorLink = array_get($query, 'editorLink');
                    $hints = array_get($query, 'hints', []);
                    $explain = array_get($query, 'explain', []);
                ?>
                <tr>
                    <td>
                        <?php echo $name ?> / <?php echo $time ?> ms
                        <?php if (count($explain) > 0): ?>
                            <br /><a class="tracy-toggle tracy-collapsed" data-ref="#tracy-connection-<?php $key ?>" data-tracy-ref="#tracy-connection-<?php $key ?>">explain</a>
                        <?php endif ?>
                    </td>
                    <td class="laravel-DatabasePanel-sql">
                        <?php echo $dumpSql ?>

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
                        <?php if (count($explain) > 0): ?>
                            <br />
                            <table class="tracy-collapsed laravel-DatabasePanel-explain" id="tracy-connection-<?php $key ?>">
                                <thead>
                                    <tr>
                                        <?php foreach ($explain[0] as $col => $foo): ?>
                                            <th><?php echo $col ?></th>
                                        <?php endforeach ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($explain as $row): ?>
                                        <tr>
                                            <?php foreach ($row as $col): ?>
                                                <td><?php echo $col ?></td>
                                            <?php endforeach ?>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php endif ?>
                        <?php echo (empty($editorLink)) ?: $editorLink ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
