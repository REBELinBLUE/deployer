<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;
use Input;
use Response;

use App\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->created = $user->created_at->format('jS F Y g:i:s A');
        }

        return view('users.listing', [
            'title' => 'Manage users',
            'users' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $rules = array(
            'name'     => 'required',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        } else {
            $user = new User;
            $user->name     = Input::get('name');
            $user->email    = Input::get('email');
            $user->password = bcrypt(Input::get('password'));
            $user->save();

            $user->created = $user->created_at->format('jS F Y g:i:s A');

            return $user;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $user_id
     * @return Response
     */
    public function update($user_id)
    {
        $rules = array(
            'name'     => 'required',
            'email'    => 'required|email|max:255|unique:users,' . $user_id
        );

        if (Input::get('password') !== '') {
            $rules['password'] = 'min:6';
        }

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        } else {
            $user = User::findOrFail($user_id);

            $user->name  = Input::get('name');
            $user->email = Input::get('email');

            if (Input::get('password') !== '') {
                $user->password = bcrypt(Input::get('password'));
            }

            $user->save();

            $user->created = $user->created_at->format('jS F Y g:i:s A');

            return $user;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $user_id
     * @return Response
     */
    public function destroy($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->delete();

        return Response::json([
            'success'  => true,
            'redirect' => '/'
        ], 200);
    }
}
