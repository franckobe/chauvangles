<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181012150436 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE group_message (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, group__id INT NOT NULL, content LONGTEXT NOT NULL, date_emission DATETIME NOT NULL, date_reception LONGTEXT NOT NULL, date_read DATETIME NOT NULL, UNIQUE INDEX UNIQ_30BD6473F624B39D (sender_id), UNIQUE INDEX UNIQ_30BD6473E5D32D49 (group__id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE group_message ADD CONSTRAINT FK_30BD6473F624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE group_message ADD CONSTRAINT FK_30BD6473E5D32D49 FOREIGN KEY (group__id) REFERENCES `group` (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE group_message');
    }
}
