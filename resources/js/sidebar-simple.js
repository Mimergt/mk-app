// Gestión simplificada de la barra lateral
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sidebar script loaded');
    
    function updateLayout() {
        const sidebar = document.querySelector('.fi-sidebar');
        if (!sidebar) return;
        
        const isCollapsed = sidebar.hasAttribute('data-collapsed') && 
                           sidebar.getAttribute('data-collapsed') === 'true';
        
        console.log('Sidebar collapsed:', isCollapsed);
        
        if (isCollapsed) {
            document.body.classList.add('fi-sidebar-collapsed');
        } else {
            document.body.classList.remove('fi-sidebar-collapsed');
        }
    }
    
    // Observar cambios en la barra lateral
    const observer = new MutationObserver(updateLayout);
    
    // Inicializar cuando la barra lateral esté disponible
    function init() {
        const sidebar = document.querySelector('.fi-sidebar');
        if (sidebar) {
            observer.observe(sidebar, {
                attributes: true,
                attributeFilter: ['data-collapsed', 'class']
            });
            updateLayout(); // Estado inicial
            console.log('Sidebar observer initialized');
        } else {
            setTimeout(init, 100);
        }
    }
    
    init();
});
