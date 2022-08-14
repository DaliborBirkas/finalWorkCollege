<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220814091927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE random (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product2 DROP FOREIGN KEY FK_B2612B5C12469DE2');
        $this->addSql('DROP INDEX IDX_B2612B5C12469DE2 ON product2');
        $this->addSql('ALTER TABLE product2 DROP category_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE random');
        $this->addSql('ALTER TABLE product2 ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE product2 ADD CONSTRAINT FK_B2612B5C12469DE2 FOREIGN KEY (category_id) REFERENCES category2 (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_B2612B5C12469DE2 ON product2 (category_id)');
    }
}
