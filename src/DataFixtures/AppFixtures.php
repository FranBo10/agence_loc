<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
     /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        $vehicule = new Vehicule;

        for($i=1; $i<=mt_rand(5,7); $i++) {
            $vehicule->setTitre($this->faker->sentence(30))
                    ->setMarque($this->faker->sentence(10))
                    ->setModele($this->faker->sentence(20))
                    ->setDescription($this->faker->paragraph(250))
                    ->setPhoto($this->faker->imageUrl(640, 480))
                    ->setPrixJournalier($this->faker->randomNumber(2))
                    ->setDateEnregistrement($this->faker->dateTime());
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
