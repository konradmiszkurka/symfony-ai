<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260223201457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE football_matches (id VARCHAR(36) NOT NULL, start_date DATETIME NOT NULL, status VARCHAR(20) NOT NULL, home_score INT DEFAULT NULL, away_score INT DEFAULT NULL, matchday INT DEFAULT NULL, external_id INT DEFAULT NULL, league_id VARCHAR(36) NOT NULL, home_team_id VARCHAR(36) NOT NULL, away_team_id VARCHAR(36) NOT NULL, UNIQUE INDEX UNIQ_32A020A9F75D7B0 (external_id), INDEX IDX_32A020A58AFC4DE (league_id), INDEX IDX_32A020A9C4C13F6 (home_team_id), INDEX IDX_32A020A45185D02 (away_team_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE leagues (id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(10) NOT NULL, country VARCHAR(255) NOT NULL, current_season VARCHAR(20) DEFAULT NULL, external_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_85CE39E9F75D7B0 (external_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE teams (id VARCHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, short_name VARCHAR(100) DEFAULT NULL, tla VARCHAR(5) DEFAULT NULL, crest VARCHAR(500) DEFAULT NULL, external_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_96C222589F75D7B0 (external_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE football_matches ADD CONSTRAINT FK_32A020A58AFC4DE FOREIGN KEY (league_id) REFERENCES leagues (id)');
        $this->addSql('ALTER TABLE football_matches ADD CONSTRAINT FK_32A020A9C4C13F6 FOREIGN KEY (home_team_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE football_matches ADD CONSTRAINT FK_32A020A45185D02 FOREIGN KEY (away_team_id) REFERENCES teams (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE football_matches DROP FOREIGN KEY FK_32A020A58AFC4DE');
        $this->addSql('ALTER TABLE football_matches DROP FOREIGN KEY FK_32A020A9C4C13F6');
        $this->addSql('ALTER TABLE football_matches DROP FOREIGN KEY FK_32A020A45185D02');
        $this->addSql('DROP TABLE football_matches');
        $this->addSql('DROP TABLE leagues');
        $this->addSql('DROP TABLE teams');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
