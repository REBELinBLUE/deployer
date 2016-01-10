<?php

namespace REBELinBLUE\Deployer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Intervention\Image\Facades\Image;
use PragmaRX\Google2FA\Vendor\Laravel\Facade as Google2FA;
use REBELinBLUE\Deployer\Events\EmailChangeRequested;
use REBELinBLUE\Deployer\Http\Requests\StoreProfileRequest;
use REBELinBLUE\Deployer\Http\Requests\StoreSettingsRequest;
use REBELinBLUE\Deployer\Repositories\Contracts\UserRepositoryInterface;

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
     * View user profile.
     * @return Response
     */
    public function index()
    {
        $user = Auth::user();
        $img  = Google2FA::getQRCodeGoogleUrl('Deployer', $user->name, $user->google2fa_secret);

        return view('user.profile', [
            'google_2fa_url' => $img,
            'user'           => $user,
            'title'          => Lang::get('users.update_profile'),
        ]);
    }

    /**
     * Update user's basic profile.
     *
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
     * Update user's settings.
     *
     * @param  StoreSettingsRequest $request
     * @return Response
     */
    public function settings(StoreSettingsRequest $request)
    {
        $this->repository->updateById($request->only(
            'skin'
        ), Auth::user()->id);

        return redirect()->to('/');
    }

    /**
     * Send email to change a new email.
     * @return Response
     */
    public function requestEmail()
    {
        event(new EmailChangeRequested(Auth::user()));

        return 'success';
    }

    /**
     * Show the page to input the new email.
     */
    public function email($token)
    {
        return view('user.change-email', [
            'token' => $token,
        ]);
    }

    /**
     * Change the user's email.
     * @return Response
     */
    public function changeEmail(Request $request)
    {
        $user = $this->repository->findByEmailToken($request->get('token'));

        if ($request->get('email')) {
            $user->email       = $request->get('email');
            $user->email_token = '';

            $user->save();
        }

        return redirect()->to('/');
    }

    /**
     * Upload file.
     * @return Response
     */
    public function upload(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|image',
        ]);

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file            = $request->file('file');
            $path            = '/upload/' . date('Y-m-d');
            $destinationPath = public_path() . $path;
            $filename        = uniqid() . '.' . $file->getClientOriginalExtension();

            $file->move($destinationPath, $filename);

            return [
                'image'   => url($path . '/' . $filename),
                'path'    => $path . '/' . $filename,
                'message' => 'success',
            ];
        } else {
            return 'failed';
        }
    }

    /**
     * Reset the user's avatar to gravatar.
     * @return Response
     */
    public function gravatar()
    {
        $user         = Auth::user();
        $user->avatar = null;
        $user->save();

        return [
            'image'   => avatar($user),
            'success' => true,
        ];
    }

    /**
     * Set and crop the avatar.
     * @return Response
     */
    public function avatar(Request $request)
    {
        $path   = $request->get('path', '/upload/picture.jpg');
        $image  = Image::make(public_path() . $path);
        $rotate = $request->get('dataRotate');

        if ($rotate) {
            $image->rotate($rotate);
        }

        $width  = $request->get('dataWidth');
        $height = $request->get('dataHeight');
        $left   = $request->get('dataX');
        $top    = $request->get('dataY');

        $image->crop($width, $height, $left, $top);
        $path = '/upload/' . date('Y-m-d') . '/avatar' . uniqid() . '.jpg';

        $image->save(public_path() . $path);

        $user         = Auth::user();
        $user->avatar = $path;
        $user->save();

        return [
            'image'   => url($path),
            'success' => true,
        ];
    }

    public function twoFactor(Request $request)
    {
        $secret = null;
        if ($request->get('two_factor')) {
            $secret = Google2FA::generateSecretKey();
        }

        $user                   = Auth::user();
        $user->google2fa_secret = $secret;
        $user->save();

        return redirect()->route('profile.index');
    }
}
