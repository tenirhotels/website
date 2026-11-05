@if ($paginator->hasPages())
    <div class="row">
        <div class="col-auto col mx-auto">
            <nav class="d-flex">
                @foreach ($elements as $element)
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if($page === $paginator->currentPage())
                                <span class="color-black fz-3 mx-2">{{ $page }}</span>
                            @else
                                <a class="color-dark color-hover-orange fz-3 mx-2" href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </nav>
        </div>
    </div>
@endif
