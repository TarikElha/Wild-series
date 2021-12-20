<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $program1 = new Program();
        $program2 = new Program();

        $program1->setTitle('The Walking Dead');
        $program1->setSummary('Une histoire de zombies, un truc comme ça.');
        $program1->setCategory($this->getReference('category_4'));

        $program2->setTitle('Fear The Walking Dead');
        $program2->setSummary('Encore une histoire de zombies réchauffée.');
        $program2->setCategory($this->getReference('category_3'));

        $program2->addActor($this->getReference('actor_0'));

        for($i=0; $i<count(ActorFixtures::ACTORS); $i++){
            $program1->addActor($this->getReference('actor_' . $i));
        }

        $manager->persist($program1);
        $manager->persist($program2);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
            ActorFixtures::class,
            ];
    }
}
