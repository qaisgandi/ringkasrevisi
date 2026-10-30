<?php

namespace App\Services\Chatbot;

/**
 * Definisi ringkas istilah statistik yang sering ditanyakan pengguna umum.
 * Dipakai saat AI tidak tersedia agar jawaban tetap akurat dan konsisten.
 */
class StatistikGlossary
{
    private const ENTRIES = [
        'ipm' => [
            'aliases' => ['ipm', 'indeks pembangunan manusia'],
            'label' => 'IPM (Indeks Pembangunan Manusia)',
            'definition' => 'IPM mengukur capaian pembangunan manusia dari tiga dimensi: umur panjang dan hidup sehat, pengetahuan, serta standar hidup layak. Nilainya berkisar 0–100; semakin tinggi, semakin baik capaian pembangunan manusianya.',
        ],
        'tpt' => [
            'aliases' => ['tpt', 'tingkat pengangguran terbuka'],
            'label' => 'TPT (Tingkat Pengangguran Terbuka)',
            'definition' => 'TPT adalah persentase penduduk yang termasuk penganggur terhadap total angkatan kerja. Penganggur mencakup mereka yang tidak bekerja, sedang mencari pekerjaan, atau mempersiapkan usaha.',
        ],
        'tpak' => [
            'aliases' => ['tpak', 'tingkat partisipasi angkatan kerja'],
            'label' => 'TPAK (Tingkat Partisipasi Angkatan Kerja)',
            'definition' => 'TPAK menunjukkan persentase angkatan kerja terhadap penduduk usia kerja. Angka ini menggambarkan seberapa besar penduduk usia kerja yang aktif secara ekonomi, baik bekerja maupun mencari pekerjaan.',
        ],
        'pdrb' => [
            'aliases' => ['pdrb', 'produk domestik regional bruto'],
            'label' => 'PDRB (Produk Domestik Regional Bruto)',
            'definition' => 'PDRB adalah nilai tambah barang dan jasa yang dihasilkan suatu wilayah dalam periode tertentu. PDRB harga berlaku menggambarkan nilai nominal, sedangkan PDRB harga konstan dipakai untuk melihat pertumbuhan riil.',
        ],
        'gini' => [
            'aliases' => ['gini', 'gini ratio', 'rasio gini'],
            'label' => 'Rasio Gini',
            'definition' => 'Rasio Gini mengukur ketimpangan distribusi pengeluaran penduduk. Nilainya 0–1; semakin mendekati 1, semakin tinggi ketimpangan. Nilai 0 berarti distribusi pengeluaran merata sempurna.',
        ],
        'inflasi' => [
            'aliases' => ['inflasi', 'laju inflasi'],
            'label' => 'Inflasi',
            'definition' => 'Inflasi adalah kenaikan harga barang dan jasa secara umum dalam suatu periode. Inflasi umumnya diukur dari perubahan Indeks Harga Konsumen (IHK), baik dibanding bulan sebelumnya (month-to-month) maupun tahun sebelumnya (year-on-year).',
        ],
        'kemiskinan' => [
            'aliases' => ['kemiskinan', 'garis kemiskinan'],
            'label' => 'Kemiskinan',
            'definition' => 'Penduduk miskin adalah penduduk dengan rata-rata pengeluaran per kapita per bulan di bawah Garis Kemiskinan. Garis Kemiskinan terdiri dari komponen makanan dan non-makanan yang dihitung BPS setiap periode.',
        ],
        'hls' => [
            'aliases' => ['hls', 'harapan lama sekolah'],
            'label' => 'HLS (Harapan Lama Sekolah)',
            'definition' => 'HLS adalah lamanya sekolah (dalam tahun) yang diharapkan akan dirasakan oleh anak pada umur tertentu di masa mendatang. HLS mencerminkan peluang pendidikan yang tersedia di suatu wilayah.',
        ],
        'rls' => [
            'aliases' => ['rls', 'rata rata lama sekolah'],
            'label' => 'RLS (Rata-rata Lama Sekolah)',
            'definition' => 'RLS adalah rata-rata jumlah tahun yang dihabiskan penduduk usia 25 tahun ke atas untuk menempuh pendidikan formal. RLS menggambarkan capaian pendidikan penduduk yang sudah dewasa.',
        ],
        'uhh' => [
            'aliases' => ['uhh', 'umur harapan hidup', 'angka harapan hidup'],
            'label' => 'UHH (Umur Harapan Hidup)',
            'definition' => 'UHH adalah rata-rata perkiraan jumlah tahun yang dapat ditempuh seseorang sejak lahir. Angka ini mencerminkan derajat kesehatan dan kualitas hidup penduduk suatu wilayah.',
        ],
        'ipg' => [
            'aliases' => ['ipg', 'indeks pembangunan gender'],
            'label' => 'IPG (Indeks Pembangunan Gender)',
            'definition' => 'IPG mengukur pencapaian pembangunan manusia antara laki-laki dan perempuan. Semakin mendekati 100, semakin setara capaian pembangunan antara kedua kelompok tersebut.',
        ],
        'stunting' => [
            'aliases' => ['stunting', 'tengkes'],
            'label' => 'Stunting',
            'definition' => 'Stunting adalah kondisi balita yang tinggi badannya berada di bawah standar usianya akibat kekurangan gizi kronis dan infeksi berulang. Stunting diukur melalui indeks tinggi badan menurut umur (TB/U).',
        ],
        'penduduk' => [
            'aliases' => ['penduduk', 'kependudukan', 'demografi'],
            'label' => 'Penduduk',
            'definition' => 'Penduduk adalah semua orang yang berdomisili di suatu wilayah pada periode tertentu. Data kependudukan mencakup jumlah, struktur umur, jenis kelamin, persebaran, dan pertumbuhan penduduk.',
        ],
        'sensus' => [
            'aliases' => ['sensus'],
            'label' => 'Sensus',
            'definition' => 'Sensus adalah pencacahan lengkap seluruh penduduk atau seluruh unit usaha di suatu wilayah pada waktu tertentu. Berbeda dengan survei yang menggunakan sampel, sensus mendata seluruh populasi.',
        ],
        'survei' => [
            'aliases' => ['survei', 'survey'],
            'label' => 'Survei',
            'definition' => 'Survei adalah pengumpulan data dari sebagian populasi (sampel) untuk memperkirakan karakteristik populasi secara keseluruhan. Contohnya Sakernas untuk ketenagakerjaan dan Susenas untuk sosial ekonomi.',
        ],
    ];

    /**
     * @return array{key: string, label: string, definition: string}|null
     */
    public function find(string $text): ?array
    {
        $normalized = mb_strtolower(trim($text));

        if ($normalized === '') {
            return null;
        }

        $best = null;
        $bestLength = 0;

        foreach (self::ENTRIES as $key => $entry) {
            foreach ($entry['aliases'] as $alias) {
                if (mb_strlen($alias) <= $bestLength) {
                    continue;
                }

                $pattern = '/(?<![\p{L}\p{N}])' . preg_quote($alias, '/') . '(?![\p{L}\p{N}])/u';

                if (preg_match($pattern, $normalized)) {
                    $best = [
                        'key' => $key,
                        'label' => $entry['label'],
                        'definition' => $entry['definition'],
                    ];
                    $bestLength = mb_strlen($alias);
                }
            }
        }

        return $best;
    }
}
