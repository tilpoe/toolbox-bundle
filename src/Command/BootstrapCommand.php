<?php

namespace Feierstoff\ToolboxBundle\Command;

use Feierstoff\CommandBundle\Util\Cmd;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand("toolbox:bootstrap")]
class BootstrapCommand extends Cmd {

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
            $this->write("");
            $this->write("********************");
            $this->write("Welcome to your bootstrapper.");
            $this->write("Please choose an option.");

            $option = $this->IO()->choice("----- OPTIONS", [
                "1" => "Install composer packages",
                "2" => "Configuration menu",
                "3" => "Initialize database",
                "4" => "Bootstrap symfony files",
                "5" => "Initialize frontend",
                "6" => "Last steps",
                "e" => "Exit bootstrapping."
            ], "e");

            switch ($option) {
                case "1":
                    $this->initializeComposerPackages();
                    break;
                case "2":
                    $this->configurationMenu();
                    break;
                case "3":
                    $this->initializeDatabase();
                    break;
                case "4":
                    $this->bootstrapSymfony();
                    break;
                case "5":
                    $this->initializeFrontend();
                    break;
                case "6":
                    $this->write("1. Bind toolbox routes in routes.yaml (resource: '@ToolboxBundle/Controller/'");
                    $this->write("2. Change essentials path in webconfig");
                    $this->write("3. Change essentials path in tsconfig");
                    break;
                case "e":
                    $running = false;
                    break;
            }
        }

        return Command::SUCCESS;
    }

    private function initializeComposerPackages() {
        $this->bash("composer req " . implode(" ", [
            "doctrine/annotations",
            "nyholm/psr7",
            "symfony/uid",
            "symfony/psr-http-message-bridge",
            "league/oauth2-server",
            "mpdf/mpdf",
            "mpdf/qrcode",
            "symfony/http-client",
            "symfony/orm-pack:2.2.0",
        ]) . " --with-all-dependencies");

        $this->bash("composer req  --dev " . implode(" ", [
            "symfony/maker-bundle"
        ]));
    }

    private function configurationMenu() {
        $running = true;
        while ($running) {
            $this->write("********************");
            $this->write("Here you can configure your webapp.");

            $option = $this->IO()->choice("----- OPTIONS", [
                "1" => "Reset config files",
                "2" => "Generate secret keys",
                "3" => "Set database password",
                "4" => "Initialize config files",
                "b" => "Back to main menu"
            ], "b");

            switch ($option) {
                case "1":
                    $this->bootstrap("config/packages/doctrine.yaml.init");
                    $this->bootstrap(".env.init");
                    $this->bootstrap(".env.local.init");
                    $this->success();
                    break;

                case "2":
                    $env = $this->IO()->choice("Choose your environment", [
                        "dev", "prod"
                    ], "dev");

                    $this->bash("rm -rf config/secrets/{$env}/*");
                    $this->bash("APP_RUNTIME_ENV={$env} php bin/console secrets:generate-keys");
                    $this->success();
                    break;

                case "3":
                    $env = $this->IO()->choice("Choose your environment", [
                        "dev", "prod"
                    ], "dev");

                    $this->bash("APP_RUNTIME_ENV={$env} php bin/console secrets:set DB_PASSWORD");
                    $this->success();
                    break;

                case "4":
                    $this->bootstrap("config/packages/doctrine.yaml");
                    $this->bootstrap(".gitignore_template");
                    $this->bootstrap(".env");
                    $this->bootstrap(".env.local");
                    $this->success();
                    break;

                case "b":
                    $running = false;
                    break;
            }
        }
    }

    private function initializeDatabase() {
        $nameSet = $this->IO()->confirm("Have you set the name of the database in .env.local?");
        if (!$nameSet) return;

        $drop = $this->IO()->confirm("Do you want to drop the existing database?");
        if ($drop) {
            $this->bash("php bin/console doctrine:database:drop --force");
        }

        $this->bash("php bin/console doctrine:database:create");
        $this->bootstrap("src/Entity/User.php");
        $this->bootstrap("src/Entity/Privilege.php");
        $this->bootstrap("src/Entity/Role.php");
        $this->bootstrap("src/Controller/Api/AuthController.php");
        $this->bash("php bin/console make:migration");
        $this->bash("php bin/console doctrine:migrations:migrate");
        $this->success();
    }

    private function bootstrapSymfony() {
        $this->bash("rm -rf scripts");
        $this->bootstrap("config/packages/monolog.yaml");
        $this->bootstrap("config/packages/web_profiler.yaml");
        $this->bash("mkdir scripts");
        $this->bootstrap("scripts/migrate-diff.sh");
        $this->bootstrap("scripts/build.sh");
        $this->bootstrap("scripts/setup-composer-prod.js");
        $this->bootstrap("scripts/add-page.sh");
        $this->success();
    }

    private function initializeFrontend() {
        $running = true;
        while ($running) {
            $this->write("********************");
            $this->write("Here you can configure your frontend.");

            $option = $this->IO()->choice("----- OPTIONS", [
                "1" => "Install webpack",
                "2" => "Install symfony packages",
                "3" => "Install packages for essentials",
                "4" => "Rearrange folders",
                "5" => "Bootstrap frontend configuration",
                "6" => "Bootstrap react app",
                "b" => "Back to main menu"
            ], "b");

            switch ($option) {
                case "1":
                    $this->bash("composer req symfony/webpack-encore-bundle");
                    $this->bash("npm i");
                    $this->success();
                    break;

                case "2":
                    $this->bash("npm i -D " . implode(" ", [
                        "sass-loader",
                        "sass",
                        "@babel/preset-react@^7.0.0",
                        "typescript",
                        "ts-loader@^9.0.0"
                    ]));
                    $this->success();
                    break;

                case "3":
                    $this->bash("npm i -D " . implode(" ", [
                        "@mui/material",
                        "@emotion/react",
                        "@emotion/styled",
                        "@tanstack/react-query",
                        "@stitches/react",
                        "@types/react",
                        "@types/react-dom",
                        "axios",
                        "clsx",
                        "color",
                        "dotenv",
                        "html-react-parser",
                        "immer",
                        "path-to-regexp",
                        "react",
                        "react-dom",
                        "react-error-boundary",
                        "react-hook-form",
                        "react-icons",
                        "react-pdf",
                        "react-redux",
                        "react-router-dom",
                        "use-immer"
                    ]));
                    $this->success();
                    break;

                case "4":
                    $this->bash("rm -r assets");
                    $this->bash("mkdir frontend");
                    $this->success();
                    break;

                case "5":
                    $this->bash("rm -f templates/base.html.twig");
                    $this->bootstrap("tsconfig.json");
                    $this->bootstrap("webpack.config.js");
                    $this->bootstrap(".eslintrc_template.json");
                    $this->bootstrap("templates/app.html.twig");
                    $this->success();
                    break;

                case "6":
                    $this->bootstrap("frontend/*");
                    $this->success();
                    break;

                case "b":
                    $running = false;
                    break;
            }
        }
    }

}