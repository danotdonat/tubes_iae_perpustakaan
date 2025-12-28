<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     * Secara default Laravel akan mencari tabel 'books'.
     * @var string
     */
    protected $table = 'books';

    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment).
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'stock',
        'category'
    ];

    /**
     * Casting tipe data kolom.
     * Memastikan 'stock' selalu diperlakukan sebagai integer saat diakses.
     * @var array<string, string>
     */
    protected $casts = [
        'stock' => 'integer',
    ];

    /**
     * Relasi ke Model Borrow (Peminjaman).
     * Satu buku dapat memiliki banyak riwayat peminjaman (One-to-Many).
     * * @return HasMany
     */
    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class);
    }
}
