<div class="Laravel-AuthPanel">
    <h1><?php echo is_null($user) === false ? 'Logged in' : 'Unlogged' ?></h1>
    <?php if (is_null($user) === true): ?>
        <p>No identity</p>
    <?php else: ?>
        <table>
            <tbody>
                <?php foreach ($user as $key => $value): ?>
                    <tr>
                        <th><?php echo $key  ?></th>
                        <td>
                            <?php
                                echo Tracy\Dumper::toHtml($value, [Tracy\Dumper::LIVE => true])
                            ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>
</div>
