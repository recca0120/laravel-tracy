<div class="laravel-RequestPanel">
    <h1>Request</h1>
    <div class="tracy-inner">
        @if (empty($requestData) === true)
            <p><i>empty</i></p>
        @else
            <table>
                <tbody>
                    @foreach ($requestData as $key => $value)
                        <tr>
                            <th>{{ strtoupper($key) }}</th>
                            <td>{!! (is_string($value))?e($value):Tracy\Dumper::toHtml($value, $toHtmlOption) !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
