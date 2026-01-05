<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book; // Pastikan model Book sudah diimport

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            ['title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'isbn' => '9789793062791', 'stock' => 10, 'category' => 'Novel'],
            ['title' => 'Bumi', 'author' => 'Tere Liye', 'isbn' => '9786020303079', 'stock' => 5, 'category' => 'Fiksi'],
            ['title' => 'Filosofi Teras', 'author' => 'Henry Manampiring', 'isbn' => '9786024125189', 'stock' => 12, 'category' => 'Self Dev'],
            ['title' => 'Atomic Habits', 'author' => 'James Clear', 'isbn' => '9781847941831', 'stock' => 8, 'category' => 'Self Dev'],
            ['title' => 'Negeri 5 Menara', 'author' => 'A. Fuadi', 'isbn' => '9789792248616', 'stock' => 7, 'category' => 'Novel'],
            ['title' => 'Pulang', 'author' => 'Leila S. Chudori', 'isbn' => '9789799105158', 'stock' => 4, 'category' => 'Fiksi Sejarah'],
            ['title' => 'Cantik Itu Luka', 'author' => 'Eka Kurniawan', 'isbn' => '9786020312583', 'stock' => 6, 'category' => 'Sastra'],
            ['title' => 'Dilan 1990', 'author' => 'Pidi Baiq', 'isbn' => '9786027870413', 'stock' => 15, 'category' => 'Romance'],
            ['title' => 'Sang Pemimpi', 'author' => 'Andrea Hirata', 'isbn' => '9789797960742', 'stock' => 9, 'category' => 'Novel'],
            ['title' => 'Hujan', 'author' => 'Tere Liye', 'isbn' => '9786020324784', 'stock' => 11, 'category' => 'Fiksi'],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
