#!/bin/bash
cp composer.json composer-prod.json
node scripts/setup-composer-prod.js
encore production --progress