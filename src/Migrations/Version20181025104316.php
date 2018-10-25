<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181025104316 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64912469DE2');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP INDEX IDX_8D93D64912469DE2 ON user');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, ADD password VARCHAR(255) NOT NULL, DROP role_id, DROP date_inscription, CHANGE email email VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD role_id INT NOT NULL, ADD date_inscription DATETIME NOT NULL, DROP roles, DROP password, CHANGE email email VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64912469DE2 FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64912469DE2 ON user (role_id)');
    }
}
