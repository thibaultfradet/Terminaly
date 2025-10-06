<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251006163608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, sale_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, price NUMERIC(7, 2) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D34A04AD12469DE2 (category_id), INDEX IDX_D34A04AD4A7E4868 (sale_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sale (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', payment_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sale_product (id INT AUTO_INCREMENT NOT NULL, sale_id INT DEFAULT NULL, product_id INT DEFAULT NULL, quantity NUMERIC(5, 2) NOT NULL, price NUMERIC(7, 2) NOT NULL, INDEX IDX_A654C63F4A7E4868 (sale_id), INDEX IDX_A654C63F4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD4A7E4868 FOREIGN KEY (sale_id) REFERENCES sale (id)');
        $this->addSql('ALTER TABLE sale_product ADD CONSTRAINT FK_A654C63F4A7E4868 FOREIGN KEY (sale_id) REFERENCES sale (id)');
        $this->addSql('ALTER TABLE sale_product ADD CONSTRAINT FK_A654C63F4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD4A7E4868');
        $this->addSql('ALTER TABLE sale_product DROP FOREIGN KEY FK_A654C63F4A7E4868');
        $this->addSql('ALTER TABLE sale_product DROP FOREIGN KEY FK_A654C63F4584665A');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE sale');
        $this->addSql('DROP TABLE sale_product');
        $this->addSql('DROP TABLE `user`');
    }
}
