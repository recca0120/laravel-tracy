<div class="laravel-UserPanel">
    <h1>{{ $isLoggedIn===true?'Logged in':'Unlogged' }}</h1>
    @if ($isLoggedIn===false)
        <p>no identity</p>
    @else
        {!! Tracy\Dumper::toHtml($user, $dumpOption) !!}
    @endif
</div>
