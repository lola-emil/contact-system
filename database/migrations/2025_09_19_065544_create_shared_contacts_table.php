<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shared_contacts', function (Blueprint $table) {
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->foreignId("contact_id")->constrained("contacts")->onDelete("cascade");

            $table->enum("permission", ["editor", "viewer"])->default("viewer");
            $table->boolean("confirmed")->default(false);
            $table->timestamp("shared_at")->useCurrent();

            $table->primary(["user_id", "contact_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_contacts');
    }
};
