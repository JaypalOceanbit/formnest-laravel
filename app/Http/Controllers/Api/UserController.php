<?php

namespace App\Http\Controllers\Api;

use App\Constants\Keys;
use App\Constants\ResponseCodes;
use App\Http\Controllers\BaseController;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    /**
     * Register a new user.
     *
     * Expected fields:
     * - first_name (or firstName)
     * - last_name (or lastName)
     * - mobile number (phone, mobile_number, mobile_no)
     * - email id (email, email_id)
     * - password
     * - confirm password (confirm_password, password_confirmation)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $input = $request->all();

        // Support aliases for field names
        if (!isset($input['first_name']) && isset($input['firstName'])) {
            $input['first_name'] = $input['firstName'];
        }
        if (!isset($input['last_name']) && isset($input['lastName'])) {
            $input['last_name'] = $input['lastName'];
        }
        if (!isset($input['phone'])) {
            $input['phone'] = $input['mobile_number'] ?? $input['mobile_no'] ?? $input['mobile'] ?? null;
        }
        if (!isset($input['email']) && isset($input['email_id'])) {
            $input['email'] = $input['email_id'];
        }
        if (!isset($input['confirm_password']) && isset($input['password_confirmation'])) {
            $input['confirm_password'] = $input['password_confirmation'];
        }

        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|same:password',
        ];

        $messages = [
            'first_name.required' => 'The first name field is required.',
            'last_name.required' => 'The last name field is required.',
            'phone.required' => 'The mobile number field is required.',
            'phone.unique' => 'The mobile number has already been taken.',
            'email.required' => 'The email id field is required.',
            'email.email' => 'The email id must be a valid email address.',
            'email.unique' => 'The email id has already been taken.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 6 characters.',
            'confirm_password.required' => 'The confirm password field is required.',
            'confirm_password.same' => 'The confirm password and password must match.',
        ];

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            $this->setFailMessage('Validation error.');
            return $this->sendValidationError($validator->errors());
        }

        $firstName = trim($input['first_name']);
        $lastName = trim($input['last_name']);
        $email = strtolower(trim($input['email']));
        $phone = trim($input['phone']);
        $fullName = trim($firstName . ' ' . $lastName);

        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($input['password']),
        ]);

        $token = $user->createToken('AuthToken')->accessToken;

        $this->setSuccessMessage('User registered successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, [
            Keys::USER => $user,
            Keys::TOKEN => $token,
        ]);

        return $this->sendSuccessResult();
    }

    /**
     * Login user with username (email or phone) and password.
     *
     * Expected fields:
     * - username (or email, phone, mobile_number, email_id)
     * - password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $input = $request->all();

        $username = $input['username'] ?? $input['email'] ?? $input['phone'] ?? $input['mobile_number'] ?? $input['email_id'] ?? null;
        $password = $input['password'] ?? null;

        $validator = Validator::make([
            'username' => $username,
            'password' => $password,
        ], [
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'The username (email or phone) is required.',
            'password.required' => 'The password field is required.',
        ]);

        if ($validator->fails()) {
            $this->setFailMessage('Validation error.');
            return $this->sendValidationError($validator->errors());
        }

        $username = trim($username);

        // Authenticate with email or phone
        $user = User::where(function ($query) use ($username) {
            $query->where('email', strtolower($username))
                  ->orWhere('phone', $username);
        })->first();

        if (!$user || !Hash::check($password, $user->password)) {
            $this->setFailMessage('Invalid credentials. Please check your username and password.');
            $this->addFailResultKeyValue(Keys::ERROR, 'These credentials do not match our records.');
            return $this->sendFailResultWithCode(ResponseCodes::UNAUTHORIZED_USER);
        }

        $token = $user->createToken('AuthToken')->accessToken;

        $this->setSuccessMessage('User logged in successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, [
            Keys::USER => $user,
            Keys::TOKEN => $token,
        ]);

        return $this->sendSuccessResult();
    }

    /**
     * Get authenticated user profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        $user = Auth::guard('api')->user() ?? $request->user();

        if (!$user) {
            return $this->unauthorised();
        }

        $this->setSuccessMessage('User profile retrieved successfully.');
        $this->addSuccessResultKeyValue(Keys::DATA, $user);

        return $this->sendSuccessResult();
    }

    /**
     * Logout authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user() ?? $request->user();

        if ($user && $user->token()) {
            $user->token()->revoke();
        }

        $this->setSuccessMessage('User logged out successfully.');
        return $this->sendSuccessResult();
    }

    /**
     * Password reset placeholder.
     */
    public function resetPassword(Request $request, $token = null)
    {
        return response()->json([
            Keys::STATUS => true,
            Keys::MESSAGE => 'Password reset endpoint.',
            'token' => $token,
        ]);
    }

    /**
     * Password update placeholder.
     */
    public function updatePassword(Request $request)
    {
        return response()->json([
            Keys::STATUS => true,
            Keys::MESSAGE => 'Password update endpoint.',
        ]);
    }

    /**
     * Called When Token Is not Pass in Header Or Token Expire.
     */
    public function unauthorised()
    {
        $this->addFailResultKeyValue(Keys::ERROR, 'Unauthorised User');
        return $this->sendFailResultWithCode(ResponseCodes::UNAUTHORIZED_USER);
    }

    /**
     * Called When admin Services Access by non-Admin User.
     */
    public function adminaccess()
    {
        $this->addFailResultKeyValue(Keys::ERROR, 'Service Allow only for Admin . ');
        return $this->sendFailResultWithCode(ResponseCodes::UNAUTHORIZED_USER);
    }

    /**
     * Called When Active User's Services Access by non-Active User.
     */
    public function activeaccess()
    {
        $this->addFailResultKeyValue(Keys::ERROR, 'You don\'t have access to use this service.');
        $this->addFailResultKeyValue(Keys::DATA, Auth::user());
        return $this->sendFailResultWithCode(ResponseCodes::INACTIVE_USER);
    }
}
