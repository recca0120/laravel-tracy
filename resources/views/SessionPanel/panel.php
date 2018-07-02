<h1>Session</h1>

<div class="tracy-inner Laravel-SessionPanel">
    <div class="tracy-inner-container">
        <?php if (empty($rows) === true): ?>
            <p><i>empty</i></p>
        <?php else: ?>
            <table>
                <tbody>
                    <?php foreach ($rows as $key => $value): ?>
                        <tr>
                            <th><?php echo $key  ?></th>
                            <td>
                                <?php echo Tracy\Dumper::toHtml($value, [Tracy\Dumper::LIVE => true]) ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>
</div>
