<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201123101530 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quack_tag (quack_id INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY(quack_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_C7845150D3950CA9 ON quack_tag (quack_id)');
        $this->addSql('CREATE INDEX IDX_C7845150BAD26311 ON quack_tag (tag_id)');
        $this->addSql('CREATE TABLE tag (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('DROP INDEX IDX_9474526CD3950CA9');
        $this->addSql('DROP INDEX IDX_9474526CF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, quack_id, author_id, content, created_at FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, quack_id INTEGER NOT NULL, author_id INTEGER NOT NULL, content VARCHAR(255) NOT NULL COLLATE BINARY, created_at DATETIME DEFAULT NULL, CONSTRAINT FK_9474526CD3950CA9 FOREIGN KEY (quack_id) REFERENCES quack (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES duck (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO comment (id, quack_id, author_id, content, created_at) SELECT id, quack_id, author_id, content, created_at FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE INDEX IDX_9474526CD3950CA9 ON comment (quack_id)');
        $this->addSql('CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)');
        $this->addSql('DROP INDEX IDX_83D44F6FF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__quack AS SELECT id, author_id, file_name, content, created_at FROM quack');
        $this->addSql('DROP TABLE quack');
        $this->addSql('CREATE TABLE quack (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL COLLATE BINARY, content CLOB NOT NULL COLLATE BINARY, created_at DATETIME NOT NULL, CONSTRAINT FK_83D44F6FF675F31B FOREIGN KEY (author_id) REFERENCES duck (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO quack (id, author_id, file_name, content, created_at) SELECT id, author_id, file_name, content, created_at FROM __temp__quack');
        $this->addSql('DROP TABLE __temp__quack');
        $this->addSql('CREATE INDEX IDX_83D44F6FF675F31B ON quack (author_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE quack_tag');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP INDEX IDX_9474526CD3950CA9');
        $this->addSql('DROP INDEX IDX_9474526CF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, quack_id, author_id, content, created_at FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, quack_id INTEGER NOT NULL, author_id INTEGER NOT NULL, content VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO comment (id, quack_id, author_id, content, created_at) SELECT id, quack_id, author_id, content, created_at FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE INDEX IDX_9474526CD3950CA9 ON comment (quack_id)');
        $this->addSql('CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)');
        $this->addSql('DROP INDEX IDX_83D44F6FF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__quack AS SELECT id, author_id, file_name, content, created_at FROM quack');
        $this->addSql('DROP TABLE quack');
        $this->addSql('CREATE TABLE quack (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, tags CLOB DEFAULT NULL COLLATE BINARY --(DC2Type:array)
        )');
        $this->addSql('INSERT INTO quack (id, author_id, file_name, content, created_at) SELECT id, author_id, file_name, content, created_at FROM __temp__quack');
        $this->addSql('DROP TABLE __temp__quack');
        $this->addSql('CREATE INDEX IDX_83D44F6FF675F31B ON quack (author_id)');
    }
}
