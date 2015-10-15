<div class="laravel-RequestPanel">
    <h1>Request</h1>
    <div class="tracy-inner">
        @if (empty($request) === true)
            <p><i>empty</i></p>
        @else
            <table>
                <tbody>
                    @foreach ($request as $key => $value)
                        <tr>
                            <th>{{ strtoupper($key) }}</th>
                            <td>
                                @if (is_string($value) === true)
                                    {{ $value }}
                                @else
                                    {!! Tracy\Dumper::toHtml($value, $dumpOption) !!}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
