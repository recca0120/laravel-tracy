<h1>View</h1>

<div class="tracy-inner">
    <table>
        <tr>
            <th>
                name
            </th>
            <th>
                data
            </th>
        </tr>
        <?php foreach ($views as $view): ?>
            <tr>
                <td>
                    <?php echo $view['name'] ?>
                    <?php echo $view['path'] ?>
                </td>
                <td>
                    <?php echo Tracy\Dumper::toHtml($view['data'], [Tracy\Dumper::LIVE => true]) ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
</div>
