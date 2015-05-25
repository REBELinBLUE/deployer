<?php namespace App\Http\Controllers\Admin;

use App\Template;
use App\CommandTemplate;
use App\Http\Controllers\Controller;

/**
 * The controller for managing the command associated with templates
 */
class TemplateCommandController extends Controller
{
    /**
     * Shows the commands associated with a template
     * 
     * @param Template $template
     * @return Response
     */
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
