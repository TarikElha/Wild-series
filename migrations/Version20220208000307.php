<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220208000307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE actor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE actor_program (actor_id INT NOT NULL, program_id INT NOT NULL, INDEX IDX_B01827EE10DAF24A (actor_id), INDEX IDX_B01827EE3EB8070A (program_id), PRIMARY KEY(actor_id, program_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, episode_id INT DEFAULT NULL, comment LONGTEXT NOT NULL, rate INT NOT NULL, INDEX IDX_9474526CF675F31B (author_id), INDEX IDX_9474526C362B62A0 (episode_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE watchlist (user_id INT NOT NULL, program_id INT NOT NULL, INDEX IDX_340388D3A76ED395 (user_id), INDEX IDX_340388D33EB8070A (program_id), PRIMARY KEY(user_id, program_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE actor_program ADD CONSTRAINT FK_B01827EE10DAF24A FOREIGN KEY (actor_id) REFERENCES actor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE actor_program ADD CONSTRAINT FK_B01827EE3EB8070A FOREIGN KEY (program_id) REFERENCES program (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C362B62A0 FOREIGN KEY (episode_id) REFERENCES episode (id)');
        $this->addSql('ALTER TABLE watchlist ADD CONSTRAINT FK_340388D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE watchlist ADD CONSTRAINT FK_340388D33EB8070A FOREIGN KEY (program_id) REFERENCES program (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE episode ADD slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE program ADD owner_id INT DEFAULT NULL, ADD updated_at DATETIME NOT NULL, ADD slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE program ADD CONSTRAINT FK_92ED77847E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_92ED77847E3C61F9 ON program (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE actor_program DROP FOREIGN KEY FK_B01827EE10DAF24A');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE program DROP FOREIGN KEY FK_92ED77847E3C61F9');
        $this->addSql('ALTER TABLE watchlist DROP FOREIGN KEY FK_340388D3A76ED395');
        $this->addSql('DROP TABLE actor');
        $this->addSql('DROP TABLE actor_program');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE watchlist');
        $this->addSql('ALTER TABLE category CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE episode DROP slug, CHANGE title title VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE synopsis synopsis LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP INDEX IDX_92ED77847E3C61F9 ON program');
        $this->addSql('ALTER TABLE program DROP owner_id, DROP updated_at, DROP slug, CHANGE title title VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE summary summary LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE poster poster VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE season CHANGE description description LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
