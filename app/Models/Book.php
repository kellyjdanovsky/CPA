<?php

namespace App\Models;

use Eloquent;

class Book extends Eloquent
{
    protected $fillable = ['name', 'description', 'author', 'book_type', 'url', 'location', 'total_copies', 'issued_copies', 'my_class_id'];

    public function my_class()
    {
        return $this->belongsTo(MyClass::class);
    }
}
