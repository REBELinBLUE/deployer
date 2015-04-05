<?php namespace App\Http\Composers;

use App\Group;
use Illuminate\Contracts\View\View;

class NavigationComposer
{
    public function compose(View $view)
    {
        $view->with('groups', Group::orderBy('name')->get());
    }
}
