<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;

final class Version20220403120305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Init database';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE pk_credential_sources (id VARCHAR(255) NOT NULL, public_key_credential_id TEXT NOT NULL, type VARCHAR(255) NOT NULL, transports TEXT NOT NULL, attestation_type VARCHAR(255) NOT NULL, trust_path JSON NOT NULL, aaguid TEXT NOT NULL, credential_public_key TEXT NOT NULL, user_handle VARCHAR(255) NOT NULL, counter INT NOT NULL, other_ui TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('COMMENT ON COLUMN pk_credential_sources.public_key_credential_id IS \'(DC2Type:base64)\'');
        $this->addSql('COMMENT ON COLUMN pk_credential_sources.transports IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN pk_credential_sources.trust_path IS \'(DC2Type:trust_path)\'');
        $this->addSql('COMMENT ON COLUMN pk_credential_sources.aaguid IS \'(DC2Type:aaguid)\'');
        $this->addSql('COMMENT ON COLUMN pk_credential_sources.credential_public_key IS \'(DC2Type:base64)\'');
        $this->addSql('COMMENT ON COLUMN pk_credential_sources.other_ui IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN pk_credential_sources.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(
            'CREATE TABLE users (id VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, roles TEXT NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('COMMENT ON COLUMN users.roles IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        throw new LogicException('Wild cats never go back!');
    }
}
