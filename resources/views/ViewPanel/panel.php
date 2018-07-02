<h1>View</h1>

<div class="tracy-inner Laravel-ViewPanel">
    <div class="tracy-inner-container">
        <table>
            <tr>
                <th>
                    name
                </th>
                <th>
                    data
                </th>
            </tr>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td>
                        <?php echo $row['name'] ?>
                        <?php echo $row['path'] ?>
                    </td>
                    <td>
                        <?php echo Tracy\Dumper::toHtml($row['data'], [Tracy\Dumper::LIVE => true]) ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
</div>
