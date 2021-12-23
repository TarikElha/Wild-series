<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for($i=0; $i<10; $i++){
            $season = new Season();
            
            if(5 > $i)
                $season->setProgram($this->getReference('program_' . $i));
            else
                $season->setProgram($this->getReference('program_4'));

            
            $season->setNumber($i+1);
            $season->setYear(1989);
            $season->setDescription("Description season " . $i);
            $manager->persist($season);
            $this->addReference('season_' . $i, $season);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProgramFixtures::class,
            ];
    }
}