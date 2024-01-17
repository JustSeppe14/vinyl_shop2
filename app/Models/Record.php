<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Storage;

class Record extends Model
{
    use HasFactory;

    protected $guarded = ['id','created_at','updated_at']; //NOT mass assignable

    protected function genreName():Attribute
    {
        return Attribute::make(
            get: fn($value,$attributes)=>Genre::find($attributes['genre_id'])->name,
        );
    }

    protected function priceEuro(): Attribute
    {
        return Attribute::make(
            get: fn($value,$attributes)=> '€ ' . number_format($attributes['price'],2)
        );
    }

    public function scopeMaxPrice($query, $price = 100)
    {
        return $query->where('price','<=',$price);
    }
    public function scopeSearchTitleOrArtist($query,$search = '%')
    {
        return $query->where('title','like',"%{$search}%")
            ->orWhere('artist','like',"%{$search}%");
    }

    public function scopeCoverExists($query, $exists = true)
    {
        // make an array with all the mb_id attributes
        $mb_ids = $query->pluck('mb_id');
        // empty array to store 'mb_id's that have a cover
        $covers = [];
        foreach ($mb_ids as $mb_id) {
            // $exists = true: if the cover exists, add the mb_id to the $covers array
            // $exists = false: if the cover does not exist, add the mb_id to the $covers array
            if ($exists) {
                if (Storage::disk('public')->exists('covers/' . $mb_id . '.jpg'))
                    $covers[] = $mb_id;
            } else {
                if (!Storage::disk('public')->exists('covers/' . $mb_id . '.jpg'))
                    $covers[] = $mb_id;
            }
        }
        // return only the records with the mb_id in the $covers array
        return $query->whereIn('mb_id', $covers);
    }

    protected function cover(): Attribute
    {
        return Attribute::make(
            get:  function ($value, $attributes){
                $coverPath = 'covers/' . $attributes['mb_id'] . '.jpg';
                return (Storage::disk('public')->exists($coverPath))
                    ? Storage::url($coverPath)
                    : Storage::url('covers/no-cover.png');
            },
        );
    }

    protected $appends = ['genre_name','price_euro','cover'];
    public function genre()
    {
        return $this->belongsTo(Genre::class)->withDefault(); // a record belongs to a "genre"
    }
}
