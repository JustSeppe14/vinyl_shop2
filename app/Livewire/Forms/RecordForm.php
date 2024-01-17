<?php

namespace App\Livewire\Forms;

use App\Models\Record;
use Http;
use Illuminate\Support\Facades\Storage;
use Image;
use Livewire\Attributes\Validate;
use Livewire\Form;

class RecordForm extends Form
{
    public $id = null;
    #[Validate('required', as: 'name of the artist')]
    public $artist = null;
    #[Validate('required', as: 'title for this record')]
    public $title = null;

    public $mb_id = null;
    #[Validate('required|numeric|min:0', as: 'stock')]
    public $stock = null;
    #[Validate('required|numeric|min:0', as: 'price')]
    public $price = null;
    #[Validate('required|exists:genres,id', as: 'genre')]
    public $genre_id = null;
    public $cover = '/storage/covers/no-cover.png';

    // special validation rule for mb_id (unique:records,mb_id,id) for insert and update!
    public function rules()
    {
        return [
            'mb_id' => "required|size:36|unique:records,mb_id,{$this->id}",
        ];
    }

    // $validationAttributes is used to replace the attribute name in the error message
    protected $validationAttributes = [
        'mb_id' => 'MusicBrainz ID',
    ];


    public function getArtistRecord()
    {
        $response = Http::get('https://musicbrainz.org/ws/2/release/' . $this->mb_id . '?inc=artists&fmt=json');
        if ($response->successful()) {
            $data = $response->json();
            $this->artist = $data['artist-credit'][0]['artist']['name'];
            $this->title = $data['title'];
            if ($data['cover-art-archive']['front']) {
                $this->cover = 'https://coverartarchive.org/release/' . $this->mb_id . '/front-250.jpg';
                $originalCover = Image::make($this->cover)->encode('jpg', 75);
                Storage::disk('public')->put('covers/' . $this->mb_id . '.jpg', $originalCover);
            }
        } else {
            $this->artist = null;
            $this->title = null;
            $this->cover = '/storage/covers/no-cover.png';
            $this->addError('mb_id', 'MusicBrainz id not found');
        }
    }
    // read the selected record
    public function read($record)
    {
        $this->id = $record->id;
        $this->artist = $record->artist;
        $this->title = $record->title;
        $this->mb_id = $record->mb_id;
        $this->stock = $record->stock;
        $this->price = $record->price;
        $this->genre_id = $record->genre_id;
        $this->cover = $record->cover;
    }

    // create a new record
    public function create()
    {
        $this->validate();
        Record::create([
            'artist' => $this->artist,
            'title' => $this->title,
            'mb_id' => $this->mb_id,
            'stock' => $this->stock,
            'price' => $this->price,
            'genre_id' => $this->genre_id,
        ]);
    }

    // update the selected record
    public function update(Record $record) {
        $this->validate();
        $record->update([
            'stock' => $this->stock,
            'price' => $this->price,
            'genre_id' => $this->genre_id,
        ]);
    }

    // delete the selected record
    public function delete(Record $record)
    {
        $record->delete();
    }
}
