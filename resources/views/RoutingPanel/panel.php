<style class="tracy-debug">#tracy-debug .laravel-RoutingPanel table{font:9pt/1.5 Consolas,monospace}#tracy-debug .laravel-RoutingPanel .yes td{color:green}#tracy-debug .laravel-RoutingPanel .may td{color:#67F}#tracy-debug .laravel-RoutingPanel pre,#tracy-debug .laravel-RoutingPanel code{display:inline}</style>

<div class="laravel-RoutingPanel">
    <h1>Route</h1>
    <div class="tracy-inner">
        <table>
            <tbody>
                <?php foreach ($action as $key => $value): ?>
                    <tr>
                        <th><?php echo $key ?></th>
                        <td>
                            <?php if (is_string($value) === true): ?>
                                <?php echo $value ?>
                            <?php else: ?>
                                <?php echo Tracy\Dumper::toHtml($value, $dumpOption) ?>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

