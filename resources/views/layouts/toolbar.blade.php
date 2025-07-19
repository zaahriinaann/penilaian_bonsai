<style>
    .illustration-wrap {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        background: white;
        border-radius: .5rem;
        padding: 16px 32px;
        gap: 24px;
        min-height: 120px;
        width: 100%;
        box-shadow: 0 4px 16px rgba(44, 62, 80, 0.08);
        /* Tambahan biar shadow lembut */
        max-width: 520px;
        margin: 0 auto;
    }

    .illustration {
        position: relative;
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        width: 50px;
        height: 50px;
    }

    .illustration img {
        /* background: red !important; */
        position: absolute;
        left: -140px;
        top: -120px;
        width: 210px;
        height: 210px;
        object-fit: cover;
    }

    .text-greeting {
        flex: 1;
        min-width: 0;
        margin-left: 0;
    }

    .text-greeting h2,
    .text-greeting h3 {
        margin-bottom: 4px;
        font-weight: 700;
        font-size: 1.4rem;
        color: #222;
    }

    .text-greeting p {
        margin-bottom: 0;
        color: #444;
        font-size: 1.05rem;
    }

    @media (max-width: 600px) {
        .illustration-wrap {
            padding: 8px 16px;
            margin-left: 15%;
            margin-top: 15%;
            max-width: 100vw;
            min-height: 10px;
            position: relative;
        }

        .illustration {
            position: relative;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }

        .illustration img {
            position: absolute;
            width: 140px;
            height: 140px;
            left: -75px;
            top: -80px;
        }

        .text-greeting h2,
        .text-greeting h3 {
            font-size: 1rem;
        }

        .text-greeting p {
            font-size: 0.95rem;
        }
    }
</style>

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
        <div class="d-flex align-items-center flex-wrap mt-5 toolbar">
            @if (request()->is('home'))
                <div class="illustration-wrap">
                    <div class="illustration">
                        <img src="{{ asset('assets\media\illustrations\sigma-1\17-dark.png') }}"
                            alt="Hello illustration" />
                    </div>
                    <div class="text-greeting">
                        <h2>Hello, {{ auth()->user()->name }}</h2>
                        <p>Dashboard kamu sudah siap digunakan.</p>
                    </div>
                </div>
            @endif
            @yield('button-toolbar')
        </div>
    </div>
</div>
