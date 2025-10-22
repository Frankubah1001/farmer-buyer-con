let currentPage = 1;
let totalPages = 0;

document.addEventListener('DOMContentLoaded', function() {
    loadProduceData(1);
    loadUniqueFilters();

    // Event listeners for filters (removed availability filter)
    ['filter_produce', 'filter_condition'].forEach(id => {
        document.getElementById(id).addEventListener('change', () => loadProduceData(1));
    });

    // View image event
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('view-image-btn')) {
            const imagePath = event.target.dataset.imagePath;
            const fullImagePath = imagePath; // Use backend-provided path directly
            $('#modalImage').attr('src', fullImagePath);
            $('#imageModal').modal('show');
        }
    });

    // Delete event
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-btn')) {
            const listingId = event.target.dataset.listingId;
            if (confirm('Are you sure you want to remove this listing?')) {
                fetch('delete_produce.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `prod_id=${listingId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Listing removed successfully.');
                        loadProduceData(currentPage);
                    } else {
                        alert('Error removing listing: ' + (data.error || 'Unknown error'));
                        console.error('Delete error:', data.error);
                    }
                })
                .catch(error => {
                    alert('Network error occurred.');
                    console.error('Delete network error:', error);
                });
            }
        }
    });
});

function loadProduceData(page) {
    currentPage = page;
    const produceFilter = encodeURIComponent(document.getElementById('filter_produce').value || '');
    const conditionFilter = encodeURIComponent(document.getElementById('filter_condition').value || '');
    const url = `get_produce_info.php?page=${page}&produce=${produceFilter}&condition=${conditionFilter}`;

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Fetched data:', data); // Debug log
            if (data.error) {
                populateProduceTable({ error: data.error });
                document.getElementById('pagination').style.display = 'none';
            } else {
                populateProduceTable(data.data);
                totalPages = data.total_pages || 1;
                populatePagination(totalPages, currentPage);
                document.getElementById('pagination').style.display = (totalPages > 1) ? 'flex' : 'none';
            }
        })
        .catch(error => {
            console.error('Error fetching produce data:', error);
            populateProduceTable({ error: 'Failed to fetch data.' });
            document.getElementById('pagination').style.display = 'none';
        });
}

function populateProduceTable(data) {
    const tableBody = document.getElementById('produceTable').getElementsByTagName('tbody')[0];
    tableBody.innerHTML = '';

    if (data && data.error) {
        const row = tableBody.insertRow();
        const cell = row.insertCell();
        cell.colSpan = 10;
        cell.textContent = `Error: ${data.error}`;
        return;
    }

    if (!data || data.length === 0) {
        const row = tableBody.insertRow();
        const cell = row.insertCell();
        cell.colSpan = 10;
        cell.textContent = 'No produce listings found.';
        document.getElementById('pagination').style.display = 'none';
        return;
    }

    data.forEach(listing => {
        const row = tableBody.insertRow();
        row.insertCell().textContent = listing.produce || 'N/A'; // Produce
        row.insertCell().textContent = listing.uploaded_quantity || 0; // Farmer Quantity
        let remaining = listing.remaining_quantity === 'Item Sold' ? 0 : parseInt(listing.remaining_quantity || 0, 10);
        let ordered = (listing.uploaded_quantity || 0) - remaining;
        if (listing.remaining_quantity === 'Item Sold') {
            ordered = listing.uploaded_quantity || 0;
        }
        row.insertCell().textContent = listing.quantity_ordered || 0; // Quantity Ordered   
        row.insertCell().textContent = listing.no_orders ? 'No Order Yet' : remaining; // Remaining
        row.insertCell().textContent = listing.price ? `₦${parseFloat(listing.price).toLocaleString('en-NG', { minimumFractionDigits: 2 })}` : 'N/A'; // Price
        row.insertCell().textContent = listing.conditions || 'N/A'; // Condition
        row.insertCell().textContent = listing.available_date || 'N/A'; // Available Date
        row.insertCell().textContent = listing.address || 'N/A'; // Address
        const imageCell = row.insertCell(); // Image
        if (listing.image_path) {
            const viewButton = document.createElement('button');
            viewButton.textContent = 'View';
            viewButton.classList.add('btn', 'btn-sm', 'btn-info', 'view-image-btn');
            viewButton.dataset.imagePath = listing.image_path;
            imageCell.appendChild(viewButton);
        } else {
            imageCell.textContent = 'No Image';
        }
       const actionsCell = row.insertCell(); // Actions
        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Delete';
        deleteButton.classList.add('btn', 'btn-sm', 'btn-danger', 'delete-btn');
        deleteButton.dataset.listingId = listing.prod_id || 0;
        actionsCell.appendChild(deleteButton);
        actionsCell.appendChild(document.createTextNode('\u00A0')); // Non-breaking space
        const badge = document.createElement('span');
        badge.classList.add('badge');
        if (remaining > 0) {
            badge.textContent = 'Available';
            badge.classList.add('bg-success');
            badge.style.color = 'white';
        } else {
            badge.textContent = 'Unavailable';
            badge.classList.add('bg-danger');
            badge.style.color = 'white';
        }
        badge.style.color = 'white'; // Set text color to white
        actionsCell.appendChild(badge);
    });
}

function populatePagination(totalPages, currentPage) {
    const paginationElement = document.getElementById('pagination');
    paginationElement.innerHTML = '';

    if (totalPages <= 1) return;

    const maxPagesToShow = 4;
    let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
    let endPage = Math.min(totalPages, currentPage + Math.floor(maxPagesToShow / 2));

    if (endPage - startPage + 1 < maxPagesToShow) {
        if (startPage > 1) startPage = Math.max(1, endPage - maxPagesToShow + 1);
        if (endPage < totalPages) endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);
    }

    // Previous button
    const prevLi = document.createElement('li');
    prevLi.classList.add('page-item');
    if (currentPage > 1) {
        const prevLink = document.createElement('a');
        prevLink.classList.add('page-link');
        prevLink.href = '#';
        prevLink.textContent = 'Previous';
        prevLink.addEventListener('click', (e) => { e.preventDefault(); loadProduceData(currentPage - 1); });
        prevLi.appendChild(prevLink);
    } else {
        prevLi.classList.add('disabled');
        const span = document.createElement('span');
        span.classList.add('page-link');
        span.textContent = 'Previous';
        prevLi.appendChild(span);
    }
    paginationElement.appendChild(prevLi);

    // Page numbers with ellipsis
    if (startPage > 1) {
        const firstLi = document.createElement('li');
        firstLi.classList.add('page-item');
        const firstLink = document.createElement('a');
        firstLink.classList.add('page-link');
        firstLink.href = '#';
        firstLink.textContent = 1;
        firstLink.addEventListener('click', (e) => { e.preventDefault(); loadProduceData(1); });
        firstLi.appendChild(firstLink);
        paginationElement.appendChild(firstLi);
        if (startPage > 2) {
            const dotsLi = document.createElement('li');
            dotsLi.classList.add('page-item', 'disabled');
            const span = document.createElement('span');
            span.classList.add('page-link');
            span.textContent = '...';
            dotsLi.appendChild(span);
            paginationElement.appendChild(dotsLi);
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        const pageLi = document.createElement('li');
        pageLi.classList.add('page-item');
        if (i === currentPage) pageLi.classList.add('active');
        const pageLink = document.createElement('a');
        pageLink.classList.add('page-link');
        pageLink.href = '#';
        pageLink.textContent = i;
        pageLink.addEventListener('click', (e) => { e.preventDefault(); loadProduceData(i); });
        pageLi.appendChild(pageLink);
        paginationElement.appendChild(pageLi);
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            const dotsLi = document.createElement('li');
            dotsLi.classList.add('page-item', 'disabled');
            const span = document.createElement('span');
            span.classList.add('page-link');
            span.textContent = '...';
            dotsLi.appendChild(span);
            paginationElement.appendChild(dotsLi);
        }
        const lastLi = document.createElement('li');
        lastLi.classList.add('page-item');
        const lastLink = document.createElement('a');
        lastLink.classList.add('page-link');
        lastLink.href = '#';
        lastLink.textContent = totalPages;
        lastLink.addEventListener('click', (e) => { e.preventDefault(); loadProduceData(totalPages); });
        lastLi.appendChild(lastLink);
        paginationElement.appendChild(lastLi);
    }

    // Next button
    const nextLi = document.createElement('li');
    nextLi.classList.add('page-item');
    if (currentPage < totalPages) {
        const nextLink = document.createElement('a');
        nextLink.classList.add('page-link');
        nextLink.href = '#';
        nextLink.textContent = 'Next';
        nextLink.addEventListener('click', (e) => { e.preventDefault(); loadProduceData(currentPage + 1); });
        nextLi.appendChild(nextLink);
    } else {
        nextLi.classList.add('disabled');
        const span = document.createElement('span');
        span.classList.add('page-link');
        span.textContent = 'Next';
        nextLi.appendChild(span);
    }
    paginationElement.appendChild(nextLi);
}

function loadUniqueFilters() {
    fetch('get_produce_info.php?filters=true')
        .then(response => response.json())
        .then(data => {
            populateFilterDropdown('filter_produce', data.unique_produce || []);
            populateFilterDropdown('filter_condition', data.unique_conditions || []);
        })
        .catch(error => console.error('Error fetching filters:', error));
}

function populateFilterDropdown(selectId, options) {
    const selectElement = document.getElementById(selectId);
    selectElement.innerHTML = '<option value="">All</option>';
    (options || []).forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option;
        optionElement.textContent = option;
        selectElement.appendChild(optionElement);
    });
}