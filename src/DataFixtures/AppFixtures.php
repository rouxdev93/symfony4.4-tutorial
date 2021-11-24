<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Service\DataFixturesService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{
    /**
     * @var DataFixturesService
     */
    protected $dataFixturesService;

    /**
     * AppFixtures constructor.
     * @param DataFixturesService $dataFixturesService
     */
    public function __construct(DataFixturesService $dataFixturesService)
    {
        $this->dataFixturesService = $dataFixturesService;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->dataFixturesService->createUserFixtures();
        $this->dataFixturesService->createMicroPostFixtures();

        $manager->flush();
    }

}
