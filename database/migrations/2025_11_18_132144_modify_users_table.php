<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 標準の不要カラム削除
            $table->dropColumn(['name', 'email', 'email_verified_at', 'remember_token']);

            // 社員番号やログインID用
            $table->string('user_id', 50)->unique()->after('id');

            // 権限管理用
            $table->string('role', 20)->default('user')->after('user_id');

            // 初期パスワード変更フラグ
            $table->boolean('must_change_password')->default(true)->after('password');
        });
    }

    public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        if (Schema::hasColumn('users', 'user_id')) {
            $table->dropColumn('user_id');
        }
        if (Schema::hasColumn('users', 'role')) {
            $table->dropColumn('role');
        }
        if (Schema::hasColumn('users', 'must_change_password')) {
            $table->dropColumn('must_change_password');
        }
    });
}

};
