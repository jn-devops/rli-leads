<?php

namespace App\Livewire;

use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Homeful\KwYCCheck\Models\Lead;
class MapViewer extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public Lead $record;
    public $lat =0.0;
    public $lng = 0.0;
    public $address ='';
    public function mount(): void
    {
        list($this->lat, $this->lng) = explode(',', trim($this->record->meta['checkin']['body']['inputs']['location'], '[]'));
        $this->address=$this->record->meta->checkin['body']['data']['fieldsExtracted']['address'];
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Form $form): Form
    {
        $model = $form->getModel();
        return $form
            ->schema([
                Map::make('location')
                    ->label('Checkin Coordinates')
                    ->mapControls([
                        'mapTypeControl'    => true,
                        'scaleControl'      => true,
                        'streetViewControl' => true,
                        'rotateControl'     => true,
                        'fullscreenControl' => true,
                        'searchBoxControl'  => false, // creates geocomplete field inside map
                        'zoomControl'       => false,
                    ])
                    ->defaultZoom(12)
                    ->defaultLocation([$this->lat,$this->lng]) ,
                Map::make('location2')
                    ->label('ID Address')
                    ->mapControls([
                        'mapTypeControl'    => true,
                        'scaleControl'      => true,
                        'streetViewControl' => true,
                        'rotateControl'     => true,
                        'fullscreenControl' => true,
                        'searchBoxControl'  => false, // creates geocomplete field inside map
                        'zoomControl'       => false,
                    ])
                    ->defaultLocation([
                        $this->getLatLngFromAddress($this->address)['lat'],
                        $this->getLatLngFromAddress($this->address)['lng'],
                    ])
                    ->defaultZoom(12),
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function getLatLngFromAddress($address) {
        // Replace with your actual Google API key
        $googleMapsApiKey = config('filament-google-maps.key');

        // URL encode the address to make it URL-safe
        $encodedAddress = urlencode($address);
        // Geocoding API URL
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$encodedAddress}&key={$googleMapsApiKey}";

        // Make the API request
        $response = file_get_contents($url);

        // Decode the JSON response
        $json = json_decode($response, true);

        // Check if the request was successful
        if (isset($json['results'][0])) {
            // Get the latitude and longitude
            $lat = $json['results'][0]['geometry']['location']['lat'];
            $lng = $json['results'][0]['geometry']['location']['lng'];

            return [
                'lat' => $lat,
                'lng' => $lng
            ];
        } else {
            // Return null if no result found
            return [
                'lat' => 0.0,
                'lng' => 0.0
            ];
        }
    }
    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->update($data);
    }

    public function render(): View
    {
        return view('livewire.map-viewer');
    }
}
