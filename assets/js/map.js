
document.addEventListener('DOMContentLoaded', () => {
  const m = document.getElementById('events-map');
  if (!m) return;
  const map = L.map('events-map').setView([51.2194, 4.4025], 8); // Antwerp region
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  const events = [
    { title: "Zaterdagmarkt Antwerpen", coords: [51.214, 4.399], date: "2025-09-06" },
    { title: "Foodtruck Festival Gent", coords: [51.042, 3.726], date: "2025-09-12" },
    { title: "Weekmarkt Leuven", coords: [50.879, 4.701], date: "2025-09-14" }
  ];

  events.forEach(e => {
    L.marker(e.coords).addTo(map).bindPopup(`<b>${e.title}</b><br>${e.date}`);
  });
});
