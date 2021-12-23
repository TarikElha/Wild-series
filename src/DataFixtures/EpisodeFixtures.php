<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)

    {
        for($i=0; $i<100; $i++){
            $episode = new Episode();
            if(10 > $i)
                $episode->setSeason($this->getReference('season_' . $i));
            else
                $episode->setSeason($this->getReference('season_9'));

            $episode->setTitle('episode title ' . $i);
            $episode->setNumber($i+1);
            $episode->setSynopsis('episode synopsis ' . $i);
            $manager->persist($episode);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            SeasonFixtures::class,
            ];
    }
}