#!/bin/bash
rm -r migrations/*
php bin/console doctrine:migrations:version --delete --all
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate