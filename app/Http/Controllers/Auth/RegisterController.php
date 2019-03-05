<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendVerificationEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    private $user;


    /**
     * RegisterController constructor.
     * @param UserRepository $user
     */
    public function __construct(User $user)
    {
        $this->middleware('guest');
        $this->user = $user;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile' => 'required|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }


    /**
     * Create User
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function create(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        try {
            \DB::beginTransaction();
            $userData = $this->createUserDetails($request->all());
            //$verifyToken = $this->getVerifyToken();
            //\Log::info(" ============ REGISTRATION ================ ".$verifyToken);
            $user = $this->user->create($userData);
            if ($user) {
                //$verifyToken = $this->getVerifyToken();
                //\Log::info(" ============ REGISTRATION ================ ".$verifyToken);
                //$user->verify_token = $verifyToken;
                $profile_name = $user->first_name . ' ' . $user->last_name;
                $user->save();
                dispatch(new SendVerificationEmail($user));
                //$this->sendVerificationEmail($user->email, $user->verify_token, $profile_name);
                \DB::commit();
                return response()->json(['success' => __('messages.REGISTRATION_SUCCESS')], 200);
            }
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json(['error' => __('messages.UNPROCESSED_REQUEST')], 400);
        }
    }

    private function createUserDetails($request)
    {
        return $userData = [
            'first_name' => $request['firstName'],
            'last_name' => $request['lastName'],
            'mobile' => $request['mobile'],
            'address' => $request['address'],
            'city' => $request['city'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
            'verified' => 0,
            'verify_token' => $this->getVerifyToken()
        ];
    }

    private function getVerifyToken()
    {
        $id = str_random(30);

        return $id;
    }

    private function verificationLink($code)
    {
        $id = str_random(30);
        $validator = \Validator::make(['id' => $id], ['id' => 'unique:accounts,vcode']);

        if ($validator->fails()) {
            $this->verificationCode();
        }

        return $id;
    }

    private function sendVerificationEmail($userEmail, $verificationCode, $name)
    {
        $data = ['from' => '', 'system' => ''];
        $subject = 'Verify your account';
        Mail::send('emails.verificationCode', array('name' => $name, 'verificationCode' => $verificationCode),
            function ($message) use ($userEmail, $name, $subject, $data) {
                $message->from($data['from'], $data['system']);
                $message->to($userEmail, $name)->subject($subject);

            });
    }
}
