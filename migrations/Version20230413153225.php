<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230413153225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_C4E0A61FA76ED395 ON team (user_id)');
        $this->addSql('ALTER TABLE team_members DROP FOREIGN KEY FK_BAD9A3C8B842D717');
        $this->addSql('DROP INDEX IDX_BAD9A3C8B842D717 ON team_members');
        $this->addSql('ALTER TABLE team_members CHANGE team_id_id team_id INT NOT NULL');
        $this->addSql('ALTER TABLE team_members ADD CONSTRAINT FK_BAD9A3C8296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_BAD9A3C8296CD8AE ON team_members (team_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FA76ED395');
        $this->addSql('DROP INDEX IDX_C4E0A61FA76ED395 ON team');
        $this->addSql('ALTER TABLE team DROP user_id');
        $this->addSql('ALTER TABLE team_members DROP FOREIGN KEY FK_BAD9A3C8296CD8AE');
        $this->addSql('DROP INDEX IDX_BAD9A3C8296CD8AE ON team_members');
        $this->addSql('ALTER TABLE team_members CHANGE team_id team_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE team_members ADD CONSTRAINT FK_BAD9A3C8B842D717 FOREIGN KEY (team_id_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_BAD9A3C8B842D717 ON team_members (team_id_id)');
    }
}
