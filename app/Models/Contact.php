<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Contact extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'firstname',
        'lastname',
        "company",
        'email',
        "phone_number",
        "user_id"
    ];

    public static function getContacts(int $limit, string $searchTerm = "")
    {
        $userId = Auth::id();

        $ownedContacts = DB::table('contacts as c')
            ->select(
                'c.id',
                'c.firstname',
                'c.lastname',
                'c.company',
                'c.phone_number',
                'c.email',
                'c.created_at',
                'u.id as owner_id',
                DB::raw("CONCAT(u.firstname, ' ', u.lastname) as owner_name"),
                DB::raw("'owner' as permission")
            )
            ->join('users as u', 'c.user_id', '=', 'u.id')
            ->where('c.user_id', $userId)
            ->whereNull('c.deleted_at');

        // Shared contacts query
        $sharedContacts = DB::table('shared_contacts as sc')
            ->select(
                'c.id',
                'c.firstname',
                'c.lastname',
                'c.company',
                'c.phone_number',
                'c.email',
                'c.created_at',
                'u.id as owner_id',
                DB::raw("CONCAT(u.firstname, ' ', u.lastname) as owner_name"),
                'sc.permission'
            )
            ->join('contacts as c', 'sc.contact_id', '=', 'c.id')
            ->join('users as u', 'c.user_id', '=', 'u.id')
            ->where('sc.user_id', $userId)
            ->where('sc.confirmed', true)
            ->whereNull('c.deleted_at');


        if (!empty($searchTerm)) {
            $searchTerm = "%" . $searchTerm . "%";

            $ownedContacts->where(function ($query) use ($searchTerm) {
                $query->whereRaw(DB::raw("CONCAT(c.firstname, ' ', c.lastname) LIKE ?"), [$searchTerm])
                    ->orWhere('c.company', 'LIKE', $searchTerm)
                    ->orWhere('c.email', 'LIKE', $searchTerm)
                    ->orWhere('c.phone_number', 'LIKE', $searchTerm);
            });

            $sharedContacts->where(function ($query) use ($searchTerm) {
                $query->whereRaw(DB::raw("CONCAT(c.firstname, ' ', c.lastname) LIKE ?"), [$searchTerm])
                    ->orWhere('c.lastname', 'LIKE', $searchTerm)
                    ->orWhere('c.company', 'LIKE', $searchTerm)
                    ->orWhere('c.email', 'LIKE', $searchTerm);
            });
        }

        // Combine both queries with UNION ALL
        $contactsQuery = $ownedContacts
            ->unionAll($sharedContacts)
            ->orderBy('created_at', 'desc');


        $contacts = $contactsQuery->paginate($limit);
        return $contacts;
    }



    public static function checkOwnerShip($contactId)
    {
        $userId = Auth::id();
        $contact = DB::table("contacts")->find($contactId);

        if ($contact->user_id == $userId) return true;
        return false;
    }

    public static function paginateContact($contacts, $limit = 5, $pageNumber = 1)
    {
        $pageNumber = $pageNumber;
        $count = count($contacts);
        $pageCount = ceil($count / $limit);
        $startIndex = ($pageNumber - 1) * $limit;

        return [
            "count" => $count,
            "pageCount" => $pageCount,
            "result" => array_slice($contacts, $startIndex, $limit)
        ];
    }

    public static function getSelectedContacts(array $ids)
    {
        return Contact::whereIn("id", $ids)->get();
    }
}
