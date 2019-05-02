<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2019 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190219225540 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE users_user_handles (user_id VARCHAR(255) NOT NULL, user_handle VARCHAR(100) NOT NULL, INDEX IDX_EFD91D5DA76ED395 (user_id), UNIQUE INDEX UNIQ_EFD91D5DF4D23BE4 (user_handle), PRIMARY KEY(user_id, user_handle)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE public_key_credential_sources (id VARCHAR(100) NOT NULL, publicKeyCredentialId LONGTEXT NOT NULL COMMENT \'(DC2Type:base64)\', type VARCHAR(255) NOT NULL, transports LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', attestationType VARCHAR(255) NOT NULL, trustPath JSON NOT NULL COMMENT \'(DC2Type:trust_path)\', aaguid LONGTEXT NOT NULL COMMENT \'(DC2Type:base64)\', credentialPublicKey LONGTEXT NOT NULL COMMENT \'(DC2Type:base64)\', userHandle VARCHAR(255) NOT NULL, counter INT NOT NULL, createdAt DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_user_handles ADD CONSTRAINT FK_EFD91D5DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users_user_handles ADD CONSTRAINT FK_EFD91D5DF4D23BE4 FOREIGN KEY (user_handle) REFERENCES public_key_credential_sources (id)');
        $this->addSql('ALTER TABLE credentials CHANGE user_id user_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE last_login_at last_login_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE display_name displayName VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(true, 'Wildcats Never Quit');
    }
}
