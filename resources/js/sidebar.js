// Script para manejar el estado de colapso de la barra lateral
document.addEventListener('DOMContentLoaded', function() {
    // Observar cambios en el estado de la barra lateral
    const sidebar = document.querySelector('.fi-sidebar');
    const main = document.querySelector('.fi-main');
    
    if (sidebar && main) {
        // Crear un MutationObserver para detectar cambios en atributos
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && 
                    (mutation.attributeName === 'data-collapsed' || 
                     mutation.attributeName === 'class')) {
                    updateLayout();
                }
            });
        });
        
        // Observar cambios en la barra lateral
        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['data-collapsed', 'class']
        });
        
        // Función para actualizar el layout
        function updateLayout() {
            const isCollapsed = sidebar.hasAttribute('data-collapsed') && 
                               sidebar.getAttribute('data-collapsed') === 'true' ||
                               sidebar.classList.contains('fi-sidebar-collapsed');
            
            if (isCollapsed) {
                document.body.classList.add('fi-sidebar-collapsed');
            } else {
                document.body.classList.remove('fi-sidebar-collapsed');
            }
        }
        
        // Ejecutar una vez al cargar
        updateLayout();
        
        // También escuchar clicks en el botón de colapso
        const collapseButton = document.querySelector('.fi-sidebar-collapse-button, .fi-sidebar-toggle');
        if (collapseButton) {
            collapseButton.addEventListener('click', function() {
                // Esperar un poco para que Filament procese el cambio
                setTimeout(updateLayout, 100);
            });
        }
    }
    
    // Manejar clics fuera de la barra lateral en móviles
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 768) {
            const sidebar = document.querySelector('.fi-sidebar');
            const sidebarButton = document.querySelector('[data-sidebar-toggle]');
            
            if (sidebar && !sidebar.contains(event.target) && 
                (!sidebarButton || !sidebarButton.contains(event.target))) {
                sidebar.classList.remove('show');
                sidebar.setAttribute('data-opened', 'false');
            }
        }
    });
});

// Función para alternar la barra lateral manualmente si es necesario
window.toggleSidebar = function() {
    const sidebar = document.querySelector('.fi-sidebar');
    if (sidebar) {
        const isCollapsed = sidebar.hasAttribute('data-collapsed') && 
                           sidebar.getAttribute('data-collapsed') === 'true';
        sidebar.setAttribute('data-collapsed', !isCollapsed);
        
        // Trigger el observer manualmente
        const event = new Event('sidebarToggle');
        sidebar.dispatchEvent(event);
    }
};
