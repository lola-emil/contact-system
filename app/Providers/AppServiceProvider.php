<?php

namespace App\Providers;

use App\Models\Contact;
use App\Models\SharedContact;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define("update-contact", function (User $user, SharedContact $sharedContact) {
            $matchedContact = Contact::find($sharedContact->contact_id);

            $isOwner = $user->id === $matchedContact->user_id;
            $isEditor =  $sharedContact->user_id === $user->id
                && $sharedContact->permission === "editor";

            if ($isOwner || $isEditor) return true;


            return false;
        });

        Gate::define("owned-contact", function (User $user, Contact $contact) {
            return $user->id === $contact->user_id;
        });
    }
}
