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
            <img src="https://ppbindonesia.com/wp-content/uploads/2024/05/cropped-Logo-PPBI-300x155.png" alt=""
                style="width: 100px;">
        </a>
    </div>
    <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
        {{-- Navigation --}}
        <div class="d-flex align-items-stretch" id="kt_header_nav">
            <div class="header-menu align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="header-menu"
                data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
                data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
                data-kt-drawer-toggle="#kt_header_menu_mobile_toggle" data-kt-swapper="true"
                data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
                <div class="menu menu-lg-rounded menu-column menu-lg-row menu-state-bg menu-title-gray-700 menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-400 fw-bold my-5 my-lg-0 align-items-stretch"
                    id="#kt_header_menu" data-kt-menu="true">
                    <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start"
                        class="menu-item {{ request()->is('home') ? 'here' : '' }} menu-lg-down-accordion me-lg-1">
                        <a href="/home" class="menu-link py-3">
                            <span class="menu-title">Dashboard</span>
                            <span class="menu-arrow d-lg-none"></span>
                        </a>
                    </div>
                    <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start"
                        class="menu-item menu-lg-down-accordion me-lg-1">
                        <span class="menu-link py-3">
                            <span class="menu-title">Master</span>
                            <span class="menu-arrow d-lg-none"></span>
                        </span>

                        <?php
                        // Menu Dropdown
                        $dropdownMaster = [
                            [
                                'judul' => 'Kontes Bonsai',
                                'description' => 'Anda dapat mengelola kontes yang akan diadakan atau telah diadakan, serta menambahkan kontes baru.',
                                'link' => '/kontes',
                            ],
                            [
                                'judul' => 'Juri Kontes',
                                'description' => 'Anda dapat mengelola siapa saja juri yang akan menjadi juri pada kontes, serta menambahkan dan mengubah juri baru.',
                                'link' => '/juri',
                            ],
                            [
                                'judul' => 'Bonsai Peserta',
                                'description' => 'Anda dapat mengelola bonsai milik peserta yang hadir pada kontes, serta menambahkan bonsai baru yang akan diikutkan.',
                                'link' => '/bonsai',
                            ],
                            [
                                'judul' => 'Penilaian Bonsai',
                                'description' => 'Anda dapat mengelola penilaian bonsai milik peserta pada kontes, serta menambahkan dan mengubah penilaian baru.',
                                'link' => '/penilaian',
                            ],
                        ];
                        ?>
                        <div
                            class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-rounded-0 py-lg-4 w-lg-225px">
                            @foreach ($dropdownMaster as $dropdownItem)
                                <div class="menu-item">
                                    <a class="menu-link py-3" href="{{ $dropdownItem['link'] }}"
                                        title="{{ $dropdownItem['description'] }}" data-bs-toggle="tooltip"
                                        data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right">
                                        <span class="menu-title">Kelola {{ $dropdownItem['judul'] }}</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Profile --}}
        <div class="d-flex align-items-stretch flex-shrink-0">
            <div class="topbar d-flex align-items-stretch flex-shrink-0">
                <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                    <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click"
                        data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <img alt="Pic" src="assets/media/avatars/150-26.jpg" class="rounded-circle" />
                    </div>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px"
                        data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <div class="symbol symbol-50px me-5">
                                    <img alt="Logo" src="assets/media/avatars/150-26.jpg" />
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bolder d-flex align-items-center fs-5">Max
                                        Smith
                                        <span class="badge badge-light-success fw-bolder fs-8 px-2 py-1 ms-2">Pro</span>
                                    </div>
                                    <a href="#" class="fw-bold text-muted text-hover-primary fs-7">max@kt.com</a>
                                </div>
                            </div>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                            <a href="../dist/account/overview.html" class="menu-link px-5">My
                                Profile</a>
                        </div>
                        <div class="menu-item px-5">
                            <a href="#" class="menu-link px-5">
                                <span class="menu-text">My Audit Logs</span>
                                <span class="menu-badge">
                                    <span class="badge badge-light-danger badge-circle fw-bolder fs-7">3</span>
                                </span>
                            </a>
                        </div>
                        <div class="menu-item px-5" data-kt-menu-trigger="hover" data-kt-menu-placement="right-start">
                            <a href="#" class="menu-link px-5">
                                <span class="menu-title">My Subscription</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-5">Referrals</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-5">Billing</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-5">Payments</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link d-flex flex-stack px-5">Statements
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                            title="View your statements"></i></a>
                                </div>
                                <div class="separator my-2"></div>
                                <div class="menu-item px-3">
                                    <div class="menu-content px-3">
                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                            <input class="form-check-input w-30px h-20px" type="checkbox"
                                                value="1" checked="checked" name="notifications" />
                                            <span class="form-check-label text-muted fs-7">Notifications</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="menu-item px-5">
                            <a href="../dist/account/activity.html" class="menu-link px-5">My
                                Activities</a>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5" data-kt-menu-trigger="hover"
                            data-kt-menu-placement="right-start">
                            <a href="#" class="menu-link px-5">
                                <span class="menu-title position-relative">Language
                                    <span
                                        class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">English
                                        <img class="w-15px h-15px rounded-1 ms-2"
                                            src="assets/media/flags/united-states.svg" alt="" /></span></span>
                            </a>
                            <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link d-flex px-5 active">
                                        <span class="symbol symbol-20px me-4">
                                            <img class="rounded-1" src="assets/media/flags/united-states.svg"
                                                alt="" />
                                        </span>English</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link d-flex px-5">
                                        <span class="symbol symbol-20px me-4">
                                            <img class="rounded-1" src="assets/media/flags/spain.svg"
                                                alt="" />
                                        </span>Spanish</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link d-flex px-5">
                                        <span class="symbol symbol-20px me-4">
                                            <img class="rounded-1" src="assets/media/flags/germany.svg"
                                                alt="" />
                                        </span>German</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link d-flex px-5">
                                        <span class="symbol symbol-20px me-4">
                                            <img class="rounded-1" src="assets/media/flags/japan.svg"
                                                alt="" />
                                        </span>Japanese</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link d-flex px-5">
                                        <span class="symbol symbol-20px me-4">
                                            <img class="rounded-1" src="assets/media/flags/france.svg"
                                                alt="" />
                                        </span>French</a>
                                </div>
                            </div>
                        </div>
                        <div class="menu-item px-5 my-1">
                            <a href="#" class="menu-link px-5">Account Settings</a>
                        </div>
                        <div class="menu-item px-5">
                            <a href="#" class="menu-link px-5">Sign Out</a>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                            <div class="menu-content px-5">
                                <label
                                    class="form-check form-switch form-check-custom form-check-solid pulse pulse-success"
                                    for="kt_user_menu_dark_mode_toggle">
                                    <input class="form-check-input w-30px h-20px" type="checkbox" value="1"
                                        name="mode" id="kt_user_menu_dark_mode_toggle"
                                        data-kt-url="../dist/index.html" />
                                    <span class="pulse-ring ms-n1"></span>
                                    <span class="form-check-label text-gray-600 fs-7">Dark
                                        Mode</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
