<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama agar tidak duplikat saat di-seed ulang
        // \App\Models\Question::truncate(); // Uncomment jika ingin menghapus data lama otomatis

        $data = [
            // --- BAGIAN 1: SOAL PILIHAN GANDA (ORIGINAL) ---
            [
                'question' => 'Berapa kali minimal kita sikat gigi sehari?',
                'options' => json_encode(['1 Kali', '2 Kali', 'Seminggu Sekali']),
                'answer_index' => 1,
                'category' => 'brushing'
            ],
            [
                'question' => 'Kapan kita wajib cuci tangan?',
                'options' => json_encode(['Sebelum Makan', 'Saat Tidur', 'Saat Mandi']),
                'answer_index' => 0,
                'category' => 'handwashing'
            ],
            [
                'question' => 'Bagaimana cara menjaga kebersihan alat reproduksi?',
                'options' => json_encode(['Jarang ganti celana dalam', 'Ganti celana dalam min 2x sehari', 'Pakai celana ketat']),
                'answer_index' => 1,
                'category' => 'reproductive'
            ],

            // --- BAGIAN 2: TANTANGAN CUCI TANGAN (BENAR/SALAH) ---
            [
                'question' => 'Mencuci tangan adalah membersihkan tangan dan jari-jari menggunakan air mengalir dan sabun.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'handwashing'
            ],
            [
                'question' => 'Mencuci tangan dengan bersih dapat mencegah penyakit dan memutus penyebaran kuman.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'handwashing'
            ],
            [
                'question' => 'Sebelum dan sesudah makan diperlukan mencuci tangan pakai sabun.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'handwashing'
            ],
            [
                'question' => 'Mencuci tangan pakai sabun diperlukan setelah kita bermain atau berolahraga.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'handwashing'
            ],
            [
                'question' => 'Waktu yang tepat untuk cuci tangan pakai sabun adalah setelah buang sampah.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'handwashing'
            ],
            [
                'question' => 'Setelah buang air besar dan kecil sebaiknya mencuci tangan pakai sabun.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'handwashing'
            ],
            [
                'question' => 'Apabila tidak mencuci tangan pakai sabun dapat menyebabkan diare (sakit perut).',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'handwashing'
            ],
            [
                'question' => 'Apabila tidak mencuci tangan pakai sabun dapat menyebabkan cacingan.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'handwashing'
            ],
            [
                'question' => 'Ada 6 langkah cara mencuci tangan yang baik dan benar.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'handwashing'
            ],
            [
                'question' => 'Setelah mencuci tangan kita perlu mengeringkan tangan dengan kain lap kering atau tisu.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'handwashing'
            ],

            // --- BAGIAN 3: TANTANGAN KESEHATAN GIGI (BENAR/SALAH) ---
            [
                'question' => 'Saya rajin menyikat gigi maka gigi saya akan bersih dan sehat.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'brushing'
            ],
            [
                'question' => 'Saya menyikat gigi 1 kali sehari karena gigi saya masih bersih.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 1, // Salah
                'category' => 'brushing'
            ],
            [
                'question' => 'Saya tidak perlu menggunakan sikat gigi dan pasta gigi ketika akan menyikat gigi.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 1, // Salah
                'category' => 'brushing'
            ],
            [
                'question' => 'Saya menggunakan pasta gigi sebesar biji kacang.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'brushing'
            ],
            [
                'question' => 'Gigi saya sedang berlubang tetapi saya masih bisa makan dengan nyaman.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 1, // Salah
                'category' => 'brushing'
            ],
            [
                'question' => 'Saya menyikat permukaan gigi bagian belakang atas dan bawah.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'brushing'
            ],
            [
                'question' => 'Saya merapatkan gigi ketika menyikat permukaan gigi bagian luar.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'brushing'
            ],
            [
                'question' => 'Saya menyikat gigi setelah sarapan pagi dan malam hari sebelum tidur.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'brushing'
            ],
            [
                'question' => 'Saya menyikat gigi minimal 2 kali sehari.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'brushing'
            ],
            [
                'question' => 'Saya menyikat gigi bagian belakang dengan gerakan maju mundur.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'brushing'
            ],
            [
                'question' => 'Saya tidak menyikat permukaan lidah.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 1, // Salah
                'category' => 'brushing'
            ],
            [
                'question' => 'Saya tidak perlu menyikat gigi sebelum tidur.',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 1, // Salah
                'category' => 'brushing'
            ],

            // --- BAGIAN 4: TANTANGAN KESEHATAN REPRODUKSI (BENAR/SALAH) ---
            [
                'question' => 'Apa yang terjadi pada remaja putra adalah perubahan suara , tumbuhnya bulu- bulu halus didaerah wajah dan tempat lain , dan timbul jakun',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'reproductive'
            ],
            [
                'question' => 'Mimpi basah merupakan tanda lain pada remaja putra bahwa remaja tersebut mulai akhil baligh atau pubertas',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'reproductive'
            ],
            [
                'question' => 'Menstruasi atau haid merupakan tanda lain dari remaja putri memasuki usia remaja',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'reproductive'
            ],
            [
                'question' => 'Pada masa pubertas akan terjadi perubahan dalam tubuh dan perubahan ini dipengaruhi oleh faktor hormonal dalam tubuh',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'reproductive'
            ],
            [
                'question' => 'Faktor hormonal mempunyai peranan penting untuk proses pertumbuhan dan perkembangan tubuh',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'reproductive'
            ],
            [
                'question' => 'Hormon yang dihasilkan oleh alat reproduksi laki-laki adalah testosteron dan androgen',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'reproductive'
            ],
            [
                'question' => 'Hormon yang dihasilkan oleh alat reproduksi perempuan adalah progesteron',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'reproductive'
            ],
            [
                'question' => 'Tempat terjadinya pembuahan atau pertemuan antara sel telur dengan sel sperma disebut rahim atau uterus',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 1, // Salah (Tuba Fallopi, bukan rahim)
                'category' => 'reproductive'
            ],
            [
                'question' => 'Istilah yang dikenal dalam kesehatan reproduksi jenis kelamin perempuan disebut vagina',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'reproductive'
            ],
            [
                'question' => 'Salah satu fungsi vagina adalah untuk mengeluarkan cairan atau darah yang dihasilkan dari dalam rahim',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'reproductive'
            ],
            [
                'question' => 'Himen atau selaput dara adalah selaput tipis yang menutupi seluruh vagina bagian luar',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 1, // Salah (Menutupi sebagian liang vagina, bukan seluruh bagian luar/vulva)
                'category' => 'reproductive'
            ],
            [
                'question' => 'Masa subur pada wanita adalah masa yang sangat mungkin bagi seorang wanita bisa menstruasi',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 1, // Salah (Masa subur = ovulasi/bisa hamil, bukan menstruasi)
                'category' => 'reproductive'
            ],
            [
                'question' => 'Masa subur pada wanita adalah masa yang mungkin terjadi keputihan',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar (Keputihan fisiologis sering terjadi saat ovulasi)
                'category' => 'reproductive'
            ],
            [
                'question' => 'Kejadian pertemuan sel telur dan sel sperma disebut fertilisasi',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar
                'category' => 'reproductive'
            ],
            [
                'question' => 'Seksual pranikah hanya dilakukan 1-2 kali menimbulkan kehamilan dan risiko negatif bagi remaja',
                'options' => json_encode(['Benar', 'Salah']),
                'answer_index' => 0, // Benar (Sekali saja bisa hamil)
                'category' => 'reproductive'
            ],
        ];

        \App\Models\Question::insert($data);
    }
}