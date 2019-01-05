<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    public $timestamps = false;


    protected $fillable = ['name','value'];


    /**
     * Get all of the owning metaable models.
     */
    public function metable()
    {
        return $this->morphTo();
    }
}
