<div class="container-xxl d-flex align-items-center">
    <div class="d-flex align-items-center d-lg-none ms-n2 me-3" title="Show aside menu">
        <div class="btn btn-icon btn-custom w-30px h-30px w-md-40px h-md-40px" id="kt_header_menu_mobile_toggle">
            <span class="svg-icon svg-icon-2x">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z"
                        fill="black" />
                    <path opacity="0.3"
                        d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z"
                        fill="black" />
                </svg>
            </span>
        </div>
    </div>
    {{-- Logo --}}
    <div class="header-logo me-5 me-md-10 flex-grow-1 flex-lg-grow-0">
        <a href="/home">
            <img src="{{ asset('assets/media/logos/logo-ppbi-small-nobg.png') }}" alt="" style="width: 75px;">
        </a>
    </div>
    <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
        {{-- Navigation Admin --}}
        <div class="d-flex align-items-stretch" id="kt_header_nav">
            <div class="header-menu align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="header-menu"
                data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
                data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
                data-kt-drawer-toggle="#kt_header_menu_mobile_toggle" data-kt-swapper="true"
                data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
                <div class="menu menu-lg-rounded menu-column menu-lg-row menu-state-bg menu-title-gray-700 menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-400 fw-bold my-5 my-lg-0 align-items-stretch"
                    id="#kt_header_menu" data-kt-menu="true">
                    <div class="menu-item {{ request()->is('home') ? 'here' : '' }} me-lg-1">
                        <a href="/home" class="menu-link py-3">
                            <span>Dashboard</span>
                            {{-- <span class="menu-arrow d-lg-none"></span> --}}
                        </a>
                    </div>

                    <?php
                    // Menu Dropdown
                    $dropdown = config('menu');
                    ?>

                    @foreach ($dropdown as $dropdownItem)
                        @if (in_array(auth()->user()->role, $dropdownItem['role']))
                            @if (!$dropdownItem['submenu'])
                                <div class="menu-item {{ request()->is($dropdownItem['link']) ? 'here' : '' }} me-lg-1">
                                    <a href="{{ $dropdownItem['link'] }}" class="menu-link py-3">
                                        <span>{{ $dropdownItem['menu'] }}</span>
                                    </a>
                                </div>
                            @else
                                <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                                    data-kt-menu-placement="bottom-start"
                                    class="menu-item menu-lg-down-accordion me-lg-1 {{ request()->is($dropdownItem['link'] . '/*') ? 'here' : '' }}">
                                    <span class="menu-link py-3">
                                        <span class="menu-title">{{ $dropdownItem['menu'] }}</span>
                                        <span class="menu-arrow d-lg-none"></span>
                                    </span>

                                    <div
                                        class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-rounded-0 py-lg-4 w-lg-225px">
                                        @foreach ($dropdownItem['submenu'] as $item)
                                            @if (in_array(auth()->user()->role, $item['role']))
                                                <div class="menu-item">
                                                    <a class="menu-link py-3" href="{{ $item['link'] }}"
                                                        title="{{ $item['description'] }}" data-bs-toggle="tooltip"
                                                        data-bs-trigger="hover" data-bs-dismiss="click"
                                                        data-bs-placement="right">
                                                        <span class="menu-title">{{ $item['judul'] }}</span>
                                                    </a>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Profile --}}
        <div class="d-flex align-items-stretch flex-shrink-0">
            <div class="topbar d-flex align-items-stretch flex-shrink-0">
                <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                    <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click"
                        data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <img alt="Pic" src="{{ asset('assets\media\avatars\blank.png') }}"
                            class="rounded-circle" />
                    </div>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 mt-2 fs-6 w-275px"
                        data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <div class="symbol symbol-50px me-5 d-none">
                                    <img alt="Logo" src="{{ asset('assets/media/avatars/150-26.jpg') }}" />
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bolder d-flex align-items-center fs-5 text-capitalize">
                                        {{ Auth::user()->name }}
                                    </div>
                                    <span
                                        class="fw-bold text-muted text-hover-primary fs-7">{{ Auth::user()->email }}</span>
                                    <div class="separator my-2"></div>
                                    <span
                                        class="fw-bold fs-7 badge bg-light-success text-success text-capitalize">{{ Auth::user()->role }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                            <a href="../dist/account/overview.html" class="menu-link px-5">Akun</a>
                        </div>
                        <div class="menu-item px-5 my-1">
                            <a href="#" class="menu-link px-5">Pengaturan Akun</a>
                        </div>
                        <div class="menu-item px-5">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <a onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="menu-link px-5">Keluar</a>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
