<div class="container">
    <h1>{{ $title }}</h1>
    <p>Your clean, MVC framework is running smoothly.</p>

    <form method="POST" action="/submit">
        @csrf
        <button type="submit">Test Form</button>
    </form>
</div>