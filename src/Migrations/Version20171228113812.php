<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171228113812 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE log (id INTEGER NOT NULL, message CLOB NOT NULL, context CLOB NOT NULL --(DC2Type:array)
        , channel VARCHAR(255) NOT NULL, level SMALLINT UNSIGNED NOT NULL, extra CLOB NOT NULL --(DC2Type:array)
        , created INTEGER UNSIGNED NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP INDEX IDX_2DE8C6A3D60322AC');
        $this->addSql('DROP INDEX IDX_2DE8C6A3A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_role AS SELECT user_id, role_id FROM user_role');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('CREATE TABLE user_role (user_id INTEGER NOT NULL, role_id INTEGER NOT NULL, PRIMARY KEY(user_id, role_id), CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user_role (user_id, role_id) SELECT user_id, role_id FROM __temp__user_role');
        $this->addSql('DROP TABLE __temp__user_role');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3D60322AC ON user_role (role_id)');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3A76ED395 ON user_role (user_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE log');
        $this->addSql('DROP INDEX IDX_2DE8C6A3A76ED395');
        $this->addSql('DROP INDEX IDX_2DE8C6A3D60322AC');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_role AS SELECT user_id, role_id FROM user_role');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('CREATE TABLE user_role (user_id INTEGER NOT NULL, role_id INTEGER NOT NULL, PRIMARY KEY(user_id, role_id))');
        $this->addSql('INSERT INTO user_role (user_id, role_id) SELECT user_id, role_id FROM __temp__user_role');
        $this->addSql('DROP TABLE __temp__user_role');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3A76ED395 ON user_role (user_id)');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3D60322AC ON user_role (role_id)');
    }
}
