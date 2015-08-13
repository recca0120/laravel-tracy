<style class="tracy-debug">#tracy-debug td.laravel-DbConnectionPanel-sql{background:white!important}#tracy-debug .laravel-DbConnectionPanel-source{color:#BBB!important}</style>

<h1>Queries: {{ $count }}, time: {{ $totalTime }}</h1>
<?php $i=0; ?>
<div class="tracy-inner laravel-DbConnectionPanel">
    <table>
        <thead>
            <tr>
                <th>Time&nbsp;ms</th>
                <th>SQL Query</th>
                <th>Connection</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($queries as $query)
                <tr>
                    <td>
                        {{ array_get($query, 'time') }} ms
                        @if ($query['explainSql'] !== null)
                            <br /><a class="tracy-toggle tracy-collapsed" data-ref="#tracy-connection-{{ $i }}" data-tracy-ref="#tracy-connection-{{ $i }}">explain</a>
                        @endif
                    </td>
                    <td class="laravel-DbConnectionPanel-sql">
                        {!! $query['dumpSql'] !!}
                        @if ($query['explainSql'] !== null)
                            <table class="tracy-collapsed laravel-DbConnectionPanel-explain" id="tracy-connection-{{ $i }}">
                                <thead>
                                    <tr>
                                        @foreach ($query['explainSql'][0] as $col => $foo)
                                            <th>{{ e($col) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($query['explainSql'] as $row)
                                        <tr>
                                            @foreach ($row as $col)
                                                <td>{{ e($col) }}</td>
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
                    <td>{{ $query['connection']->getDatabaseName() }}</td>
                </tr>
                <?php $i++; ?>
            @endforeach
        </tbody>
    </table>
</div>
