<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Tissu;

class CatalogueShowController extends Controller
{
    public function __invoke($tissu)
    {
        $product = Tissu::with(['categorie', 'images'])
                      ->where('slug', $tissu)
                      ->orWhere('id', $tissu)
                      ->firstOrFail();

        return view('catalogue.show', compact('product'));
    }
}
