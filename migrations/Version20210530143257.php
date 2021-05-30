<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210530143257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_task_settings (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, task_id INT NOT NULL, is_additional_panel_open TINYINT(1) NOT NULL, is_children_open TINYINT(1) NOT NULL, INDEX IDX_4B0BE7D9A76ED395 (user_id), INDEX IDX_4B0BE7D98DB60186 (task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_task_settings ADD CONSTRAINT FK_4B0BE7D9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_task_settings ADD CONSTRAINT FK_4B0BE7D98DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_task_settings');
    }
}
