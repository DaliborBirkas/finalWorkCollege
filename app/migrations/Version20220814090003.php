<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220814090003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorite2 (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, likes INT NOT NULL, UNIQUE INDEX UNIQ_E5B211F34584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order2 (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, order_note VARCHAR(255) NOT NULL, order_date DATE NOT NULL, sent TINYINT(1) NOT NULL, price NUMERIC(10, 2) NOT NULL, paid TINYINT(1) NOT NULL, INDEX IDX_E4F48CE8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ordered_products2 (id INT AUTO_INCREMENT NOT NULL, order_number_id INT DEFAULT NULL, product_id INT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, paid TINYINT(1) NOT NULL, INDEX IDX_51E880638C26A5E8 (order_number_id), INDEX IDX_51E880634584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favorite2 ADD CONSTRAINT FK_E5B211F34584665A FOREIGN KEY (product_id) REFERENCES product2 (id)');
        $this->addSql('ALTER TABLE order2 ADD CONSTRAINT FK_E4F48CE8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ordered_products2 ADD CONSTRAINT FK_51E880638C26A5E8 FOREIGN KEY (order_number_id) REFERENCES order2 (id)');
        $this->addSql('ALTER TABLE ordered_products2 ADD CONSTRAINT FK_51E880634584665A FOREIGN KEY (product_id) REFERENCES product2 (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite2 DROP FOREIGN KEY FK_E5B211F34584665A');
        $this->addSql('ALTER TABLE order2 DROP FOREIGN KEY FK_E4F48CE8A76ED395');
        $this->addSql('ALTER TABLE ordered_products2 DROP FOREIGN KEY FK_51E880638C26A5E8');
        $this->addSql('ALTER TABLE ordered_products2 DROP FOREIGN KEY FK_51E880634584665A');
        $this->addSql('DROP TABLE favorite2');
        $this->addSql('DROP TABLE order2');
        $this->addSql('DROP TABLE ordered_products2');
    }
}
