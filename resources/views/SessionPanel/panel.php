<style class="tracy-debug">#tracy-debug .laravel-SessionPanel .tracy-inner{width:700px}#tracy-debug .laravel-SessionPanel .tracy-inner table{width:100%}#tracy-debug .laravel-SessionPanel-parameters pre{background:#FDF5CE;padding:.4em .7em;border:1px dotted silver;overflow:auto}</style>

<div class="laravel-SessionPanel">
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
                                <?php if (is_string($value) === true): ?>
                                    <?php echo $value  ?>
                                <?php else: ?>
                                    <?php echo Tracy\Dumper::toHtml($value, $dumpOption) ?>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>
</div>
