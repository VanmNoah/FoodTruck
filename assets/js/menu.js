document.addEventListener('DOMContentLoaded', function() {    const menuContainer = document.getElementById('menu-items');    const showMoreBtn = document.getElementById('show-more-menu');    const itemsPerPage = 6;    let currentPage = 1;    // Fetch menu items from database    function loadMenuItems(page) {        fetch(`/api/get_menu.php?page=${page}&limit=${itemsPerPage}`)            .then(response => response.json())            .then(data => {                if (data.items.length > 0) {                    data.items.forEach(item => {                        const menuCard = createMenuCard(item);                        menuContainer.appendChild(menuCard);                    });                                        // Show/hide "Show More" button                    if (data.hasMore) {                        showMoreBtn.classList.remove('hidden');                    } else {                        showMoreBtn.classList.add('hidden');                    }                }            });    }

    // Create menu card element
    function createMenuCard(item) {
        const card = document.createElement('div');
        card.className = 'menu-card';
        card.innerHTML = `
            <img src="${item.image || ''}" alt="${item.title}" class="menu-card-img">
            <div class="menu-card-content">
                <h3>${item.title}</h3>
                <p>${item.description}</p>
                <span class="price">€${item.price.toFixed(2)}</span>
            </div>
        `;
        return card;
    }

    // Load initial items
    loadMenuItems(currentPage);

    // Handle "Show More" button click
    showMoreBtn.addEventListener('click', () => {
        currentPage++;
        loadMenuItems(currentPage);
    });
});