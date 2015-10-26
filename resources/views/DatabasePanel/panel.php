<style class="tracy-debug">#tracy-debug td.laravel-DatabasePanel-sql{background:white!important}#tracy-debug .laravel-DatabasePanel-source{color:#BBB!important}</style>

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
                <tr>
                    <td>
                        <?php echo array_get($query, 'name') ?> / <?php echo array_get($query, 'time') ?> ms
                        <?php if (count($query['explain']) > 0): ?>
                            <br /><a class="tracy-toggle tracy-collapsed" data-ref="#tracy-connection-<?php $key ?>" data-tracy-ref="#tracy-connection-<?php $key ?>">explain</a>
                        <?php endif ?>
                    </td>
                    <td class="laravel-DatabasePanel-sql">
                        <?php echo array_get($query, 'dumpSql') ?>
                        <?php if (count($query['explain']) > 0): ?>
                            <table class="tracy-collapsed laravel-DatabasePanel-explain" id="tracy-connection-<?php $key ?>">
                                <thead>
                                    <tr>
                                        <?php foreach ($query['explain'][0] as $col => $foo): ?>
                                            <th><?php echo $col ?></th>
                                        <?php endforeach ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($query['explain'] as $row): ?>
                                        <tr>
                                            <?php foreach ($row as $col): ?>
                                                <td><?php echo $col ?></td>
                                            <?php endforeach ?>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php endif ?>
                        <?php echo (empty($query['editorLink'])) ?: $query['editorLink'] ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
