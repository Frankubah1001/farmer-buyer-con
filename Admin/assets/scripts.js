// Submenu toggle functionality
document.addEventListener('DOMContentLoaded', function () {
    const submenuItems = document.querySelectorAll('.has-submenu > a');

    submenuItems.forEach(item => {
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

    // Close submenu when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.has-submenu')) {
            document.querySelectorAll('.has-submenu').forEach(submenu => {
                submenu.classList.remove('open');
            });
        }
    });
});
