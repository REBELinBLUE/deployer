<?php namespace App\Http\Controllers;

use Lang;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\TemplateRepositoryInterface;
use App\Http\Requests\StoreTemplateRequest;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
   /**
     * The template repository
     *
     * @var TemplateRepositoryInterface
     */
    private $templateRepository;

    /**
     * Class constructor
     *
     * @param TemplateRepositoryInterface $templateRepository
     * @return void
     */
    public function __construct(TemplateRepositoryInterface $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    /**
     * Display a listing of the templates.
     *
     * @param TemplateRepositoryInterface $templateRepository
     * @return Response
     */
    public function index()
    {
        return view('templates.listing', [
            'title'     => Lang::get('templates.manage'),
            'templates' => $this->templateRepository->getAll()
        ]);
    }

    /**
     * Store a newly created template in storage.
     *
     * @param StoreTemplateRequest $request
     * @return Response
     */
    public function store(StoreTemplateRequest $request)
    {
        return $this->templateRepository->create($request->only('name'));
    }

    /**
     * Update the specified template in storage.
     *
     * @param int $template_id
     * @param StoreTemplateRequest $request
     * @return Response
     */
    public function update($template_id, StoreTemplateRequest $request)
    {
        return $this->templateRepository->updateById($request->only('name'), $template_id);
    }
}
