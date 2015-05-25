<?php namespace App\Http\Controllers;

use App\Template;
use App\CommandTemplate;
use App\Http\Controllers\Controller;

class TemplateCommandController extends Controller
{
    public function index(Template $template)
    {
        return view('templates.commands', [
            'title'    => 'Template Commands', // FIXME: Translate, add nav
            'breadcrumb' => [
                ['url' => url('admin/templates'), 'label' => $template->name]
            ],
            'template' => $template,
            'commands' => $template->commands
        ]);
    }
}