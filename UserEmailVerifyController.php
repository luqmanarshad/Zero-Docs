<?php

namespace App\Http\Controllers;

use App\Services\SmsMail\MailService;
use App\Services\UserEmailVerifyService;
use Illuminate\Http\Request;

class UserEmailVerifyController extends Controller
{
    public $userEmailVerifyService;

    public function __construct()
    {
        $this->userEmailVerifyService = new UserEmailVerifyService;
    }

    public function emailVerified($token)
    {
        $verified =  $this->userEmailVerifyService->emailVerified($token);
        if ($verified == true) {
            return redirect()->route('login')->with('success', __('Congratulations! Successfully verified your email.'));
        } else {
            return redirect(route('login'));
        }
    }

    public function emailVerify($token)
    {
        $user = $this->userEmailVerifyService->getUserByToken($token);

        if (!is_null($user)) {
            if ($user->status == USER_STATUS_ACTIVE) {
                return redirect()->route('login');
            }
        } else {
            return redirect()->route('login')->with('error', __(SOMETHING_WENT_WRONG));
        }
        return view('auth.verify', compact('token'));
    }

    public function emailVerifyResend($token)
    {
        $user = $this->userEmailVerifyService->getUserByToken($token);

        if (getOption('email_verification_status', 0) == 1) {
            if ($user) {
                $subject = __('Resent Account Verification') . ' ' . getOption('app_name');
                $message = __('Please verify your account');
                $emails = [$user->email];
                $ownerUserId = $user->role == USER_ROLE_OWNER ? $user->id : $user->owner_user_id;

                $mailService = new MailService;
                $mailService->sendUserEmailVerificationMail($emails, $subject, $message, $user, $ownerUserId);

                return redirect()->route('login')->with('success', __(SENT_SUCCESSFULLY));
            }
        } else {
            return redirect()->route('login')->with('error', __(SOMETHING_WENT_WRONG));
        }
    }
}
