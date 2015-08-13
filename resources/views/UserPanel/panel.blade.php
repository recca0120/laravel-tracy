<div class="laravel-UserPanel">
    <h1>{{ $auth->check()===true?'Logged in':'Unlogged' }}</h1>
    @if ($auth->check()===true)
        <p>no identity</p>
    @else
        {!! Tracy\Dumper::toHtml($user, $toHtmlOption) !!}
    @endif
</div>
