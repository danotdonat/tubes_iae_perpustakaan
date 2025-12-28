<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrow extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     * @var string
     */
    protected $table = 'borrows';

    /**
     * Kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     * @var array<int, string>
     */
    protected $fillable = [
        'member_id',
        'book_id',
        'borrow_date',
        'return_date',
        'status',
    ];

    /**
     * Casting tipe data kolom.
     * Sangat penting agar borrow_date otomatis menjadi objek Carbon.
     * @var array<string, string>
     */
    protected $casts = [
        'borrow_date' => 'datetime',
        'return_date' => 'datetime',
        'member_id'   => 'integer',
        'book_id'     => 'integer',
    ];

    /**
     * Relasi ke Model Member (Anggota).
     * @return BelongsTo
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Relasi ke Model Book (Buku).
     * @return BelongsTo
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
