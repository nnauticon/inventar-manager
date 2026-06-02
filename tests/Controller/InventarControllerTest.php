<?php

namespace App\Tests\Controller;

use App\Entity\Inventar;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class InventarControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;

    /** @var EntityRepository<Inventar> */
    private EntityRepository $inventarRepository;
    private string $path = '/inventar/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->inventarRepository = $this->manager->getRepository(Inventar::class);

        foreach ($this->inventarRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'inventar[name]' => 'Testing',
            'inventar[serialNumber]' => 'Testing',
            'inventar[purchasedAt]' => '2026-06-02T12:00', 
        ]);

        self::assertResponseRedirects();
        self::assertSame(1, $this->inventarRepository->count([]));
    }

    public function testShow(): void
    {
        $fixture = new Inventar();
        $fixture->setName('My Title');
        $fixture->setSerialNumber('001Test');
        $fixture->setPurchasedAt(new \DateTimeImmutable('now'));

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
    }

    public function testEdit(): void
    {
        $fixture = new Inventar();
        $fixture->setName('Value');
        $fixture->setSerialNumber('002Test');
        $fixture->setPurchasedAt(new \DateTimeImmutable('now'));

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'inventar[name]' => 'Something New',
            'inventar[serialNumber]' => 'Something New',
            'inventar[purchasedAt]' => '2026-06-03T12:00',
        ]);

        self::assertResponseRedirects();

        $allFixtures = $this->inventarRepository->findAll();
        $updatedFixture = $allFixtures[0];

        self::assertSame('Something New', $updatedFixture->getName());
        self::assertSame('Something New', $updatedFixture->getSerialNumber());
        self::assertSame('2026-06-03', $updatedFixture->getPurchasedAt()->format('Y-m-d'));
    }

    public function testRemove(): void
    {
        $fixture = new Inventar();
        $fixture->setName('Value');
        $fixture->setSerialNumber('003Test');
        $fixture->setPurchasedAt(new \DateTimeImmutable('now'));

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects();
        self::assertSame(0, $this->inventarRepository->count([]));
    }
}
