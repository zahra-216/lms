<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YoutubePlaylist extends Model
{
    protected $fillable = ['course_id', 'level_id', 'playlist_id', 'title'];
}
