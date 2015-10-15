<h1>Events: {{ round($totalTime*100, 2) }} ms</h1>
<div class="tracy-inner laravel-EventPanel">
    <table>
        <thead>
            <tr>
                <th>Event</th>
                <th>Arguments</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($events as $event)
                <tr>
                    <th>
                        <span class="tracy-dump-object">{{ array_get($event, 'dispatcher.args.0') }}</span><br />
                        {!! array_get($event, 'editorLink') !!}<br />
                        <span class="tracy-dump-string">{{ round(array_get($event, 'time', 0) * 100, 2) }} ms</span>
                    </th>
                    <td>
                        {!! Tracy\Dumper::toHtml(array_get($event, 'dispatcher.args.1'), array_merge($dumpOption, [
                            Tracy\Dumper::TRUNCATE => 50,
                            Tracy\Dumper::COLLAPSE => TRUE,
                        ])) !!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
