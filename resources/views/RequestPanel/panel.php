<h1>Request</h1>

<div class="tracy-inner Laravel-RequestPanel">
    <table>
        <?php foreach ($rows as $key => $value): ?>
            <tr>
                <th>
                    <?php echo ucfirst($key) ?>
                </th>
                <td>
                    <?php echo Tracy\Dumper::toHtml($value, [Tracy\Dumper::LIVE => true]) ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
</div>
