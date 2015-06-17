<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Repositories\Contracts\TemplateRepositoryInterface;
use Lang;

/**
 * Controller for managing deployment template
 */
class TemplateController extends Controller
{
    /**
     * Shows all templates.
     *
     * @param TemplateRepositoryInterface $projectRepository
     * @return Response
     */
    public function index(TemplateRepositoryInterface $templateRepository)
    {
        $templates = $templateRepository->getAll();

        return view('templates.listing', [
            'title'     => Lang::get('templates.manage'),
            'templates' => $templates->toJson() // Because PresentableInterface toJson() is not working in the view
        ]);
    }
}
