<?php

namespace REBELinBLUE\Deployer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Intervention\Image\Facades\Image;
use PragmaRX\Google2FA\Contracts\Google2FA as Google2FA;
use REBELinBLUE\Deployer\Contracts\Repositories\UserRepositoryInterface;
use REBELinBLUE\Deployer\Events\EmailChangeRequested;
use REBELinBLUE\Deployer\Http\Requests\StoreProfileRequest;
use REBELinBLUE\Deployer\Http\Requests\StoreSettingsRequest;

/**
 * The use profile controller.
 */
class ProfileController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var Google2FA
     */
    private $google2fa;

    /**
     * ProfileController constructor.
     *
     * @param UserRepositoryInterface $repository
     * @param Google2FA               $google2fa
     */
    public function __construct(UserRepositoryInterface $repository, Google2FA $google2fa)
    {
        $this->repository = $repository;
        $this->google2fa  = $google2fa;
    }

    /**
     * View user profile.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        $code = $this->google2fa->generateSecretKey();
        if ($user->has_two_factor_authentication || old('google_code')) {
            $code = old('google_code', $user->google2fa_secret);
        }

        $img = $this->google2fa->getQRCodeGoogleUrl('Deployer', $user->email, $code);

        return view('user.profile', [
            'google_2fa_url'  => $img,
            'google_2fa_code' => $code,
            'title'           => Lang::get('users.update_profile'),
        ]);
    }

    /**
     * Update user's basic profile.
     *
     * @param StoreProfileRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
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
     * @param StoreSettingsRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function settings(StoreSettingsRequest $request)
    {
        $this->repository->updateById($request->only(
            'skin',
            'scheme'
        ), Auth::user()->id);

        return redirect()->to('/');
    }

    /**
     * Send email to change a new email.
     *
     * @return string
     *
     * @fires EmailChangeRequested
     */
    public function requestEmail()
    {
        event(new EmailChangeRequested(Auth::user()));

        return 'success';
    }

    /**
     * Show the page to input the new email.
     *
     * @param string $token
     *
     * @return \Illuminate\View\View
     */
    public function email($token)
    {
        return view('user.change-email', [
            'token' => $token,
        ]);
    }

    /**
     * Change the user's email.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
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
     *
     * @param Request $request
     *
     * @return array|string
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
     *
     * @return array
     */
    public function gravatar()
    {
        $user         = Auth::user();
        $user->avatar = null;
        $user->save();

        return [
            'image'   => $user->avatar_url,
            'success' => true,
        ];
    }

    /**
     * Set and crop the avatar.
     *
     * @param Request $request
     *
     * @return array
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

    /**
     * Activates two factor authentication.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function twoFactor(Request $request)
    {
        $secret = null;
        if ($request->has('two_factor')) {
            $secret = $request->get('google_code');

            if (!$this->google2fa->verifyKey($secret, $request->get('2fa_code'))) {
                $secret = null;

                return redirect()->back()
                                 ->withInput($request->only('google_code', 'two_factor'))
                                 ->withError(Lang::get('auth.invalid_code'));
            }
        }

        $user                   = Auth::user();
        $user->google2fa_secret = $secret;
        $user->save();

        return redirect()->to('/');
    }
}
