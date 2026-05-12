<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260512133227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
		// Ajout des columns en les permettant d'être null dans un premier temps
		$this->addSql('ALTER TABLE "user" ADD last_name  VARCHAR(150) DEFAULT NULL');
		$this->addSql('ALTER TABLE "user" ADD first_name VARCHAR(200) DEFAULT NULL');
		
		// Ajout d'une valeur temporaire à ces champs 
		$this->addSql('UPDATE "user" SET last_name  = \'Inconnu\' WHERE last_name  IS NULL');
		$this->addSql('UPDATE "user" SET first_name = \'Inconnu\' WHERE first_name IS NULL');

		// Empêcher ces champs d'être null à présent
		$this->addSql('ALTER TABLE "user" ALTER COLUMN last_name  SET NOT NULL');
		$this->addSql('ALTER TABLE "user" ALTER COLUMN first_name SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" DROP last_name');
        $this->addSql('ALTER TABLE "user" DROP first_name');
    }
}
