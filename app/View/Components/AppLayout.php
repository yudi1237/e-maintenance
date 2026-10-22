<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public function __construct(
        public string $bodyClass = '',
        public bool $dark = false,
    ) {}

    public function render(): View
    {
        return view('layouts.app');
    }
}
