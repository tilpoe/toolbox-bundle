<?php

namespace Feierstoff\ToolboxBundle\Command;

use Feierstoff\CommandBundle\Util\Cmd;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand("toolbox:prepare-build")]
class PrepareBuildCommand extends Cmd {

    public function isEnabled(): bool{
        // disable on prod
        if (getenv("APP_ENV") === 'prod') {
            return false;
        }

        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->initExecution($input, $output, __DIR__);

        $running = true;
        while ($running) {
            $this->write("********************");

            $option = $this->IO()->choice("----- BUILD PROCESS", [
                "1" => "Prepare database access",
                "2" => "Symfony/React app instructions",
                "e" => "Exit"
            ], "e");

            switch ($option) {
                case "1":
                    $this->info("Generating secrets for production");
                    $this->bash("rm -rf config/secrets/prod/*");
                    $this->bash("APP_RUNTIME_ENV=prod php bin/console secrets:generate-keys");
                    $this->info("Set database password for production");
                    $this->bash("APP_RUNTIME_ENV=prod php bin/console secrets:set DB_PASSWORD");
                    $this->success();
                    break;
                case "2":
                    $this->write("1. Set database variables in .env");
                    $this->write("2. Set TOOLBOX_PATH in .env");
                    $this->write("3. Push to git.");
                    $this->write("4. Copy /config/secrets/prod/prod.decrypt.private.php to production");
                    $this->write("5. On server: execute scripts/install-composer.sh");
                    $this->write("6. On server: COMPOSER=composer-prod.json composer install/update");
                    $this->write("7. On server: npm run build");
                    $this->write("8. On server: php bin/console cache:clear");
                    break;
                default:
                    $running = false;
            }
        }

        return Command::SUCCESS;
    }

}