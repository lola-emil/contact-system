<?php

namespace App\Http\Controllers;

use App\Events\ContactShared;
use App\Events\Kuan;
use App\Events\ShareContactEvent;
use App\Http\Services\ContactService;
use App\Models\Contact;
use App\Models\SharedContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Events\PrivateMessageSent;


class ContactController extends Controller
{

    public function __construct(
        protected ContactService $contactService
    ) {}

    private $contactValidateRules = [
        "firstname"    => "required|string|max:255",
        "lastname"     => "required|string|max:255",
        "email"        => "required|email",
        "company"      => "required|string|max:255",
        "phone_number" => "required|regex:/^(\+?\d{10,15})$/",
    ];

    private $_pageLimit = 5;


    public function contactsPage(Request $request)
    {
        $search = $request->input("query") ?? "";

        $unconfirmedContacts = SharedContact::getUnconfirmedSharedContacts();

        $contacts = $this->contactService->getPaginatedContacts($this->_pageLimit, $search);

        return Inertia::render("Contacts", [
            ...$contacts,
            "unconfirmedContacts" => $unconfirmedContacts,
        ]);
    }

    public function getContacts(Request $request)
    {
        $search = $request->input("query") ?? "";

        $result = $this->contactService->getPaginatedContacts($this->_pageLimit, $search);

        return response()->json($result);
    }

    public function createContact(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate($this->contactValidateRules);

        $result = $this->contactService->createContact($validated, $userId);

        if ($result["errors"])
            return response()->json([
                "errors" => $result["errors"]
            ], 422);


        return response()->json([
            "message" => "Added successfully.",
            "result" => $result["result"]
        ]);
    }



    public function getContactById($id)
    {
        $matchedContact = Contact::find($id);

        if (!$matchedContact)
            return response()->json([
                "error" => "Invalid contact."
            ]);

        return response()->json($matchedContact);
    }

    // Wala pa route
    public function updateContact(Request $request, $id)
    {

        $validated = $request->validate($this->contactValidateRules);

        $result = $this->contactService->updateContact($validated, $id);


        if (isset($result["not-found"]))
            return response()->json([
                "error" => $result["not-found"]
            ], 404);

        if ($result["errors"])
            return response()->json([
                "errors" => $result["errors"]
            ]);


        return response()->json([
            "message" => "Update successful."
        ]);
    }

    public function deleteContact($id)
    {
        $userId = Auth::id();


        $result = $this->contactService->deleteContact($userId, $id);


        if (isset($result["not-found"]))
            return response()->json([
                "error" => $result["not-found"]
            ], 404);

        return response()->json([
            "message" => "Deleted successfully"
        ]);
    }

    public function shareContact(Request $request)
    {
        $validated = $request->validate([
            "contact_id" => ["required", "integer", "exists:contacts,id"],
            "email"      => ["required", "email"],
            "permission" => ["required"]
        ]);

        $result = $this->contactService->shareContact($validated);

        if ($result["error"])
            return response()->json([
                "error" => $result["error"]
            ], 422);

        return response()->json([
            "message" => "Shared na."
        ]);
    }

    public function acceptSharedContact(Request $request)
    {

        $userId = Auth::id();

        $validated = $request->validate([
            "contact_id" => "required|numeric",
            "confirmed"  => "required|in:0,1"
        ]);

        SharedContact::where("user_id", $userId)
            ->where("contact_id", $validated["contact_id"])
            ->update(['confirmed' => $validated['confirmed']]);

        return response()->json([
            "message" => "Updated successfully"
        ]);
    }

    public function ingoreSharedContact(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            "contact_id" => "required|numeric",
        ]);

        SharedContact::where("user_id", $userId)
            ->where("contact_id", $validated["contact_id"])
            ->delete();


        return response()->json([
            "message" => "Ignored"
        ]);
    }

    public function getUnconfirmedShares()
    {
        $unconfirmedContacts = SharedContact::getUnconfirmedSharedContacts();
        return response()->json($unconfirmedContacts);
    }

    public function shareMultiple(Request $request)
    {
        $user = Auth::user();
        $input = json_decode($request->getContent(), true);

        $result = $this->contactService->shareMultiple($input);
        
        foreach($result["userIds"] as $receiverIds) 
            event(new ContactShared($user, $receiverIds));

        return response()->json([
            "message" => "Success",
            ...$result
        ]);
    }

    public function deleteMultiple(Request $request)
    {
        $input = json_decode($request->getContent(), true);

        $userId = Auth::id();

        $result = $this->contactService->deleteMultiple($input, $userId);

        return response()->json($result);
    }

}
