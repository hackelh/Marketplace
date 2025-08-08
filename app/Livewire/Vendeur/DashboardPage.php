<?php

namespace App\Livewire\Vendeur;

use Livewire\Component;
use App\Models\Tissu;
use App\Models\Categorie;

class DashboardPage extends Component
{
    public $activeTab = 'gestion';

    public function render()
    {
        $totalTissus = Tissu::count();
        $totalCategories = Categorie::count();
        
        return view('livewire.pages.vendeur.dashboard', [
            'totalTissus' => $totalTissus,
            'totalCategories' => $totalCategories,
        ]);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }
} 