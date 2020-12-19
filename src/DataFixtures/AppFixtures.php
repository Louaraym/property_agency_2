<?php

namespace App\DataFixtures;

use App\Entity\Property;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->faker = Factory::create('fr_FR');
        $this->entityManager = $entityManager;
    }

    public function load(ObjectManager $manager):void
    {
        $this->loadData();
        $manager->flush();
    }

    public function loadData(): void
    {
        for ($i=0; $i<150; $i++){

            $property = new Property();

            $property
                ->setTitle($this->faker->words($nb = 3, $asText = true))
                ->setSurface($this->faker->numberBetween(20,350))
                ->setPrice($this->faker->numberBetween(100000,1000000))
                ->setRoomNumber($this->faker->numberBetween(2,10))
                ->setBedroomNumber($this->faker->numberBetween(1,9))
                ->setHeat($this->faker->numberBetween(0, count(Property::HEAT) - 1))
                ->setFloor($this->faker->numberBetween(0,15))
                ->setAddress($this->faker->address)
                ->setCity($this->faker->city)
                ->setPostalCode($this->faker->postcode)
                ->setDescription($this->faker->sentences($nb = 5, $asText = true))
                ->setCreatedAt($this->faker->dateTimeBetween('-5 years', 'now'))
           ;

            $this->entityManager->persist($property);
        }
    }
}
