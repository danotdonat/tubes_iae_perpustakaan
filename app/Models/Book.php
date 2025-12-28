<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    // Penting agar data bisa masuk lewat Book::create()
    protected $fillable = ['title', 'author', 'isbn', 'stock', 'category'];
}
