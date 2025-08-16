<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
        
        <!-- Gestion des erreurs globales -->
        <script>
        window.addEventListener('error', function(event) {
            // Ignorer les erreurs de type 'removeChild' provenant de scripts tiers
            if (event.message && event.message.includes('removeChild')) {
                console.warn('Erreur DOM ignorée (script tiers) :', event);
                event.preventDefault();
                return false;
            }
        }, true);
        </script>
        
        <!-- Notification Toast -->
        <div x-data="notification" 
             x-show="show"
             x-transition.opacity.duration.300
             class="fixed bottom-4 right-4 w-full max-w-md rounded-lg shadow-lg overflow-hidden z-50"
             style="display: none;"
             @click.away="show = false">
            <div class="p-4" :class="bgClass + ' ' + borderClass + ' border-l-4'">
                <div class="flex items-start">
                    <!-- Icône -->
                    <div class="flex-shrink-0 pt-1">
                        <svg class="h-6 w-6" :class="iconClass" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPath" />
                        </svg>
                    </div>
                    
                    <!-- Contenu -->
                    <div class="ml-3 flex-1">
                        <!-- Titre -->
                        <h3 x-show="title" class="text-sm font-medium" :class="textClass" x-text="title"></h3>
                        
                        <!-- Message -->
                        <div class="mt-1 text-sm text-gray-700">
                            <p x-text="message"></p>
                        </div>
                    </div>
                    
                    <!-- Bouton de fermeture -->
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="show = false" 
                                class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2"
                                :class="buttonClass">
                            <span class="sr-only">Fermer</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('notification', () => ({
                show: false,
                title: '',
                message: '',
                type: 'success',
                timeout: null,
                
                init() {
                    // Gestion des notifications Livewire
                    Livewire.on('notify', data => {
                        if (this.timeout) clearTimeout(this.timeout);
                        
                        this.title = data.title || '';
                        this.message = data.message || '';
                        this.type = data.type || 'success';
                        
                        this.show = true;
                        
                        this.timeout = setTimeout(() => {
                            this.show = false;
                        }, 4000);
                    });
                    
                    Livewire.on('panier-mis-a-jour', () => {
                        this.updatePanierCount();
                    });
                    
                    this.updatePanierCount();
                },
                
                get iconPath() {
                    const paths = {
                        'success': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        'error': 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                        'warning': 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        'info': 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                    };
                    return paths[this.type] || paths['success'];
                },
                
                get bgClass() {
                    const classes = {
                        'success': 'bg-green-50',
                        'error': 'bg-red-50',
                        'warning': 'bg-yellow-50',
                        'info': 'bg-blue-50'
                    };
                    return classes[this.type] || 'bg-green-50';
                },
                
                get textClass() {
                    const classes = {
                        'success': 'text-green-800',
                        'error': 'text-red-800',
                        'warning': 'text-yellow-800',
                        'info': 'text-blue-800'
                    };
                    return classes[this.type] || 'text-green-800';
                },
                
                get iconClass() {
                    const classes = {
                        'success': 'text-green-400',
                        'error': 'text-red-400',
                        'warning': 'text-yellow-400',
                        'info': 'text-blue-400'
                    };
                    return classes[this.type] || 'text-green-400';
                },
                
                get buttonClass() {
                    const classes = {
                        'success': 'text-green-500 hover:bg-green-100 focus:ring-green-500',
                        'error': 'text-red-500 hover:bg-red-100 focus:ring-red-500',
                        'warning': 'text-yellow-500 hover:bg-yellow-100 focus:ring-yellow-500',
                        'info': 'text-blue-500 hover:bg-blue-100 focus:ring-blue-500'
                    };
                    return classes[this.type] || 'text-green-500 hover:bg-green-100 focus:ring-green-500';
                },
                
                get borderClass() {
                    const classes = {
                        'success': 'border-green-400',
                        'error': 'border-red-400',
                        'warning': 'border-yellow-400',
                        'info': 'border-blue-400'
                    };
                    return classes[this.type] || 'border-green-400';
                },
                
                updatePanierCount() {
                    const panier = {!! json_encode(session('panier', [])) !!};
                    const panierCount = Object.keys(panier).length;
                    const countElement = document.getElementById('panier-count');
                    if (countElement) {
                        countElement.textContent = panierCount;
                        countElement.classList.add('animate-bounce');
                        setTimeout(() => {
                            countElement.classList.remove('animate-bounce');
                        }, 1000);
                    }
                }
            }));
        });
        </script>
        @stack('scripts')
        
        <script>
            // Détection des erreurs de lecture audio/vidéo
            window.addEventListener('error', function(event) {
                if (event.target.tagName === 'AUDIO' || event.target.tagName === 'VIDEO') {
                    console.log('Erreur sur un élément média:', event);
                    event.preventDefault();
                }
            }, true);

            // Détection des appels à play() échoués
            if (typeof HTMLMediaElement !== 'undefined') {
                const originalPlay = HTMLMediaElement.prototype.play;
                HTMLMediaElement.prototype.play = function() {
                    return originalPlay.call(this).catch(error => {
                        console.warn('Erreur de lecture média:', error, this);
                        return Promise.reject(error);
                    });
                };
            }
        </script>
    </body>
</html>
