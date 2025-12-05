<script>
// Submenu toggle functionality - Immediate execution to avoid conflicts
(function() {
    'use strict';
    
    function initSubmenu() {
        const submenuItems = document.querySelectorAll('.has-submenu > a');
        
        // Remove any existing listeners to prevent duplicates
        submenuItems.forEach(item => {
            const newItem = item.cloneNode(true);
            item.parentNode.replaceChild(newItem, item);
        });
        
        // Re-select after cloning
        const freshSubmenuItems = document.querySelectorAll('.has-submenu > a');
        
        freshSubmenuItems.forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                
                const parent = this.parentElement;
                const isCurrentlyOpen = parent.classList.contains('open');

                // Close all other submenus
                document.querySelectorAll('.has-submenu').forEach(submenu => {
                    if (submenu !== parent) {
                        submenu.classList.remove('open');
                    }
                });

                // Toggle current submenu
                if (isCurrentlyOpen) {
                    parent.classList.remove('open');
                } else {
                    parent.classList.add('open');
                }
            });
        });

        // Auto-open submenu if a child is active
        const activeSubmenuItem = document.querySelector('.submenu a.active');
        if (activeSubmenuItem) {
            const parentSubmenu = activeSubmenuItem.closest('.has-submenu');
            if (parentSubmenu) {
                parentSubmenu.classList.add('open');
            }
        }

        // Close submenu when clicking outside (only once)
        document.removeEventListener('click', closeSubmenusOnOutsideClick);
        document.addEventListener('click', closeSubmenusOnOutsideClick);
    }
    
    function closeSubmenusOnOutsideClick(e) {
        if (!e.target.closest('.has-submenu')) {
            document.querySelectorAll('.has-submenu').forEach(submenu => {
                submenu.classList.remove('open');
            });
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSubmenu);
    } else {
        // DOM already loaded
        initSubmenu();
    }
})();

// Sidebar toggle for mobile
(function() {
    'use strict';
    
    function initSidebarToggle() {
        const toggleBtn = document.getElementById('headerToggle');
        const sidebar = document.querySelector('.sidebar');
        
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebarToggle);
    } else {
        initSidebarToggle();
    }
})();
</script>

<script>
/**
 * Table Row Numbering Helper
 * Calculates correct row numbers for tables with DESC ordering
 */

/**
 * Calculate row number for an item in a paginated, descending-ordered list
 * @param {number} currentPage - Current page number (1-indexed)
 * @param {number} itemsPerPage - Number of items per page
 * @param {number} indexInPage - Index of item in current page (0-indexed)
 * @returns {number} Sequential row number (1, 2, 3...)
 */
function getRowNumber(currentPage, itemsPerPage, indexInPage) {
    return ((currentPage - 1) * itemsPerPage) + indexInPage + 1;
}

/**
 * Add row numbers to array of items
 * @param {Array} items - Array of items
 * @param {number} currentPage - Current page
 * @param {number} itemsPerPage - Items per page
 * @returns {Array} Items with row_number property
 */
function addRowNumbers(items, currentPage, itemsPerPage) {
    return items.map((item, index) => ({
        ...item,
        row_number: getRowNumber(currentPage, itemsPerPage, index)
    }));
}
</script>


