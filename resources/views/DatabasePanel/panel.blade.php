<style class="tracy-debug">#tracy-debug td.laravel-DatabasePanel-sql{background:white!important}#tracy-debug .laravel-DatabasePanel-source{color:#BBB!important}</style>

<h1>Queries: {{ $count }}, time: {{ $totalTime }}</h1>
<div class="tracy-inner laravel-DatabasePanel">
    <table>
        <thead>
            <tr>
                <th>Database / Time&nbsp;ms</th>
                <th>SQL Query</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($queries as $key => $query)
                <tr>
                    <td>
                        {{ array_get($query, 'name') }} / {{ array_get($query, 'time') }} ms
                        @if (count($query['explain']) > 0)
                            <br /><a class="tracy-toggle tracy-collapsed" data-ref="#tracy-connection-{{ $key }}" data-tracy-ref="#tracy-connection-{{ $key }}">explain</a>
                        @endif
                    </td>
                    <td class="laravel-DatabasePanel-sql">
                        {!! array_get($query, 'dumpSql') !!}
                        @if (count($query['explain']) > 0)
                            <table class="tracy-collapsed laravel-DatabasePanel-explain" id="tracy-connection-{{ $key }}">
                                <thead>
                                    <tr>
                                        @foreach ($query['explain'][0] as $col => $foo)
                                            <th>{!! $col !!}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($query['explain'] as $row)
                                        <tr>
                                            @foreach ($row as $col)
                                                <td>{!! $col !!}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                        @if (empty($query['editorLink']) === false)
                            {!! $query['editorLink'] !!}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
