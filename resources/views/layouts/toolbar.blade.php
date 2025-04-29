<div class="toolbar py-5 py-lg-15" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-xxl d-flex flex-stack flex-wrap">
        <!-- Toolbar Title -->
        <?php
        // get url
        $url = $_SERVER['REQUEST_URI'];
        
        // explode then slice   
        $url = explode('/', $url);
        $url = array_slice($url, 1);
        ?>

        <div class="d-grid gap-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item {{ $url[0] == 'home' ? 'd-none' : '' }}"><a href="/">Home</a></li>
                    @foreach ($url as $key => $urlItem)
                        @if ($loop->last)
                            <li class="breadcrumb-item active text-capitalize" aria-current="page">{{ $urlItem }}
                            </li>
                        @else
                            <li class="breadcrumb-item text-capitalize"><a
                                    href="/{{ $urlItem }}">{{ $urlItem }}</a></li>
                        @endif
                    @endforeach
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
