<h1>View</h1>
<div class="tracy-inner">
    <table>
        <thead>
            <th>
                name
            </th>
            <th>
                data
            </th>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td>
                        <?php echo $log['name'] ?><br />
                        <?php echo $log['path'] ?>
                    </td>
                    <td>
                        <?php if (is_string($log['data']) === true): ?>
                            <?php echo $log['data']  ?>
                        <?php else: ?>
                            <?php echo Tracy\Dumper::toHtml($log['data'], $dumpOption) ?>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
