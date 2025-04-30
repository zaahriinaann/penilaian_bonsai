<div class="toolbar py-5 py-lg-15" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-xxl d-flex flex-stack flex-wrap">
        <!-- Toolbar Title -->
        @php
            $segments = request()->segments();
            $count = count($segments);
        @endphp

        <div class="d-grid gap-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    {{-- Home --}}
                    <li class="breadcrumb-item">
                        <a href="/">Home</a>
                    </li>

                    @if ($count === 2)
                        {{-- Example: /master/kontes --}}
                        <li class="breadcrumb-item">
                            <a href="/{{ $segments[0] . '/' . $segments[1] }}">
                                {{ ucfirst($segments[0]) }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active" style="font-weight: bold;" aria-current="page">
                            {{ ucfirst($segments[1]) }}
                        </li>
                    @elseif ($count === 3)
                        {{-- Example: /master/kontes/kontes-dummy --}}
                        <li class="breadcrumb-item">
                            <a href="/{{ $segments[0] }}/{{ $segments[1] }}/{{ $segments[2] }}">
                                {{ ucfirst($segments[0]) }}
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="/{{ $segments[0] }}/{{ $segments[1] }}">
                                {{ ucfirst($segments[1]) }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active" style="font-weight: bold;" aria-current="page">
                            {{ ucwords(str_replace('-', ' ', $segments[2])) }}
                        </li>
                    @endif
                </ol>
            </nav>
            <h3 class="text-white fw-bolder fs-2qx me-5">@yield('title')</h3>
        </div>

        <!-- Toolbar Buttons -->
        <div class="d-flex align-items-center flex-wrap py-2">
            @yield('button-toolbar')
        </div>
    </div>
</div>
