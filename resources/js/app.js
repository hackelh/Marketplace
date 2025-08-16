import './bootstrap';

// Vérifier si Alpine n'est pas déjà chargé
if (!window.Alpine) {
    import('alpinejs').then(Alpine => {
        window.Alpine = Alpine.default;
        import('@alpinejs/intersect').then(module => {
            window.Alpine.plugin(module.default);
            window.Alpine.start();
        });
    });
}
