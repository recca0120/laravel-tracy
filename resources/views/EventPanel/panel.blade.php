<h1>Events: {{ array_sum(array_pluck($events, 'time')) }} ms</h1>
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
                        <span class="tracy-dump-object">{{ array_get($event, 'dispatcher.args.0') }}</span>
                        <br />
                        <span class="tracy-dump-string">{{ array_get($event, 'time') }} ms</span>
                    </th>
                    <td>
                        {!! Tracy\Dumper::toHtml(array_get($event, 'dispatcher.args.1'), array_merge($toHtmlOption, [
                            Tracy\Dumper::TRUNCATE => 50,
                            Tracy\Dumper::COLLAPSE => TRUE,
                        ])) !!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
