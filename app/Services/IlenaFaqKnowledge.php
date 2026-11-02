<?php

namespace App\Services;

/** Curated bilingual facts mirrored from the public FAQ page for grounded chat retrieval. */
final class IlenaFaqKnowledge
{
    /** @return array<int, array{title:string,content:string,source_url:string}> */
    public static function documents(string $locale = 'id'): array
    {
        $english = strtolower(trim($locale)) === 'en';
        $faq = [
            ['Product availability', 'Bagaimana cara mengetahui stok produk?', 'Buka halaman produk untuk melihat stok setiap varian. Untuk konfirmasi terbaru, hubungi tim layanan pelanggan.', 'How can I check product availability?', 'Open the product page to see the available stock for each option. For the latest confirmation, contact our customer service team.'],
            ['Promotions', 'Di mana saya bisa melihat promo yang sedang berlaku?', 'Periksa situs Ilena untuk melihat penawaran yang sedang berlaku. Setiap promo dapat memiliki syarat, batas stok, dan ketentuan pengiriman tertentu.', 'Where can I find current promotions?', 'Check the Ilena website for current offers. Promotions may have specific eligibility, stock, and delivery terms.'],
            ['Shipping origin', 'Dari mana pesanan Ilena dikirim?', 'Pesanan dikirim dari fasilitas Ilena di Jatibarang, Mijen, Semarang, Jawa Tengah.', 'Where are Ilena products shipped from?', 'Orders are shipped from our facility in Jatibarang, Mijen, Semarang, Central Java.'],
            ['Product catalogue', 'Di mana saya bisa melihat katalog Ilena?', 'Kakak dapat melihat produk di situs ini dan membuka katalog digital melalui tautan katalog resmi atau menghubungi layanan pelanggan.', 'Where can I find the Ilena catalogue?', 'You can browse our products on this website and access the digital catalogue through the official catalogue link or customer service.'],
            ['Marketplace availability', 'Apakah produk Ilena tersedia di marketplace?', 'Sebagian produk Ilena juga tersedia melalui platform e-commerce Indonesia yang telah dipilih. Ketersediaan dapat berbeda untuk tiap produk.', 'Can I buy Ilena products on e-commerce platforms?', 'Selected Ilena products are also available through established Indonesian e-commerce platforms.'],
            ['Catalogue updates', 'Seberapa sering katalog Ilena diperbarui?', 'Informasi katalog diperbarui saat produk baru diperkenalkan. Untuk detail produk terbaru, gunakan informasi pada situs Ilena.', 'How often is the catalogue updated?', 'We update catalogue information as new products are introduced. Please refer to the website for the latest product details.'],
            ['Guest checkout', 'Bisakah saya memesan tanpa membuat akun?', 'Bisa. Tambahkan produk ke keranjang, lanjutkan ke checkout, lalu isi detail pengiriman sebagai tamu. Akun dapat digunakan untuk mengelola profil dan riwayat pesanan.', 'Can I order without creating an account?', 'Yes. You can add products to your cart, continue to checkout, and provide delivery details as a guest. You may also sign in to manage your account and order history.'],
            ['Order history and tracking', 'Di mana saya bisa melihat atau melacak pesanan?', 'Masuk ke akun dan buka riwayat pesanan. Pelacakan pesanan tamu dapat menggunakan detail pesanan yang diberikan setelah checkout.', 'Where can I view my orders?', 'Sign in and open your account’s order history. Guest-order tracking is available using the order details provided after checkout.'],
            ['Custom furniture and interior', 'Apakah Ilena menerima pesanan furniture custom?', 'Pertanyaan mengenai custom interior dan furniture ditangani secara terpisah. Kunjungi ilenafurniture.net untuk informasi layanan interior.', 'Can I request custom furniture?', 'Custom interior and furniture enquiries are handled separately. Visit ilenafurniture.net to learn about the interior service.'],
            ['Payment methods', 'Metode pembayaran apa yang tersedia?', 'Metode yang tersedia ditampilkan saat checkout dan dapat berbeda. Pilih metode pembayaran di halaman pembayaran untuk melihat instruksinya.', 'Which payment methods are available?', 'Available methods are shown during checkout and may vary. Select a method on the payment page to see its instructions.'],
            ['Payment safety', 'Apakah aman membayar melalui situs Ilena?', 'Gunakan hanya pilihan pembayaran yang muncul saat checkout. Jangan pernah membagikan kata sandi, kode sekali pakai, atau detail kartu lengkap kepada siapa pun.', 'Is it safe to pay on the Ilena website?', 'Use only the payment options shown at checkout. Never share your password, one-time code, or full card details with anyone.'],
            ['Failed payment', 'Apa yang harus dilakukan jika pembayaran gagal?', 'Periksa status pembayaran pada detail pesanan dan ikuti instruksi yang tertera. Hubungi layanan pelanggan jika statusnya belum jelas.', 'What if my payment fails?', 'Check the payment status in your order details, then follow the instructions shown there. Contact customer service if the status remains unclear.'],
            ['Delivery cost', 'Berapa biaya pengiriman pesanan?', 'Biaya pengiriman bergantung pada tujuan, produk, dan promo yang berlaku. Jumlah yang dikenakan ditampilkan sebelum pembayaran dikonfirmasi.', 'How much does delivery cost?', 'Delivery costs depend on the destination, products, and any active promotion. The applicable amount is shown before you confirm payment.'],
            ['Delivery estimate', 'Berapa lama pesanan sampai?', 'Estimasi pengiriman bergantung pada tujuan dan ekspedisi. Periksa detail pesanan atau hubungi layanan pelanggan untuk mendapatkan pembaruan.', 'How long does delivery take?', 'Delivery estimates depend on the destination and expedition. Check the order details or contact customer service for an update.'],
            ['Shipment tracking', 'Bagaimana cara melacak pesanan?', 'Jika informasi pelacakan sudah tersedia, Kakak dapat melihatnya dari detail pesanan.', 'Can I track my order?', 'When tracking information is available, you can view it from your order details.'],
            ['Assembly', 'Apakah produk dikirim dalam keadaan sudah dirakit?', 'Kebutuhan perakitan berbeda untuk setiap produk. Periksa detail produk sebelum memesan.', 'Are products delivered assembled?', 'Assembly requirements vary by product. Please review the product details before ordering.'],
            ['Order cancellation', 'Bagaimana cara membatalkan pesanan?', 'Buka detail pesanan dan periksa apakah pilihan pembatalan tersedia. Jika pilihan tersebut tidak muncul, hubungi layanan pelanggan dengan nomor pesanan.', 'How do I ask to cancel an order?', 'Open your order details and check whether the cancellation option is available. If it is not shown, contact customer service with your order number.'],
            ['Damaged or incorrect item', 'Apa yang harus dilakukan jika barang rusak atau tidak sesuai?', 'Segera hubungi layanan pelanggan Ilena dan sertakan nomor pesanan serta foto masalah yang terlihat jelas agar tim dapat memeriksanya.', 'What should I do if an item arrives damaged or incorrect?', 'Contact Ilena customer service as soon as possible and include your order number and clear photos of the issue so our team can review it.'],
            ['Warranty and replacement', 'Bagaimana mengajukan garansi atau penggantian produk?', 'Kelayakan dan langkah berikutnya bergantung pada produk serta ketentuan yang berlaku. Hubungi layanan pelanggan dengan detail pesanan dan foto pendukung.', 'How are warranty or replacement requests handled?', 'Eligibility and next steps depend on the product and applicable terms. Contact customer service with your order details and supporting photos.'],
            ['Furniture care', 'Bagaimana cara merawat furniture Ilena?', 'Hindari paparan sinar matahari langsung dalam waktu lama dan tempatkan furniture di ruangan dengan sirkulasi udara baik. Ikuti petunjuk perawatan yang disertakan bersama produk.', 'How should I care for my Ilena furniture?', 'Keep furniture away from prolonged direct sunlight and place it in a well-ventilated room. Follow any care instructions supplied with your product.'],
            ['Payment confirmation', 'Bagaimana memastikan pembayaran berhasil?', 'Periksa status pembayaran pada detail pesanan. Pemberitahuan transaksi berhasil juga dikirim melalui email; bila status belum jelas, hubungi layanan pelanggan.', 'How do I confirm that my payment went through?', 'Check the payment status in your order details. A successful-transaction notice is also sent by email; contact customer service if the status is unclear.'],
            ['Delivery coverage', 'Apakah Ilena melayani pengiriman ke luar Jawa?', 'FAQ Ilena menyebut pengiriman tersedia ke seluruh Indonesia. Biaya dan ketersediaan untuk alamat tujuan akan ditampilkan saat checkout sebelum pembayaran dikonfirmasi.', 'Does Ilena deliver outside Java?', 'Ilena’s FAQ states that delivery is available across Indonesia. The cost and availability for your address are shown at checkout before you confirm payment.'],
            ['Physical catalogue', 'Bagaimana mendapatkan katalog fisik Ilena?', 'Katalog fisik dapat diperoleh melalui toko mitra yang bekerja sama dengan Ilena. Periksa halaman Partner untuk mencari informasi mitra atau hubungi layanan pelanggan.', 'How can I get a printed Ilena catalogue?', 'Printed catalogues may be available through Ilena partner stores. Check the Partner page for store information or contact customer service.'],
        ];

        $url = site_url(($english ? 'en/' : '') . 'faq');
        $titlesId = [
            'Product availability' => 'Ketersediaan produk', 'Promotions' => 'Promo dan diskon',
            'Shipping origin' => 'Asal pengiriman', 'Product catalogue' => 'Katalog produk',
            'Marketplace availability' => 'Ketersediaan marketplace', 'Catalogue updates' => 'Pembaruan katalog',
            'Guest checkout' => 'Checkout tanpa akun', 'Order history and tracking' => 'Riwayat dan pelacakan pesanan',
            'Custom furniture and interior' => 'Furniture custom dan interior', 'Payment methods' => 'Metode pembayaran',
            'Payment safety' => 'Keamanan pembayaran', 'Failed payment' => 'Pembayaran gagal',
            'Delivery cost' => 'Biaya pengiriman', 'Delivery estimate' => 'Estimasi pengiriman',
            'Shipment tracking' => 'Pelacakan pesanan', 'Assembly' => 'Perakitan produk',
            'Order cancellation' => 'Pembatalan pesanan', 'Damaged or incorrect item' => 'Barang rusak atau tidak sesuai',
            'Warranty and replacement' => 'Garansi dan penggantian', 'Furniture care' => 'Perawatan furniture',
            'Payment confirmation' => 'Konfirmasi pembayaran', 'Delivery coverage' => 'Cakupan pengiriman',
            'Physical catalogue' => 'Katalog fisik',
        ];
        $rows = [];
        foreach ($faq as [$titleId, $questionId, $answerId, $questionEn, $answerEn]) {
            $title = $english ? $titleId : $titlesId[$titleId];
            $question = $english ? $questionEn : $questionId;
            $answer = $english ? $answerEn : $answerId;
            $rows[] = [
                'title' => $title,
                'content' => "FAQ: {$question}\nAnswer: {$answer}",
                'source_url' => $url,
            ];
        }
        return $rows;
    }
}
