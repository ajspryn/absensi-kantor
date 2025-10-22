@if ($paginator->hasPages())
    <div class="d-flex justify-content-between align-items-center">
        <div class="text-start">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="btn btn-s bg-gray-light rounded-xs" style="opacity: 0.5; pointer-events: none;">
                    <i class="bi bi-chevron-left font-12 color-gray-dark"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-s bg-highlight rounded-xs">
                    <i class="bi bi-chevron-left font-12 color-white"></i>
                </a>
            @endif
        </div>

        <div class="text-center flex-grow-1">
            <span class="font-12 font-600 color-theme">
                Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
            </span>
            <br>
            <span class="font-10 opacity-60">
                {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} dari {{ $paginator->total() }} item
            </span>
        </div>

        <div class="text-end">
            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-s bg-highlight rounded-xs">
                    <i class="bi bi-chevron-right font-12 color-white"></i>
                </a>
            @else
                <span class="btn btn-s bg-gray-light rounded-xs" style="opacity: 0.5; pointer-events: none;">
                    <i class="bi bi-chevron-right font-12 color-gray-dark"></i>
                </span>
            @endif
        </div>
    </div>

    {{-- Mobile Navigation Numbers (only show on larger screens) --}}
    @if ($paginator->lastPage() > 1 && $paginator->lastPage() <= 7)
        <div class="d-flex justify-content-center mt-3 d-none d-sm-flex flex-wrap">
            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="btn btn-xs mx-1 mb-1 bg-gray-light color-gray-dark" style="opacity: 0.5; pointer-events: none;">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="btn btn-xs bg-highlight color-white font-600 mx-1 mb-1 rounded-xs">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="btn btn-xs bg-blue-dark color-white mx-1 mb-1 rounded-xs">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>
    @endif
@endif
