<div class="toolbar py-5 py-lg-15" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-xxl d-flex flex-stack flex-wrap">
        <!-- Toolbar Title -->
        <h3 class="text-white fw-bolder fs-2qx me-5">@yield('title')</h3>

        <!-- Toolbar Buttons -->
        <div class="d-flex align-items-center flex-wrap py-2">
            @yield('button-toolbar')
        </div>
    </div>
</div>
