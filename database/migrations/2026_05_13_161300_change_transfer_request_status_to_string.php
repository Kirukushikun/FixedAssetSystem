<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE transfer_requests MODIFY status VARCHAR(100) NOT NULL DEFAULT 'Pending Division Head Approval'");
    }

    public function down(): void
    {
        DB::statement("UPDATE transfer_requests SET status = 'pending' WHERE status = 'Pending Division Head Approval'");
        DB::statement("UPDATE transfer_requests SET status = 'approved' WHERE status = 'Approved'");
        DB::statement("UPDATE transfer_requests SET status = 'rejected' WHERE status = 'Rejected'");
        DB::statement("ALTER TABLE transfer_requests MODIFY status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
    }
};
