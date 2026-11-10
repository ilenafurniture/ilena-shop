<?php

namespace App\Config;

/** Initial menu-only copy, seeded once into app_settings for admin editing. */
final class MenuDescriptionDefaults
{
    public static function entries(): array
    {
        $id = [
            'dresser-3-drawer' => [
                'cabana' => 'Dresser 3 tingkat dengan desain modern minimalis, dilengkapi tiga laci vertikal dan handle mushroom yang elegan. Menggunakan material premium dan memiliki penyimpanan multifungsi.',
                'sorely' => 'Lemari laci 3 tingkat dengan desain minimalis, handle mushroom hitam, dan kaki kokoh. Dibuat dari material premium untuk penyimpanan yang praktis dan stylish.',
            ],
            'dresser-4-drawer' => [
                'cody' => 'Lemari 4 laci series Cody dengan corak serat kayu unik, desain modern, dan material premium untuk penyimpanan multifungsi.',
            ],
            'dresser-5-drawer' => [
                'cabana' => 'Dresser 5 tingkat dengan desain modern minimalis, handle mushroom hitam, dan material premium untuk penyimpanan multifungsi.',
                'cutout' => 'Dresser 5 laci dengan corak kayu unik, desain modern minimalis, dan material premium untuk penyimpanan multifungsi.',
                'socoplate' => 'Lemari laci 5 tingkat dengan desain minimalis, handle ergonomis, serta kaki besi kokoh yang elegan. Dibuat dari material premium untuk penyimpanan multifungsi.',
                'sorely' => 'Lemari laci 5 tingkat series Sorely dengan desain minimalis, handle mushroom hitam, kaki kokoh, dan material premium untuk penyimpanan multifungsi.',
            ],
            'dresser-6-drawer' => [
                'cabana' => 'Dresser 6 tingkat dengan desain modern minimalis, handle mushroom hitam, dan material premium untuk penyimpanan multifungsi.',
                'cutout' => 'Dresser 6 laci series Cutout dengan corak kayu unik, sentuhan modern minimalis bergaya Japanese, dan material premium untuk penyimpanan multifungsi.',
                'metal-frame' => 'Lemari laci 6 tingkat dengan desain modern, perpaduan kayu solid dan rangka besi kokoh, serta material premium untuk penyimpanan multifungsi.',
                'orca' => 'Dresser 6 laci dengan desain modern minimalis, perpaduan warna kayu hangat dan putih, serta material premium yang kokoh untuk penyimpanan multifungsi.',
                'plint-base' => 'Lemari laci 6 tingkat series Plint Base dengan desain minimalis, handle ergonomis, dan material premium untuk penyimpanan multifungsi.',
                'water-case' => 'Lemari laci 6 tingkat series Water Case dengan desain minimalis, handle frameless, dan material premium untuk penyimpanan multifungsi.',
            ],
            'side-table' => [
                'cabana' => 'Nakas multifungsi dengan desain modern minimalis yang stylish. Cocok mengisi sisi samping kasur atau sudut ruangan sebagai tempat buku, hp, hingga lampu tidur.',
                'cody' => 'Meja nakas minimalis yang cocok digunakan sebagai tambahan di berbagai sudut ruangan. Model simpel, menyatu dan melengkapi interior jadi terlihat lebih menawan.',
                'industrial' => 'Hadir dengan desain modern yang unik dan cantik. Menampilkan style edgy perpaduan wiremesh logam dan kayu. Melengkapi interior ruangan jadi sempurna.',
                'cutout' => 'Furniture tambahan untuk ruang tamu, kamar tidur, dan kantor yang akan melengkapi interior rumah jadi lebih aesthetic.',
                'metal-frame' => 'Dibuat dengan menggunakan finishing kayu jati dengan corak khasnya yang unik serta rangka kaki besi finishing satin yang kokoh & awet lama.',
                'orca' => 'Side table minimalis perpaduan corak hitam/putih dan coklat kayu yang kontras memberikan sentuhan cantik & unik. Meja ini menyempurnakan interior Anda.',
                'socoplate' => 'Meja samping ini cocok digunakan sebagai furniture tambahan untuk berbagai ruangan. Dibuat dengan desain modern minimalis dari kayu dan logam yang kuat dan kokoh.',
                'plint-base' => 'Nakas multifungsi untuk pelengkap furniture di kamar tidur maupun sudut ruangan lainnya dengan desain stylish dan minimalis.',
                'water-case' => 'Desain modern dan minimalis yang praktis, dilengkapi sebuah laci handle frameless. Side table ini cocok di samping kasur atau sudut ruangan sebagai tempat buku, hp, hingga lampu tidur tanpa memakan banyak ruang.',
            ],
            'wardrobe' => [
                'cody' => 'Tampil dengan gaya modern klasik khas tekstur kayu yang cocok untuk mengisi kamar tidur dengan memunculkan kesan elegan dan mewah.',
                'industrial' => 'Pilihan tepat untuk mewujudkan ruangan dengan konsep industrial kekinian. Terbuat dari kayu dan wire mesh logam yang kokoh hingga waktu yang lama.',
                'sorely' => 'Dirancang dengan menyatukan 2 elemen material kayu solid dan besi. Hadir dengan desain unik sebagai tempat menyimpan pakaian serta keperluan lainnya.',
                'socoplate' => 'Desain minimalis yang didukung dengan penggunaan material yang berkualitas menjadikan lemari ini tetap tampil artistik dan fungsional walau sudah berdekade lamanya.',
            ],
            'credenza' => [
                'industrial' => 'Mengusung desain modern edgy. Dari material kayu dan jaring kawat logam finishing satin yang kokoh serta unik. Cocok melengkapi ruang tamu, keluarga, hingga lobi kantor.',
            ],
            'console-table' => [
                'cabana' => 'Difungsikan untuk memaksimalkan tampilan ruang kosong di belakang sofa atau dinding agar lebih menarik sesuai preferensi pribadi.',
                'cutout' => 'Meja konsol sebagai furniture tambahan untuk berbagai sudut ruang yang melengkapi interior rumah. Menonjolkan ciri khas kayu bercorak whitewash yang menawan.',
                'metal-frame' => 'Dirancang dengan desain modern klasik, memadukan kayu bercorak cantik dengan rangka kaki besi yang kuat dan kokoh.',
                'plint-base' => 'Meja modern minimalis yang praktis untuk melengkapi interior rumah. Warna natural kayu jati memberi kesan sederhana namun tegas.',
                'sorely' => 'Meja konsol modern minimalis dengan perpaduan kayu dan besi yang unik, stylish, dan menarik sebagai dekorasi ruangan.',
                'cody' => 'Meja tambahan bergaya modern minimalis dengan dua laci, cocok untuk berbagai ruangan. Perpaduan kayu dan logamnya kokoh dan tahan lama.',
                'industrial' => 'Meja konsol bergaya modern dengan sentuhan edgy, memadukan kayu dan wire mesh logam berfinishing satin untuk tampilan ruangan yang lebih berkarakter.',
                'orca' => 'Furniture tambahan untuk melengkapi interior ruang. Meja konsol ini terbuat dari material kayu dengan gaya sederhana dan unik.',
                'socoplate' => 'Meja konsol modern minimalis yang menonjolkan keunikan serat kayu dan logam sebagai ciri khas series Socoplate, cocok untuk berbagai sisi ruangan.',
                'water-case' => 'Meja tambahan modern minimalis dengan desain praktis. Warna natural kayu jati membuat interior terasa lebih lengkap dengan kesan sederhana namun tegas.',
            ],
            'coffee-table' => [
                'cabana' => 'Perpaduan warna cokelat dan hitam yang harmonis memberi tampilan tegas dan elegan pada meja kopi ini. Cocok untuk ruang tamu maupun ruang keluarga.',
                'cutout' => 'Coffee table modern minimalis bergaya Japanese yang mengutamakan fungsi dan kepraktisan, dengan aksen sederhana sebagai daya tarik.',
                'metal-frame' => 'Coffee table dengan perpaduan material kayu dan logam yang menghadirkan tampilan simpel dan elegan.',
                'plint-base' => 'Meja kopi yang cocok untuk ruang tamu atau kantor, memberi sentuhan pelengkap pada interior.',
                'sorely' => 'Perpaduan kayu dan logam menghasilkan coffee table unik yang menarik, dengan material kokoh untuk penggunaan jangka panjang.',
                'cody' => 'Meja kopi bergaya modern dan trendy yang menonjolkan corak serat kayu yang khas.',
                'industrial' => 'Coffee table bergaya industrial dengan perpaduan kayu dan besi wire mesh, cocok untuk ruang tamu hingga lobi kantor.',
                'orca' => 'Meja kopi elegan dengan perpaduan dua warna yang unik, cocok untuk area berkumpul di dalam ruangan.',
                'socoplate' => 'Coffee table bergaya modern klasik yang fleksibel untuk berbagai suasana dan membantu ruang terasa lebih nyaman.',
                'water-case' => 'Coffee table bergaya modern minimalis yang stylish untuk melengkapi keindahan interior ruang.',
            ],
        ];
        $en = [
            'dresser-3-drawer' => [
                'cabana' => 'A modern minimalist three-drawer dresser with elegant mushroom handles, premium materials, and versatile storage.',
                'sorely' => 'A minimalist three-drawer dresser with black mushroom handles and sturdy legs, made from premium materials for practical, stylish storage.',
            ],
            'dresser-4-drawer' => [
                'cody' => 'A four-drawer Cody dresser with distinctive wood grain, modern styling, and premium materials for versatile storage.',
            ],
            'dresser-5-drawer' => [
                'cabana' => 'A modern minimalist five-drawer dresser with black mushroom handles and premium materials for versatile storage.',
                'cutout' => 'A five-drawer dresser with distinctive wood grain, modern minimalist styling, and premium materials for versatile storage.',
                'socoplate' => 'A five-drawer dresser with minimalist styling, ergonomic handles, and elegant, sturdy metal legs. Crafted from premium materials for versatile storage.',
                'sorely' => 'A five-drawer Sorely dresser with minimalist styling, black mushroom handles, sturdy legs, and premium materials for versatile storage.',
            ],
            'dresser-6-drawer' => [
                'cabana' => 'A modern minimalist six-drawer dresser with black mushroom handles and premium materials for versatile storage.',
                'cutout' => 'A six-drawer Cutout dresser with distinctive wood grain, Japanese-inspired minimalist styling, and premium materials for versatile storage.',
                'metal-frame' => 'A modern six-drawer dresser combining solid wood, a sturdy metal frame, and premium materials for versatile storage.',
                'orca' => 'A sturdy six-drawer dresser with modern minimalist styling, warm wood tones, and white accents.',
                'plint-base' => 'A six-drawer Plint Base dresser with minimalist styling, ergonomic handles, and premium materials for versatile storage.',
                'water-case' => 'A six-drawer Water Case dresser with minimalist styling, frameless handles, and premium materials for versatile storage.',
            ],
            'side-table' => [
                'cabana' => 'A versatile, stylish nightstand with a modern minimalist design. Place it beside the bed or in a corner for books, a phone, or a bedside lamp.',
                'cody' => 'A minimalist bedside table that fits neatly into different corners of the home and blends easily with your interior.',
                'industrial' => 'An industrial-style side table that pairs wire mesh metal with wood for a distinctive interior accent.',
                'cutout' => 'An accent piece for a living room, bedroom, or office that helps complete your interior.',
                'metal-frame' => 'A side table with distinctive teak-grain finishing and sturdy legs in a satin-finished metal frame.',
                'orca' => 'A minimalist side table combining contrasting black-and-white details with warm wood tones.',
                'socoplate' => 'A modern minimalist side table made with sturdy wood and metal materials for use in various rooms.',
                'plint-base' => 'A versatile minimalist nightstand for a bedroom or another corner of your home.',
                'water-case' => 'A modern minimalist nightstand with a frameless-handle drawer. Its space-saving design fits beside a bed or in a corner for books, a phone, or a lamp.',
            ],
            'wardrobe' => [
                'cody' => 'The Cody wardrobe pairs classic-modern styling with distinctive wood grain for an elegant bedroom look.',
                'industrial' => 'An industrial-style wardrobe combining wood and metal wire mesh for a sturdy, distinctive look.',
                'sorely' => 'A wardrobe combining solid wood and metal, designed to store clothing and everyday essentials.',
                'socoplate' => 'A minimalist wardrobe made with quality materials, balancing an artistic look with lasting storage function.',
            ],
            'credenza' => [
                'industrial' => 'A credenza with an edgy modern design, combining wood and sturdy satin-finished wire mesh. Its distinctive look suits living rooms, family spaces, and office lobbies.',
            ],
            'console-table' => [
                'cabana' => 'Designed to make the empty space behind a sofa or along a wall more inviting, styled to suit your personal preference.',
                'cutout' => 'An accent table for different corners of the home, featuring the collection’s distinctive, charming whitewash wood grain.',
                'metal-frame' => 'A modern-classic design pairing beautifully grained wood with a strong, sturdy metal frame.',
                'plint-base' => 'A practical modern minimalist console table that completes your interior. Natural teak tones add a simple yet confident look.',
                'sorely' => 'A stylish modern minimalist console table combining wood and metal for a distinctive decorative accent.',
                'cody' => 'A modern minimalist accent table with two drawers for any room, built from durable wood and metal.',
                'industrial' => 'A modern console table with an edgy character, combining wood and satin-finished wire mesh for a distinctive interior accent.',
                'orca' => 'A simple, distinctive wooden console table designed as an accent piece to complement your interior.',
                'socoplate' => 'A modern minimalist console table highlighting the collection’s distinctive wood grain and metal details, suited to different areas of the home.',
                'water-case' => 'A practical modern minimalist accent table in natural teak tones, adding a simple yet confident finish to your interior.',
            ],
            'coffee-table' => [
                'cabana' => 'A harmonious blend of brown and black gives this coffee table a refined, elegant look for living and family rooms.',
                'cutout' => 'A modern minimalist Japanese-inspired coffee table focused on functionality and practicality, with a subtle accent.',
                'metal-frame' => 'A coffee table combining wood and metal for a simple, elegant look.',
                'plint-base' => 'A coffee table suited to a living room or office, adding a finishing touch to the interior.',
                'sorely' => 'A distinctive coffee table pairing wood and metal, built with sturdy materials for lasting use.',
                'cody' => 'A modern, trendy coffee table that highlights distinctive wood grain.',
                'industrial' => 'An industrial-style coffee table combining wood with wire-mesh metal, suited to living rooms and office lobbies.',
                'orca' => 'An elegant coffee table with a distinctive two-tone design for indoor gathering spaces.',
                'socoplate' => 'A modern-classic coffee table that adapts to different interiors and helps create a comfortable room.',
                'water-case' => 'A stylish modern minimalist coffee table that complements your interior.',
            ],
        ] ;
        $entries = [];
        foreach ($id as $type => $collections) {
            foreach ($collections as $collection => $description) {
                $key = $type . '|' . $collection;
                $entries[$key] = ['id' => $description, 'en' => (string) ($en[$type][$collection] ?? '')];
            }
        }
        foreach ($en as $type => $collections) {
            foreach ($collections as $collection => $description) {
                $key = $type . '|' . $collection;
                $entries[$key] ??= ['id' => '', 'en' => $description];
            }
        }
        return $entries;
    }
}
