<?php

namespace App\Models;

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


    public function  contact() {
        return $this->belongsTo(Contact::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }


    public static function getUnconfirmedSharedContacts()
    {
        $userId = Auth::id();

        $sharedContacts = SharedContact::with([
            "contact.owner"
        ])
        ->where("user_id", $userId)
        ->where("confirmed", 0)
        ->whereHas("contact", fn($q) => $q->whereNull("deleted_at"))
        ->get();

        return $sharedContacts;
    }
}
