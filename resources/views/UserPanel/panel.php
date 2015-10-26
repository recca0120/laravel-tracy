<div class="laravel-UserPanel">
    <h1><?php echo $isLoggedIn === true ? 'Logged in' : 'Unlogged' ?></h1>
    <?php if ($isLoggedIn === false): ?>
        <p>no identity</p>
    <?php else: ?>
        <table>
            <tbody>
                <?php foreach ($user as $key => $value): ?>
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
