<?php
$dropdown = [
    [
        'menu' => 'Master',
        'description' => 'Khusus Admin',
        'submenu' => [
            [
                'judul' => 'Kontes',
                'description' => 'Anda dapat mengelola kontes yang akan diadakan atau telah diadakan, serta menambahkan kontes baru.',
                'link' => '/master/kontes',
                'role' => ['admin'],
            ],
            [
                'judul' => 'Juri',
                'description' => 'Anda dapat mengelola siapa saja juri yang akan menjadi juri pada kontes, serta menambahkan dan mengubah juri baru.',
                'link' => '/master/juri',
                'role' => ['admin'],
            ],
            [
                'judul' => 'Kriteria Penilaian',
                'description' => 'Anda dapat mengelola penilaian bonsai milik peserta pada kontes, serta menambahkan dan mengubah penilaian baru.',
                'link' => '/master/penilaian',
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
                'judul' => 'Bonsai',
                // 'description' => 'Isi description disini',
                'description' => null,
                'link' => '/kontes/bonsai',
                'role' => ['admin'],
            ],
            [
                'judul' => 'Peserta',
                'description' => null,
                'link' => '/kontes/peserta',
                'role' => ['admin'],
            ],
        ],
        'link' => '#',
        'role' => ['admin'],
    ],
    [
        'menu' => 'Kontes',
        'description' => 'Khusus Juri',
        'submenu' => [],
        'link' => '#',
        'role' => ['juri'],
    ],
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
