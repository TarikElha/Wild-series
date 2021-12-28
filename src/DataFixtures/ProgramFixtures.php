<?php

namespace App\DataFixtures;

use App\Entity\Program;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    private Slugify $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function load(ObjectManager $manager): void
    {
        for($i=0; $i<5; $i++){
            $program = new Program();
            $program->setTitle('Program Title ' . $i);
            $slug = $this->slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $program->setSummary('Program Summary ' . $i);
            $program->setCategory($this->getReference('category_' . $i));
            $program->addActor($this->getReference('actor_' . $i));
            $manager->persist($program);
            $this->addReference('program_' . $i, $program);
        }

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
