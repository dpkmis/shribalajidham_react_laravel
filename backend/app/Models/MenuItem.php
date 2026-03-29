<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['menu_id', 'title', 'url', 'parent_id', 'sort_order', 'meta'];

    public function menu() { return $this->belongsTo(Menu::class); }
    public function parent() { return $this->belongsTo(MenuItem::class, 'parent_id'); }
    public function children() { return $this->hasMany(MenuItem::class, 'parent_id'); }
}
