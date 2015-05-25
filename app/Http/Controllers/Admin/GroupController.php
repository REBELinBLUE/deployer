<?php namespace App\Http\Controllers\Admin;

use Lang;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\GroupRepositoryInterface;
use App\Http\Requests\StoreGroupRequest;
use Illuminate\Http\Request;

/**
 * Group management controller
 */
class GroupController extends Controller
{
    /**
     * The group repository
     *
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * Class constructor
     *
     * @param GroupRepositoryInterface $groupRepository
     * @return void
     */
    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * Display a listing of the groups.
     *
     * @param GroupRepositoryInterface $groupRepository
     * @return Response
     */
    public function index()
    {
        return view('groups.listing', [
            'title'  => Lang::get('groups.manage'),
            'groups' => $this->groupRepository->getAll()
        ]);
    }

    /**
     * Store a newly created group in storage.
     *
     * @param StoreGroupRequest $request
     * @return Response
     */
    public function store(StoreGroupRequest $request)
    {
        return $this->groupRepository->create($request->only('name'));
    }

    /**
     * Update the specified group in storage.
     *
     * @param int $group_id
     * @param StoreGroupRequest $request
     * @return Response
     */
    public function update($group_id, StoreGroupRequest $request)
    {
        return $this->groupRepository->updateById($request->only('name'), $group_id);
    }
}
