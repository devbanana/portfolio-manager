<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DomCrawler\Form;

class AccountControllerTest extends WebTestCase
{
    private function getAddAccountForm(AbstractBrowser $client): Form
    {
        $crawler = $client->request('GET', '/accounts/new');
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains('Add Account');

        return $crawler->selectButton('Add')->form();
    }

    public function testAddAccount()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/accounts');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextNotContains('ul#accounts li', 'Active Investing');

        $form = $this->getAddAccountForm($client);
        $form['account[name]'] = 'Active Investing';
        // Select Taxable.
        $form['account[type]']->select(2);
        $form['account[allocationType]']->select('value');
        $form['account[allocationPercent]'] = 50;
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.flash-notice', 'Account successfully created.');
        $this->assertEquals(1, $crawler->filter('ul#accounts li:contains("Active Investing")')->count());
    }

    public function testPercentNegative()
    {
        $client = static::createClient();
        $form = $this->getAddAccountForm($client);
        $form['account[name]'] = 'Active Investing';
        // Select Taxable.
        $form['account[type]']->select(2);
        $form['account[allocationType]']->select('value');
        $form['account[allocationPercent]'] = -10.0;
        $crawler = $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, $crawler->filter('ul li:contains("Allocation must be between 0% and 100%.")')->count());
    }

    public function testPercentMoreThan100()
    {
        $client = static::createClient();
        $form = $this->getAddAccountForm($client);
        $form['account[name]']->setValue('Active Investing');
        $form['account[type]']->select(2);
        $form['account[allocationType]']->select('value');
        $form['account[allocationPercent]']->setValue(150.00);
        $crawler = $client->submit($form);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, $crawler->filter('ul li:contains("Allocation must be between 0% and 100%.")')->count());
    }
}
