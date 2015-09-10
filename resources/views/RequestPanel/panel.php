<div class="laravel-RequestPanel">
    <h1>Request</h1>
    <div class="tracy-inner">
        <?php if (empty($request) === true): ?>
            <p><i>empty</i></p>
        <?php else: ?>
            <table>
                <tbody>
                    <?php foreach ($request as $key => $value): ?>
                        <tr>
                            <th><?php echo strtoupper($key) ?></th>
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
        <?php endif ?>
    </div>
</div>
