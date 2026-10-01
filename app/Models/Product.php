<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
 protected $fillable = [
 'category_id', 'name', 'slug', 'description',
 'price', 'discount_price', 'stock', 'size', 'color', 'is_active',
 ];
 public function category()
 {
 return $this->belongsTo(Category::class);
 }
 public function images()
 {
 return $this->hasMany(ProductImage::class);
 }
 public function mainImage()
 {
 return $this->hasOne(ProductImage::class)->where('is_main', true);
 }
}
