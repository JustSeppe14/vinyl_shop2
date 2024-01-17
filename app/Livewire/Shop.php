<?php

namespace App\Livewire;

use App\Models\Genre;
use App\Models\Record;
use Http;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Shop extends Component
{
    use WithPagination;

    public $perPage = 6;
    public $name;
    public $genre = '%';
    public $price;
    public $priceMin, $priceMax;

    public $loading = 'Please wait...';
    public $selectedRecord;
    public $showModal = false;

    public function updated($property,$value)
    {
        if(in_array($property, ['perPage','name','genre','price']))
            $this->resetPage();
    }

    public function showTracks(Record $record)
    {
        $this->selectedRecord = $record;
        $url = "https://musicbrainz.org/ws/2/release/{$record->mb_id}?inc=recordings&fmt=json";
        $response = Http::get($url)->json();
        $this->selectedRecord['tracks'] = $response['media'][0]['tracks'];
        $this->showModal = true;
//        dump($this->selectedRecord->toArray());
    }

    public function mount()
    {
        $this->priceMin = ceil(Record::min('price'));
        $this->priceMax = ceil(Record::max('price'));
        $this->price = $this->priceMax;
    }

    #[Layout('layouts.vinylshop',['title'=>'Shop','discription'=>'Welcome to our shop'])]
    public function render()
    {
        $allGenres = Genre::has('records')->withCount('records')->get();
        $records = Record::orderBy('artist')
            ->orderBy('title')
            ->searchTitleOrArtist($this->name)
            ->where('genre_id','like',$this->genre)
            ->where('price','<=',$this->price)
            ->paginate($this->perPage);
        return view('livewire.shop',compact('records','allGenres'));
    }
}
