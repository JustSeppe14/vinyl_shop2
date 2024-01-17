<?php

namespace App\Livewire\Admin;

use App\Livewire\Forms\RecordForm;
use App\Models\Genre;
use App\Models\Record;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Records extends Component
{
    use WithPagination;
    public $search;
    public $noStock = false;
    public $noCover = false;
    public $perPage = 5;
    public $showModal = false;
    public RecordForm $form;



    public function updated($propertyName, $propertyValue)
    {
        if(in_array($propertyName, ['search','noCover','noStock','perPage']))
            $this->resetPage();
    }

    public function newRecord()
    {
        $this->form->reset();
        $this->resetErrorBag();
        $this->showModal= true;
    }
    public function getDataFromMusicbrainzApi()
    {
        $this->validateOnly('form.mb_id');
        $this->form->getArtistRecord();
    }

    public function editRecord(Record $record)
    {
        $this->resetErrorBag();
        $this->form->fill($record);
        $this->showModal = true;
    }

    public function updateRecord(Record $record)
    {
        $this->form->update($record);
        $this->showModal = false;
        $this->dispatch('swal:toast', [
            'background' => 'success',
            'html' => "The record <b><i>{$this->form->title}</i></b> from <b><i>{$this->form->artist}</i></b> has been updated",
            'icon' => 'success',
        ]);
    }

    public function createRecord()
    {
        $this->form->create();
        $this->showModal = false;
        $this->dispatch('swal:toast', [
            'background' => 'success',
            'html' => "The record <b><i>{$this->form->title}</i></b> has been added",
            'icon' => 'success',
        ]);
    }

    public function deleteRecord(Record $record)
    {
        $this->form->delete($record);
        $this->dispatch('swal:toast', [
            'background' => 'success',
            'html' => "The record <b><i>{$record->title}</i></b> from <b><i>{$record->artist}</i></b> has been deleted",
            'icon' => 'success',
        ]);
    }


    #[Layout('layouts.vinylshop',['title'=>'Records','description'=>'Manage the records of your vinyl records',])]
    public function render()
    {

        $query= Record::orderBy('artist')
            ->orderBy('title')
            ->searchTitleOrArtist($this->search);
        if ($this->noStock)
            $query->where('stock',false);
        if ($this->noCover)
            $query->coverExists(false);

        $records = $query
            ->paginate($this->perPage);

        $genres = Genre::orderBy('name')->get();
        return view('livewire.admin.records',compact('records','genres'));
    }
}
