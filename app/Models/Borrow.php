<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    use HasFactory;

    /**
     * Kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     * Sesuai dengan struktur tabel peminjaman di database.
     */
    protected $fillable = [
        'member_id',
        'book_id',
        'borrow_date',
        'return_date',
        'status',
    ];

    /**
     * Relasi ke Model Member (Anggota).
     * Menandakan bahwa satu data pinjam dimiliki oleh satu anggota.
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Relasi ke Model Book (Buku).
     * Menandakan bahwa satu data pinjam merujuk pada satu buku.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
