<?php

namespace phpMyFAQ;

use phpMyFAQ\Configuration\DatabaseConfiguration;
use phpMyFAQ\Database\Sqlite3;
use PHPUnit\Framework\TestCase;

class SitemapTest extends TestCase
{
    private Sitemap $sitemap;

    private Sqlite3 $db;

    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER['HTTP_HOST'] = 'faq.example.org';

        $dbConfig = new DatabaseConfiguration(PMF_TEST_DIR . '/content/core/config/database.php');
        Database::setTablePrefix($dbConfig->getPrefix());
        $this->db = Database::factory($dbConfig->getType());
        $this->db->connect(
            $dbConfig->getServer(),
            $dbConfig->getUser(),
            $dbConfig->getPassword(),
            $dbConfig->getDatabase(),
            $dbConfig->getPort()
        );
        $configuration = new Configuration($this->db);
        $configuration->set('main.referenceURL', 'https://faq.example.org/');

        $language = new Language($configuration);
        $language->setLanguage(false, 'en');
        $configuration->setLanguage($language);

        $this->sitemap = new Sitemap($configuration);

        $this->db->query(
            'INSERT INTO faqdata ' .
            '(id, lang, solution_id, sticky, thema, content, keywords, active, author, email, updated) VALUES ' .
            '(1, \'en\', 1000, \'yes\', \'sample question\', \'sample answer\', \'sample keywords\', \'yes\', \'Author\', \'test@example.org\', \'date\')'
        );
        $this->db->query('INSERT INTO faqdata_group (record_id, group_id) VALUES (1,-1)');
        $this->db->query('INSERT INTO faqdata_user (record_id, user_id) VALUES (1,-1)');
    }

    protected function tearDown(): void
    {
        $this->db->query('DELETE FROM faqdata WHERE id = 1');
        $this->db->query('DELETE FROM faqdata_group WHERE record_id = 1');
        $this->db->query('DELETE FROM faqdata_user WHERE record_id = 1');
    }

    public function testGetAllFirstLetters(): void
    {
        $letters = $this->sitemap->getAllFirstLetters();
        $this->assertIsString($letters);
        $this->assertEquals(
            '<ul class="nav"><li class="nav-item"><a class="nav-link" href="https://faq.example.org/sitemap/S/en.html">S</a></li></ul>',
            $letters
        );
    }
}
