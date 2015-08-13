<style class="tracy-debug">#tracy-debug .laravel-SessionPanel .tracy-inner{width:700px}#tracy-debug .laravel-SessionPanel .tracy-inner table{width:100%}#tracy-debug .laravel-SessionPanel-parameters pre{background:#FDF5CE;padding:.4em .7em;border:1px dotted silver;overflow:auto}</style>

<div class="laravel-SessionPanel">
    <h1>Session #{{ e($sessionId) }} (Lifetime: {{ e(array_get($config, 'lifetime')) }})</h1>
    <div class="tracy-inner">
        @if (empty($sessionData) === true)
            <p><i>empty</i></p>
        @else
            <table>
                <tbody>
                    @foreach ($sessionData as $key => $value)
                        <tr>
                            <th>{{ $key }}</th>
                            <td>{!! (is_string($value))?e($value):Tracy\Dumper::toHtml($value, $toHtmlOption) !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
