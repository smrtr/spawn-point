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
    protected static $directories = array
    (
        'app',
        'app/config',
        'public'
    );

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
        $root_path = realpath(dirname(__FILE__).'/../../../../../..');
        $fixtures_path = realpath(dirname(__FILE__).'/../../../fixtures');

        $output->writeln('Checking directories...');

        foreach (self::$directories as $directory) {

            if (is_dir($directory)) {

                $output->writeln('<info>'.$directory.' already exists</info>');

            } elseif (is_file($directory)) {

                $output->writeln('<error>Cannot create directory '.$directory.'; file exists with the same name</error>');

            } else {

                if (mkdir($directory)) {
                    $output->writeln('<info>Directory '.$directory.' created</info>');
                } else {
                    $output->writeln('<error>Failed to create directory '.$directory.'</error>');
                }
            }
        }

        $output->writeln('Checking fixtures...');

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
