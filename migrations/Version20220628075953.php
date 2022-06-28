<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220628075953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE history_action RENAME INDEX idx_47cc8c92a76ed395 TO IDX_9443C061A76ED395');
        $this->addSql('ALTER TABLE history_action RENAME INDEX idx_47cc8c928db60186 TO IDX_9443C0618DB60186');
        $this->addSql('ALTER TABLE user ADD first_action_time DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE history_action RENAME INDEX idx_9443c0618db60186 TO IDX_47CC8C928DB60186');
        $this->addSql('ALTER TABLE history_action RENAME INDEX idx_9443c061a76ed395 TO IDX_47CC8C92A76ED395');
        $this->addSql('ALTER TABLE user DROP first_action_time');
    }
}
