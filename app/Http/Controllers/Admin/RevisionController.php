<?php

namespace REBELinBLUE\Deployer\Http\Controllers\Admin;

use Illuminate\Http\Request;
use REBELinBLUE\Deployer\Http\Controllers\Controller;
use REBELinBLUE\Deployer\Repositories\Contracts\RevisionRepositoryInterface;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\View\Factory as ViewFactory;

/**
 * Revision log controller.
 */
class RevisionController extends Controller
{
    const LOG_ENTRIES_PER_PAGE = 35;

    /**
     * Revision listing.
     *
     * @param Request $request
     * @param RevisionRepositoryInterface $repository
     * @param ViewFactory $view
     * @param Translator $translator
     *
     * @return \Illuminate\View\View
     */
    public function index(
        Request $request,
        RevisionRepositoryInterface $repository,
        ViewFactory $view,
        Translator $translator
    ) {
        $filterByType = $request->get('filter_type', '');
        $filterByInstance = $request->get('filter_id', '');

        return $view->make('admin.revisions.index', [
            'title'     => $translator->trans('revisions.manage'),
            'revisions' => $repository->getLogEntries(self::LOG_ENTRIES_PER_PAGE, $filterByType, $filterByInstance),
            'classes'   => $repository->getTypes(),
            'instances' => $repository->getInstances($filterByType),
            'filter'    => [
                'type'     => $filterByType,
                'instance' => (int) $filterByInstance,
            ],
        ]);
    }
}
