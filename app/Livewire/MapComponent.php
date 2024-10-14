<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class MapComponent extends Component
{
    public $latitude;
    public $longitude;

    public function mount(Model $record)
    {
        $this->latitude = '14.5995';
        $this->longitude = '120.9842';
    }
    public function render()
    {
        return view('livewire.map-component');
    }
}
