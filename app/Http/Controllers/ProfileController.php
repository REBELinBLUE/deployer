<?php
namespace App\Http\Controllers;

use App\Events\EmailChangeRequested;
use App\Http\Requests\StoreProfileRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Auth;
use Lang;

/**
 * The use profile controller.
 */
class ProfileController extends Controller
{
    /**
     * Class constructor.
     *
     * @param  UserRepositoryInterface $repository
     * @return void
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return view('user.profile', [
            'user'  => Auth::user(),
            'title' => Lang::get('users.update_profile'),
        ]);
    }

    public function update(StoreProfileRequest $request)
    {
        $this->repository->updateById($request->only(
            'name',
            'password'
        ), Auth::user()->id);
        return redirect()->to('/');
    }

    public function requestEmail()
    {
        event(new EmailChangeRequested(Auth::user()));
        return 'success';
    }

    public function changeAvatar()
    {
        # code...
    }
}
