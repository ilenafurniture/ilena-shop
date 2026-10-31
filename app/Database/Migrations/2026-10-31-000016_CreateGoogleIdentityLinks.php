<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGoogleIdentityLinks extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('user_google_identities')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'user_email' => ['type' => 'VARCHAR', 'constraint' => 191],
                'provider' => ['type' => 'VARCHAR', 'constraint' => 32, 'default' => 'google'],
                'subject' => ['type' => 'VARCHAR', 'constraint' => 255],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['provider', 'subject'], 'uq_google_identity_subject');
            $this->forge->addUniqueKey(['user_email', 'provider'], 'uq_google_identity_user_provider');
            $this->forge->addKey('user_email', false, false, 'idx_google_identity_email');
            $this->forge->createTable('user_google_identities', true);
        }

        if (! $this->db->tableExists('user_password_resets')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'email' => ['type' => 'VARCHAR', 'constraint' => 191],
                'token_hash' => ['type' => 'CHAR', 'constraint' => 64],
                'expires_at' => ['type' => 'DATETIME'],
                'created_at' => ['type' => 'DATETIME'],
                'consumed_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('token_hash', 'uq_password_reset_token_hash');
            $this->forge->addKey(['email', 'expires_at'], false, false, 'idx_password_reset_email_expiry');
            $this->forge->createTable('user_password_resets', true);
        }
    }

    public function down()
    {
        // Retain identity links when rolling application code back.
    }
}
