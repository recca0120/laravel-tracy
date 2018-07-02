<h1><?php echo empty($rows) === false ? 'Logged in' : 'Unlogged' ?></h1>

<div class="tracy-inner Laravel-AuthPanel">
    <div class="tracy-inner-container">
        <?php if (empty($rows) === true): ?>
            <p>No identity</p>
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
