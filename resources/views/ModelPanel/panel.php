<style class="tracy-debug">
	#tracy-debug td.Laravel-DatabasePanel-sql { background: white !important }
	#tracy-debug .Laravel-DatabasePanel-source { color: #BBB !important }
    #tracy-debug .Laravel-DatabasePanel-hint code { color:#f00!important }
    #tracy-debug .Laravel-DatabasePanel-hint { margin-top: 15px }
    #tracy-debug .Laravel-DatabasePanel-explain { margin-top: 15px }
</style>

<h1>Models: <?php echo $total ?></h1>

<div class="tracy-inner">
    <div class="tracy-inner-container">
        <table>
            <tr>
                <th>Model Name</th>
                <th>Count</th>
            </tr>
            <?php foreach ($models as $name => $count) { ?>
                <tr>
                    <td>
                       <?php echo $name; ?>
                    </td>
                    <td>
                        <?php echo $count; ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
