<?php

namespace App\Http\Services;

use App\Models\Contact;
use App\Models\User;
use App\Models\SharedContact;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;


class ContactService
{

    public function createContact($validated, $userId)
    {
        $errors = [];

        // Check if email exists in the result
        if (Contact::where("email", $validated["email"])->exists())
            $errors["email"] = ["Email already in the contacts."];

        // Check if phone number exists in the result
        if (Contact::where("phone_number", $validated["phone_number"])->exists())
            $errors["phone_number"] = ["Phone number already in the contacts."];

        if (!empty($errors)) {
            return [
                "errors" => $errors,
                "result" => null
            ];
        }

        $result = Contact::create([
            "firstname"    => $validated["firstname"],
            "lastname"     => $validated["lastname"],
            "email"        => $validated["email"],
            "company"      => $validated["company"],
            "phone_number" => $validated["phone_number"],
            "user_id"      => $userId
        ]);

        return [
            "errors" => null,
            "result" => $result
        ];
    }

    public function getPaginatedContacts(int $pageLimit, $search)
    {
        $contacts = Contact::getContacts($pageLimit, $search);

        return [
            "contacts"            => $contacts->items(),
            "pageNumber"          => $contacts->currentPage(),
            "limit"               => $pageLimit,
            "pageCount"           => ceil($contacts->total() / $pageLimit),
            "contactCount"        => $contacts->total(),
            "searchTerm" => $search,
            "offset" => ($contacts->currentPage() - 1) * $pageLimit
        ];
    }

    public function updateContact($validated, $contactId)
    {
        $errors = [];
        $contact = Contact::find($contactId);

        if (!$contact) {
            return [
                "not-found" => "Contact not found."
            ];
        }

        if ($contact["email"] != $validated["email"] || $contact["phone_number"] != $validated["phone_number"]) {
            // Check if email exists in the result
            if (Contact::where("email", $validated["email"])->exists())
                $errors["email"] = ["Email already in the contacts."];

            // Check if phone number exists in the result
            if (Contact::where("phone_number", $validated["phone_number"])->exists())
                $errors["phone_number"] = ["Phone number already in the contacts."];
        }

        $contact->firstname    = $validated["firstname"];
        $contact->lastname     = $validated["lastname"];
        $contact->email        = $validated["email"];
        $contact->company      = $validated["company"];
        $contact->phone_number = $validated["phone_number"];
        $contact->save();

        return [
            "errors" => $errors,
            "result" => $contact
        ];
    }


    public function deleteContact($userId, $contactId)
    {
        $contact = Contact::find($contactId);

        if (!$contact) return [
            "not-found" => "Contact not found."
        ];

        if (Gate::denies("owned-contact", $contact))
            SharedContact::where("user_id", $userId)->where("contact_id", $contact->id)->delete();
        else
            $contact->delete();

        return [
            "errors" => null,
            "result" => $contact
        ];
    }

    public function shareContact($validated)
    {

        $contact = Contact::find($validated["contact_id"]);

        if (Gate::denies("owned-contact", $contact))
            return [
                "error" => "You're not allowed to share this contact"
            ];

        $recepient = User::where("email", $validated["email"])->first();

        if (!$recepient)
            return [
                "error" => "Invalid email."
            ];

        $sharedContactExists = SharedContact::where("contact_id", $validated["contact_id"])
            ->where("user_id", $recepient->id)
            ->exists();

        if ($sharedContactExists)
            return [
                "error" => "Already shared to this email."
            ];


        $sharedContact = SharedContact::create([
            "contact_id" => $validated["contact_id"],
            "user_id"    => $recepient->id,
            "permission" => $validated["permission"]
        ]);

        return [
            "error" => null,
            "data" => $sharedContact
        ];
    }

    public function shareMultiple($input)
    {
        $contactIds = $input["contacts"];
        $emails = $input["emails"];

        $permission = $input["permission"] ?? "viewer";
        $skipped = [];

        $userIds = User::whereIn("email", $emails)->pluck("id");
        $contacts = Contact::getSelectedContacts($contactIds);

        $newSharedContacts = [];

        foreach ($contacts as $contact) {
            // check not owned contacts
            if (Gate::denies("owned-contact", $contact)) {
                array_push($skipped, [
                    "contact" => $contact->email,
                    "reason" => "not owned."
                ]);
                continue;
            }

            foreach ($userIds as $userId) {
                // Prevent share to self
                if ($userId === Auth::id()) {
                    array_push($skipped, [
                        "contact" => $contact->email,
                        "reason" => "Nag-share sa kaugalingon."
                    ]);
                    continue;
                }

                $sharedContact = SharedContact::with([
                    "contact.owner"
                ])
                    ->where("user_id", $userId)
                    ->where("contact_id", $contact["id"])
                    ->first();

                // Prevent duplicate
                if ($sharedContact) {
                    array_push($skipped, [
                        "contact" => $sharedContact->email,
                        "reason" => "already shared."
                    ]);
                    continue;
                }


                array_push($newSharedContacts, [
                    "user_id" => $userId,
                    "contact_id" => $contact->id,
                    "permission" => $permission
                ]);
            }
        }

        SharedContact::insert($newSharedContacts);

        return [
            "skipped" => $skipped,
            "shared" => $newSharedContacts,
            "userIds" => $userIds,
        ];
    }

    public function deleteMultiple($input, $userId)
    {
        $contactIds = $input["contacts"];

        // Get contacts
        $matchedContacts = Contact::whereIn("id", $contactIds)->get();


        $softDeletes = [];
        $hardDeletes = []; // for shared contacts nga table

        foreach ($matchedContacts as $contact) {
            if (Gate::denies("owned-contact", $contact)) {
                $hardDeletes[] = [
                    "user_id" => $userId,
                    "contact_id" => $contact->id
                ];
            } else {
                $softDeletes[] = [
                    "id" => $contact->id
                ];
            }
        }

        if (count($softDeletes) > 0)
            Contact::whereIn("id", $contactIds)->delete(); // soft delete ra ni

        if (count($hardDeletes) > 0)
            foreach ($hardDeletes as $delete) {
                SharedContact::where("contact_id", $delete["contact_id"])
                    ->where("user_id", $delete["user_id"])
                    ->delete();
            }

        return [
            "message" => "Success."
        ];
    }
}
