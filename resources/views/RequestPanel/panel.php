<h1>Request</h1>

<div class="tracy-inner">
    <table>
        <?php foreach ($request as $key => $value): ?>
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
