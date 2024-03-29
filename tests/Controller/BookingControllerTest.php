<?php

namespace App\Test\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private BookingRepository $repository;
    private string $path = '/booking/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Booking::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Booking index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'booking[checkInDate]' => 'Testing',
            'booking[checkOutDate]' => 'Testing',
            'booking[vaccinationStatus]' => 'Testing',
            'booking[dogEntity]' => 'Testing',
        ]);

        self::assertResponseRedirects('/booking/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Booking();
        $fixture->setCheckInDate('My Title');
        $fixture->setCheckOutDate('My Title');
        $fixture->setVaccinationStatus('My Title');
        $fixture->setDogEntity('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Booking');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Booking();
        $fixture->setCheckInDate('My Title');
        $fixture->setCheckOutDate('My Title');
        $fixture->setVaccinationStatus('My Title');
        $fixture->setDogEntity('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'booking[checkInDate]' => 'Something New',
            'booking[checkOutDate]' => 'Something New',
            'booking[vaccinationStatus]' => 'Something New',
            'booking[dogEntity]' => 'Something New',
        ]);

        self::assertResponseRedirects('/booking/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCheckInDate());
        self::assertSame('Something New', $fixture[0]->getCheckOutDate());
        self::assertSame('Something New', $fixture[0]->getVaccinationStatus());
        self::assertSame('Something New', $fixture[0]->getDogEntity());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Booking();
        $fixture->setCheckInDate('My Title');
        $fixture->setCheckOutDate('My Title');
        $fixture->setVaccinationStatus('My Title');
        $fixture->setDogEntity('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/booking/');
    }
}
