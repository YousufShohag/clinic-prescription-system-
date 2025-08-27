<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TestCategory extends Model
{
    protected $fillable = ['name','slug','description','is_active'];

    // Bind by slug if you like pretty URLs
    public function getRouteKeyName(): string { return 'slug'; }

    protected static function booted(): void
    {
        static::creating(function ($cat) {
            if (empty($cat->slug)) {
                $base = Str::slug($cat->name);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $cat->slug = $slug;
            }
        });
    }
    public function tests()
    {
        return $this->hasMany(Test::class, 'test_category_id');
    }

    public function scopeActive($q){ return $q->where('is_active', true); }
}
