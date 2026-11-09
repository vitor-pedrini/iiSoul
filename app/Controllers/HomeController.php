<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * HomeController
 * Exibe a pagina publica inicial (Landing Page).
 */
class HomeController extends Controller
{
    // renderizar a pagina inicial (index)
    public function index(): void
    {
        $this->renderizar('publico/index');
    }
}
