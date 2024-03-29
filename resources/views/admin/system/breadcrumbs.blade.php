@unless ($breadcrumbs->isEmpty())
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)

                @if (!is_null($breadcrumb->url) && !$loop->last)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
                @else
                    <li class="breadcrumb-item active">{{ $breadcrumb->title }}</li>
                @endif

            @endforeach
        </ol>
    </nav>

    <h1 class="bd-title">{{ $breadcrumb->title }}</h1>
@endunless
