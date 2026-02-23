<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260223212617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE predictions (id VARCHAR(36) NOT NULL, match_id VARCHAR(36) NOT NULL, home_team_id VARCHAR(36) NOT NULL, away_team_id VARCHAR(36) NOT NULL, league_id VARCHAR(36) NOT NULL, home_team_name VARCHAR(255) NOT NULL, away_team_name VARCHAR(255) NOT NULL, league_code VARCHAR(10) NOT NULL, match_start_date DATETIME NOT NULL, home_win_probability NUMERIC(5, 4) NOT NULL, draw_probability NUMERIC(5, 4) NOT NULL, away_win_probability NUMERIC(5, 4) NOT NULL, home_expected_goals NUMERIC(5, 3) NOT NULL, away_expected_goals NUMERIC(5, 3) NOT NULL, home_odds NUMERIC(6, 2) NOT NULL, draw_odds NUMERIC(6, 2) NOT NULL, away_odds NUMERIC(6, 2) NOT NULL, most_likely_home_goals INT NOT NULL, most_likely_away_goals INT NOT NULL, score_matrix JSON NOT NULL, calculated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8E87BCE62ABEACD6 (match_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE predictions');
    }
}
