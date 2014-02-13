<?php

namespace Smrtr\SpawnPoint;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SpawnCommand
 * vendor/bin/spawn-point spawn
 *
 * @package Smrtr\SpawnPoint
 * @author Joe Green
 */
class SpawnCommand extends Command
{
    /**
     * @var array
     */
    protected static $fixtures = array
    (
        'app/bootstrap.php',
        'app/config/hostgroups.ini',
        'app/config/phpSettings.ini',
        'app/config/routes.ini',
        'public/.htaccess',
        'public/index.php'
    );

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('spawn')
            ->setDescription('Create an empty project')
        ;
    }

    /**
     * Copy fixtures into root path.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Checking fixtures...');

        $root_path = realpath(dirname(__FILE__).'/../../../../../..');
        $fixtures_path = realpath(dirname(__FILE__).'/../../../fixtures');

        foreach (self::$fixtures as $fixture) {

            if (is_file($root_path.'/'.$fixture)) {

                $output->writeln('<info>'.$fixture.' already exists</info>');

            } else {

                $result = copy(
                    $fixtures_path.'/'.$fixture,
                    $root_path.'/'.$fixture
                );

                if ($result) {
                    $output->writeln('<info>'.$fixture.' copied to '.$root_path.'</info>');
                } else {
                    $output->writeln('<error>Failed to copy '.$fixture.'</error>');
                }
            }
        }
    }
}
