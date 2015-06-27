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

    /**
     * View user profile
     * @return Response
     */
    public function index()
    {
        return view('user.profile', [
            'user'  => Auth::user(),
            'title' => Lang::get('users.update_profile'),
        ]);
    }

    /**
     * Update user's basic message
     * @param  StoreProfileRequest $request
     * @return Response
     */
    public function update(StoreProfileRequest $request)
    {
        $this->repository->updateById($request->only(
            'name',
            'password'
        ), Auth::user()->id);
        return redirect()->to('/');
    }

    /**
     * Send email to change a new email
     * @return Response
     */
    public function requestEmail()
    {
        event(new EmailChangeRequested(Auth::user()));
        return 'success';
    }

    /**
     * Show the page to input the new email
     */
    public function email($token)
    {
        return view('user.change-email', compact('token'));
    }

    public function changeAvatar()
    {
        # code...
    }
}
