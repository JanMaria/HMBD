<?php

declare(strict_types=1);

namespace App\Util;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;

class MonologDoctrineHandler extends AbstractProcessingHandler
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function write(array $record)
    {
        $log = new Log();
        $log
        ->setMessage($record['message'])
        ->setLevel($record['level'])
        ->setLevelName($record['level_name'])
        ->setContext($record['context'])
        ->setChannel($record['channel'])
        ->setExtra($record['extra']);

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
