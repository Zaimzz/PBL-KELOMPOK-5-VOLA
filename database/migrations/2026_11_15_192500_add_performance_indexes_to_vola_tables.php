<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $isPgsql = DB::connection()->getDriverName() === 'pgsql';

        // 1. Aktifkan Ekstensi pg_trgm untuk PostgreSQL / Supabase
        if ($isPgsql) {
            DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm;');
        }

        // 2. Index pada tabel `users`
        Schema::table('users', function (Blueprint $table) {
            $table->index('status', 'users_status_idx');
            $table->index('created_at', 'users_created_at_idx');
            $table->index(['role', 'status'], 'users_role_status_idx');
        });

        // 3. Index pada tabel `organizer_profiles`
        Schema::table('organizer_profiles', function (Blueprint $table) {
            $table->index('city', 'organizer_profiles_city_idx');
            $table->index('created_at', 'organizer_profiles_created_at_idx');
            $table->index(['verification_status', 'created_at'], 'organizer_profiles_vstatus_created_idx');

            if (Schema::hasColumn('organizer_profiles', 'nib')) {
                $table->index('nib', 'organizer_profiles_nib_idx');
            }
            if (Schema::hasColumn('organizer_profiles', 'legal_number')) {
                $table->index('legal_number', 'organizer_profiles_legal_num_idx');
            }
        });

        // 4. Index pada tabel `events`
        Schema::table('events', function (Blueprint $table) {
            $table->index('start_date', 'events_start_date_idx');
            $table->index('end_date', 'events_end_date_idx');
            $table->index('reviewed_at', 'events_reviewed_at_idx');
            $table->index('published_at', 'events_published_at_idx');
            $table->index('created_at', 'events_created_at_idx');
            $table->index(['status', 'created_at'], 'events_status_created_idx');
            $table->index(['status', 'end_date', 'registration_deadline'], 'events_status_end_reg_idx');
        });

        // 5. Index pada tabel `payments`
        Schema::table('payments', function (Blueprint $table) {
            $table->index('paid_at', 'payments_paid_at_idx');
            $table->index('created_at', 'payments_created_at_idx');
            $table->index(['status', 'created_at'], 'payments_status_created_idx');
            $table->index(['status', 'paid_at'], 'payments_status_paid_idx');
        });

        // 6. Index pada tabel `reports`
        if (Schema::hasTable('reports')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->index('status', 'reports_status_idx');
                $table->index('created_at', 'reports_created_at_idx');
                $table->index(['status', 'created_at'], 'reports_status_created_idx');
            });
        }

        // 7. Index pada tabel `applications`
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->index('status', 'applications_status_idx');
                $table->index(['status', 'position_id'], 'applications_status_position_idx');
                $table->index(['volunteer_profile_id', 'status'], 'applications_vol_profile_status_idx');
            });
        }

        // 8. Tambahkan GIN Trigram Indexes untuk pencarian teks super cepat di PostgreSQL
        if ($isPgsql) {
            DB::statement('CREATE INDEX IF NOT EXISTS events_title_trgm_idx ON events USING gin (title gin_trgm_ops);');
            DB::statement('CREATE INDEX IF NOT EXISTS organizer_profiles_org_name_trgm_idx ON organizer_profiles USING gin (organization_name gin_trgm_ops);');
            DB::statement('CREATE INDEX IF NOT EXISTS users_name_trgm_idx ON users USING gin (name gin_trgm_ops);');
            DB::statement('CREATE INDEX IF NOT EXISTS users_email_trgm_idx ON users USING gin (email gin_trgm_ops);');

            if (Schema::hasTable('reports')) {
                DB::statement('CREATE INDEX IF NOT EXISTS reports_ticket_num_trgm_idx ON reports USING gin (ticket_number gin_trgm_ops);');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $isPgsql = DB::connection()->getDriverName() === 'pgsql';

        if ($isPgsql) {
            DB::statement('DROP INDEX IF EXISTS events_title_trgm_idx;');
            DB::statement('DROP INDEX IF EXISTS organizer_profiles_org_name_trgm_idx;');
            DB::statement('DROP INDEX IF EXISTS users_name_trgm_idx;');
            DB::statement('DROP INDEX IF EXISTS users_email_trgm_idx;');
            DB::statement('DROP INDEX IF EXISTS reports_ticket_num_trgm_idx;');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_status_idx');
            $table->dropIndex('users_created_at_idx');
            $table->dropIndex('users_role_status_idx');
        });

        Schema::table('organizer_profiles', function (Blueprint $table) {
            $table->dropIndex('organizer_profiles_city_idx');
            $table->dropIndex('organizer_profiles_created_at_idx');
            $table->dropIndex('organizer_profiles_vstatus_created_idx');

            if (Schema::hasColumn('organizer_profiles', 'nib')) {
                $table->dropIndex('organizer_profiles_nib_idx');
            }
            if (Schema::hasColumn('organizer_profiles', 'legal_number')) {
                $table->dropIndex('organizer_profiles_legal_num_idx');
            }
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('events_start_date_idx');
            $table->dropIndex('events_end_date_idx');
            $table->dropIndex('events_reviewed_at_idx');
            $table->dropIndex('events_published_at_idx');
            $table->dropIndex('events_created_at_idx');
            $table->dropIndex('events_status_created_idx');
            $table->dropIndex('events_status_end_reg_idx');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_paid_at_idx');
            $table->dropIndex('payments_created_at_idx');
            $table->dropIndex('payments_status_created_idx');
            $table->dropIndex('payments_status_paid_idx');
        });

        if (Schema::hasTable('reports')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->dropIndex('reports_status_idx');
                $table->dropIndex('reports_created_at_idx');
                $table->dropIndex('reports_status_created_idx');
            });
        }

        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropIndex('applications_status_idx');
                $table->dropIndex('applications_status_position_idx');
                $table->dropIndex('applications_vol_profile_status_idx');
            });
        }
    }
};
