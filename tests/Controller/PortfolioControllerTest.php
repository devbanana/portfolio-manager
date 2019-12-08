<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\DomCrawler\Field\FormField;

class PortfolioControllerTest extends WebTestCase
{
    private $form;

    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('h1', 'Your Portfolio');

        $cash = $crawler->filter('th[scope="row"] a[href$="/portfolios/Cash-Reserve"]:contains("Cash Reserve")');
        $this->assertEquals(1, $cash->count());

        $stocks = $crawler->filter('th[scope="row"] a[href$="Stocks"]:contains("Stocks")');
        $this->assertEquals(1, $stocks->count());
    }

    public function testAdd()
    {
        $client = static::createClient();
        $this->form = $this->getAddPortfolioForm($client);

        $this->field('name')->setValue('Stock Advisor');
        $this->field('cashReserve')->untick();
        $this->field('unallocated')->untick();
        $this->field('allocationPercent')->setValue('50.00');
        $client->submit($this->form);

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.alert-success', 'Portfolio has been created.');
        $cell = $crawler->filter('th[scope="row"] a[href$="/portfolios/Stock-Advisor"]:contains("Stock Advisor")');
        $this->assertEquals(1, $cell->count());
    }

    public function testAddWithMissingName()
    {
        $client = static::createClient();
        $this->form = $this->getAddPortfolioForm($client);

        $this->field('cashReserve')->untick();
        $this->field('unallocated')->tick();
        $crawler = $client->submit($this->form);

        $error = $crawler->filter('span.form-error-message:contains("This value should not be blank.")');
        $this->assertEquals(1, $error->count());
    }

    public function testAddWithNonUniqueName()
    {
        $client = static::createClient();
        $this->form = $this->getAddPortfolioForm($client);

        $this->field('name')->setValue('Stocks');
        $this->field('cashReserve')->untick();
        $this->field('unallocated')->tick();
        $crawler = $client->submit($this->form);

        $error = $crawler->filter('span.form-error-message:contains("There is already a portfolio by that name.")');
        $this->assertEquals(1, $error->count());
    }

    public function testAddWithUnallocated()
    {
        $client = static::createClient();
        $this->form = $this->getAddPortfolioForm($client);

        $this->field('name')->setValue('Test');
        $this->field('cashReserve')->untick();
        $this->field('unallocated')->tick();
        $client->submit($this->form);

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div.alert-success', 'Portfolio has been created.');
        $cell = $crawler->filter('th[scope="row"]:contains("Test")');
        $this->assertEquals(1, $cell->count());
    }

    public function testAddWithMissingPercent()
    {
        $client = static::createClient();
        $this->form = $this->getAddPortfolioForm($client);

        $this->field('name')->setValue('Test');
        $this->field('cashReserve')->untick();
        $this->field('unallocated')->untick();
        $crawler = $client->submit($this->form);

        $error = $crawler->filter('span.form-error-message:contains("Please enter an allocation percent, or check auto-allocate")');
        $this->assertEquals(1, $error->count());
    }

    public function testAddWithNegativePercent()
    {
        $client = static::createClient();
        $this->form = $this->getAddPortfolioForm($client);

        $this->field('name')->setValue('Test');
        $this->field('cashReserve')->untick();
        $this->field('unallocated')->untick();
        $this->field('allocationPercent')->setValue('-50.00');
        $crawler = $client->submit($this->form);

        $error = $crawler->filter('span.form-error-message:contains("This value should be positive.")');
        $this->assertEquals(1, $error->count());
    }

    public function testAddWithPercentOver100()
    {
        $client = static::createClient();
        $this->form = $this->getAddPortfolioForm($client);

        $this->field('name')->setValue('Test');
        $this->field('cashReserve')->untick();
        $this->field('unallocated')->untick();
        $this->field('allocationPercent')->setValue('110.00');
        $crawler = $client->submit($this->form);

        $error = $crawler->filter('span.form-error-message:contains("Cannot be greater than 100%")');
        $this->assertEquals(1, $error->count());
    }

    public function testViewPortfolio()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/portfolios/Cash-Reserve');
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains('Cash Reserve');
    }

    private function getAddPortfolioForm(AbstractBrowser $client): Form
    {
        $crawler = $client->request('GET', '/portfolios/new');
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains('New Portfolio');

        return $crawler->selectButton('Add')->form();
    }

    private function field(string $field): FormField
    {
        return $this->form->get($this->form->getName() . "[$field]");
    }
}
