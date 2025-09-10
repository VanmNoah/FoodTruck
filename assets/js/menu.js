
document.addEventListener('DOMContentLoaded', () => {
  const items = [
    { name: "Pasta Pesto", desc: "Verse basilicum pesto, pijnboompitten, Parmezaan.", price: "8.50" },
    { name: "Pasta Tonno", desc: "Tonijn, rode ui, kappertjes, citroen.", price: "9.00" },
    { name: "Pasta Caprese", desc: "Tomaat, mozzarella, basilicum, balsamico.", price: "8.00" },
    { name: "Pasta Pollo", desc: "Gegrilde kip, rucola, citroenmayo.", price: "9.50" }
  ];
  const container = document.getElementById('menu-items');
  if (!container) return;
  if (container.children.length > 0) return;
  items.forEach(d => {
    const card = document.createElement('div');
    card.className = 'menu-card';
    card.innerHTML = `<h3>${d.name}</h3><p>${d.desc}</p><div class="price">&euro;${d.price}</div>`;
    container.appendChild(card);
  });
});
