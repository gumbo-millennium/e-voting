<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;

class Notice extends Component
{
    public string $color;

    /**
     * Require a color
     *
     * @param string|null $color
     * @return void
     */
    public function __construct($color = 'blue')
    {
        $this->color = $color;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.notice');
    }
}
