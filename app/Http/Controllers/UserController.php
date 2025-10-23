<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    public function searchEmails(Request $request)
    {
        $email = $request->get("query");
        $limit = $request->get("limit") ?? 3;

        $userId = Auth::id();
        $emails = User::select("email")
            ->where("email", "like", "%" . $email . "%")
            ->where("id", "!=", $userId)
            ->limit($limit)
            ->pluck("email");

        return response()->json($emails);
    }

    public function checkEmail(Request $request)
    {
        $email = $request->get("query");

        $user = User::select("email")->where("email", $email)->get();

        if (count($user) == 0)
            return response()->json([
                "error" => "Invalid email."
            ], 422);

        return response()->json([], 200);
    }

    public function checkEmailAvailability(Request $request)
    {
        $validated = $request->validate([
            "email" => "required|email"
        ]);

        $email = $validated["email"];

        $exists = User::where("email", $email)->exists();

        // Return the response
        return response()->json([
            'email' => $email,
            'valid' => true,
            'exists' => $exists
        ]);
    }

    public function getEmails()
    {
        $emails = User::pluck("email");
        return response()->json($emails, 200);
    }

    public function profile()
    {
        $userId = Auth::id();
        $user = User::find($userId);

        return Inertia::render("Profile", [
            "profile" => [
                "firstname" => $user->firstname,
                "lastname" => $user->lastname,
                "email" => $user->email
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $input = json_decode($request->getContent(), true);

        $user = User::find(Auth::id());

        if (isset($input["email"])) {
            $exists = User::where("email", $input["email"])->exists();

            if ($exists)
                return response()->json([
                    "message" => "Email already exists."
                ], 422);

            $user->email = $input["email"];
        }

        $user->firstname = $input["firstname"] ?? $user->firstname;
        $user->lastname = $input["lastname"] ?? $user->lastname;

        $user->save();

        return response()->json([
            "message" => "Updated.",
            "input" => $input
        ]);
    }

    public function updatePassword(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            "current_password" => "required",
            "new_password" => "required|min:8",
        ]);

        $user = Auth::user();

        $currentPasswordIsCorrect = Hash::check($request->current_password, $user->password);
        
        if (!$currentPasswordIsCorrect) {
            return response()->json([
                'errors' => [
                    "current_password" => ["Current password is incorrect."]
                ],
            ], 422);
        }

        if ($currentPasswordIsCorrect && $validated["current_password"] === $validated["new_password"])
            return response()->json([
                "errors" => [
                    "new_password" => ["New password should not be the same as the current one."]
                ]
            ], 422);

        $user->password = Hash::make($request->new_password);
        $user->save();

        // Return a success response
        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }
}
