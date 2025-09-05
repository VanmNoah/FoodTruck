document.addEventListener('DOMContentLoaded', function() {
    // Initialize the map
    // Vlaanderen bounds: [southWest, northEast]
    const belgiumCenter = [50.8503, 4.3517]; // Brussels coordinates
    const map = L.map('events-map').setView(belgiumCenter, 7); // Zoom level 8 for Belgium

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Fetch locations from API
    fetch('../backend/get_locations.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(locations => {
            const eventsList = document.getElementById('events-list');
            
            if (locations.length === 0) {
                eventsList.innerHTML = '<div class="event-item">Geen evenementen gevonden</div>';
                return;
            }

            locations.forEach(location => {
                // Add marker to map
                const marker = L.marker(location.coordinates)
                    .bindPopup(`<strong>${location.name}</strong><br>${location.date}<br>${location.time}`)
                    .addTo(map);

                // Add location to the list
                const locationElement = document.createElement('div');
                locationElement.className = 'event-item';
                locationElement.innerHTML = `
                    <h3>${location.name}</h3>
                    <p>Datum: ${location.date}</p>
                    <p>Tijd: ${location.time}</p>
                `;
                
                // Highlight marker when hovering over list item
                locationElement.addEventListener('mouseenter', () => {
                    marker.openPopup();
                });
                
                eventsList.appendChild(locationElement);
            });
        })
        .catch(error => {
            console.error('Error loading locations:', error);
            document.getElementById('events-list').innerHTML = 
                '<div class="event-item error">Er is een fout opgetreden bij het laden van de locaties</div>';
        });
});