<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPlaceholder extends Model
{
    protected $table = "job_placeholders";

    protected $fillable = ['title','description','job_category_id','responsibilities','requirements'];
}
