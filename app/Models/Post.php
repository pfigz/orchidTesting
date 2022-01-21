<?php

namespace App\Models;

use Orchid\Platform\Database\Seeders\OrchidDatabaseSeeder;

use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Post extends Model
{
    use AsSource, Attachable;

    protected $fillable = [
        'title',
        'description',
        'body',
        'author',
        'hero',
        
    ];

}
