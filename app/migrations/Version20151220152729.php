<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151220152729 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mac_addresses DROP FOREIGN KEY FK_FDB690817597D3FE');
        $this->addSql('ALTER TABLE mac_addresses ADD CONSTRAINT FK_FDB690817597D3FE FOREIGN KEY (member_id) REFERENCES members (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mac_addresses DROP FOREIGN KEY FK_FDB690817597D3FE');
        $this->addSql('ALTER TABLE mac_addresses ADD CONSTRAINT FK_FDB690817597D3FE FOREIGN KEY (member_id) REFERENCES members (id)');
    }
}
