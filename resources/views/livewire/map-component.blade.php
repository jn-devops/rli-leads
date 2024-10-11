<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <div id="map" style="height: 400px;"></div>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            // Initialize the map
            var map = L.map('map').setView([{{ $latitude }}, {{ $longitude }}], 13);

            // Add the OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add a marker at the provided lat/lng
            var marker = L.marker([{{ $latitude }}, {{ $longitude }}]).addTo(map);
        });
    </script>
</div>
