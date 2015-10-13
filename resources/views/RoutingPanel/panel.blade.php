@if (empty($currentRoute)===false)
    <style class="tracy-debug">#tracy-debug .laravel-RoutingPanel table{font:9pt/1.5 Consolas,monospace}#tracy-debug .laravel-RoutingPanel .yes td{color:green}#tracy-debug .laravel-RoutingPanel .may td{color:#67F}#tracy-debug .laravel-RoutingPanel pre,#tracy-debug .laravel-RoutingPanel code{display:inline}</style>

    <div class="laravel-RoutingPanel">
        <h1>Route</h1>
        <div class="tracy-inner">
            <table>
                <tbody>
                    @foreach ($currentRoute as $key => $value)
                        <tr>
                            <th>{{ e($key) }}</th>
                            <td>{!! (is_string($value))?e($value):Tracy\Dumper::toHtml($value, $toHtmlOption) !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
