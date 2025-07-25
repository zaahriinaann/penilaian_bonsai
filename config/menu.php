<?php
$dropdown = [
    [
        // ====================ADMIN=========================
        'menu' => 'Master',
        'description' => 'Khusus Admin',
        'submenu' => [
            [
                'judul' => 'Kelola Kontes',
                'description' => 'Anda dapat mengelola kontes yang akan diadakan atau telah diadakan, serta menambahkan kontes baru.',
                'link' => '/master/kontes',
                'role' => ['admin'],
            ],
            [
                'judul' => 'Kelola Juri',
                'description' => 'Anda dapat mengelola siapa saja juri yang akan menjadi juri pada kontes, serta menambahkan dan mengubah juri baru.',
                'link' => '/master/juri',
                'role' => ['admin'],
            ],
            [
                'judul' => 'Kelola Peserta',
                'description' => 'Anda dapat mengelola peserta, serta menambahkan dan mengubah akun.',
                'link' => '/master/peserta',
                'role' => ['admin'],
            ],
            [
                'judul' => 'Kelola Bonsai',
                'description' => 'Anda dapat mengelola bonsai milik peserta, serta menambahkan dan mengubahnya.',
                'link' => '/master/bonsai',
                'role' => ['admin'],
            ],
            [
                'judul' => 'Kelola Kriteria Penilaian',
                'description' => 'Anda dapat mengelola penilaian bonsai pada kontes, serta menambahkan dan mengubah penilaian baru.',
                'link' => '/master/penilaian',
                'role' => ['admin'],
            ],
            [
                'judul' => 'Fuzzy Rules',
                'description' => 'Anda dapat melihat rule fuzzy yang terbentuk otomatis dari kriteria dan subkriteria.',
                'link' => '/admin/penilaian/fuzzy-rules',
                'role' => ['admin'],
            ],
        ],
        'link' => '#',
        'role' => ['admin'],
    ],
    [
        'menu' => 'Kontes',
        'description' => 'Khusus Admin',
        'submenu' => [
            [
                'judul' => 'Pendaftaran Peserta',
                'description' => 'Anda dapat mendaftarkan peserta dan bonsai miliknya pada kontes yang sedang berlangsung.',
                'link' => '/kontes/pendaftaran-peserta',
                'role' => ['admin'],
            ],
            [
                'judul' => 'Penilaian',
                'description' => 'Juri dapat melakukan penilaian pada kontes yang sedang berlangsung.',
                'link' => '/admin/nilai',
                'role' => ['admin'],
            ],
            [
                'judul' => 'Rekap Nilai',
                'description' => 'Anda dapat melihat rekap nilai dari bonsai milik peserta yang telah dinilai oleh semua juri pada kontes yang sedang berlangsung.',
                'link' => '/rekap-nilai',
                'role' => ['admin'],
            ],
        ],
        'link' => '#',
        'role' => ['admin'],
    ],

    [
        'menu' => 'Riwayat Penilaian',
        'description' => 'Khusus Admin',
        'submenu' => [],
        'link' => '/admin/riwayat/kontes',
        'role' => ['admin'],
    ],


    // ====================JURIIII=========================
    [
        'menu' => 'Penilaian',
        'description' => 'Khusus Juri',
        'submenu' => [
            [
                'judul' => 'Penilaian',
                'description' => 'Juri dapat melakukan penilaian pada kontes yang sedang berlangsung.',
                'link' => '/juri/nilai',
                'role' => ['juri'],
            ],
            [
                'judul' => 'Rekap Nilai',
                'description' => 'Juri dapat melihat rekap nilai dari bonsai milik peserta yang telah dinilai pada kontes yang sedang berlangsung.',
                'link' => '/rekap-nilai',
                'role' => ['juri'],
            ],
        ],
        'link' => '#',
        'role' => ['juri'],
    ],
    [
        'menu' => 'Riwayat Penilaian',
        'description' => 'Anda dapat melihat riwayat penilaian dari semua kontes yang telah diadakan.',
        'submenu' => [],
        'link' => '/juri/riwayat/kontes',
        'role' => ['juri'],
    ],


    // ====================ANGGOTA/PESERTA=========================
    [
        'menu' => 'Bonsai Saya',
        'description' => 'Anda dapat melihat bonsai milik anda yang sudah pernah di daftarkan.',
        'submenu' => [],
        'link' => '/peserta/bonsai-saya',
        'role' => ['anggota'],
    ],
    [
        'menu' => 'Kontes',
        'description' => 'Khusus Peserta',
        'submenu' => [
            [
                'judul' => 'Pendaftaran Peserta',
                'description' => 'Anda dapat mendaftarkan peserta dan bonsai milik anda pada kontes yang sedang berlangsung.',
                'link' => '/kontes/pendaftaran-peserta',
                'role' => ['anggota'],
            ],
            [
                'judul' => 'Rekap Nilai',
                'description' => 'Anda dapat melihat rekap nilai dari bonsai milik anda yang telah dinilai oleh semua juri pada kontes yang sedang berlangsung.',
                'link' => '/rekap-nilai',
                'role' => ['anggota'],
            ],
        ],
        'link' => '#',
        'role' => ['anggota'],
    ],
    [
        'menu' => 'Riwayat Penilaian',
        'description' => 'Anda dapat melihat riwayat penilaian dari semua kontes yang telah diadakan.',
        'submenu' => [],
        'link' => '/peserta/riwayat/kontes',
        'role' => ['anggota'],
    ],
];

return $dropdown;
