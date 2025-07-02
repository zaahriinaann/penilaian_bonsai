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
                'judul' => 'Kelola Kriteria Penilaian',
                'description' => 'Anda dapat mengelola penilaian bonsai milik peserta pada kontes, serta menambahkan dan mengubah penilaian baru.',
                'link' => '/master/penilaian',
                'role' => ['admin'],
            ],
            [
                'judul' => 'Kelola Peserta',
                'description' => null,
                'link' => '/master/peserta',
                'role' => ['admin'],
            ],
            [
                'judul' => 'Kelola Bonsai',
                // 'description' => 'Isi description disini',
                'description' => null,
                'link' => '/master/bonsai',
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
                'judul' => 'Pendaftaran',
                'description' => '',
                'link' => '/kontes/pendaftaran',
                'role' => ['admin'],
            ],
            [
                'judul' => 'Penilaian',
                'description' => '',
                'link' => '/kontes/penilaian',
                'role' => ['admin'],
            ],
            [
                'judul' => 'Riwayat Penilaian',
                'description' => '',
                'link' => '/riwayat-penilaian',
                'role' => ['admin'],
            ]
        ],
        'link' => '#',
        'role' => ['admin'],
    ],

    [
        'menu' => 'Riwayat Penilaian',
        'description' => 'Khusus Admin',
        'submenu' => [
            [
                'judul' => 'Riwayat Penilaian',
                'description' => '',
                'link' => '/kontes/pendaftaran',
                'role' => ['admin'],
            ],
        ],
        'link' => '#',
        'role' => ['admin'],
    ],


    // ====================JURIIII=========================
    [
        'menu' => 'Penilaian',
        'description' => 'Khusus Juri',
        'submenu' => [],
        'link' => '#',
        'role' => ['juri'],
    ],
    [
        'menu' => 'Riwayat Penilaian',
        'description' => 'Khusus Juri',
        'submenu' => [
            [
                'judul' => 'Kontes',
                'description' => 'Anda dapat mengelola kontes yang akan diadakan atau telah diadakan, serta menambahkan kontes baru.',
                'link' => '/master/kontes',
                'role' => ['admin'],
            ],
        ],
        'link' => '/riwayat-penilaian',
        'role' => ['juri'],
    ],
];

return $dropdown;
