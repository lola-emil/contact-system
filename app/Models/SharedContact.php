<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class SharedContact extends Model
{
    public $timestamps = false;
    public $primaryKey = null;
    public $incrementing = false;  // We do not use auto-incrementing IDs
    protected $fillable = [
        "user_id",
        "contact_id",
        "permission",
        "confirmed",
    ];


    public static function getUnconfirmedSharedContacts()
    {
        $userId = Auth::id();

        $sharedContacts = DB::table('shared_contacts')
            ->select(
                'shared_contacts.*',
                DB::raw("CONCAT(contacts.firstname, ' ', contacts.lastname) AS contact_name"),
                DB::raw("CONCAT(users.firstname, ' ', users.lastname) AS owner")
            )
            ->join('contacts', 'contacts.id', '=', 'shared_contacts.contact_id')
            ->join('users', 'contacts.user_id', '=', 'users.id')
            ->where('shared_contacts.user_id', $userId)
            ->where('shared_contacts.confirmed', 0)
            ->whereNull('contacts.deleted_at')
            ->get();

        return $sharedContacts;
    }
}
