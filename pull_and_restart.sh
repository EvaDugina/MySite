#!/bin/bash

set -e # Прерывать выполнение при ошибке

echo "Начинаю обновление git..."
git stash
git fetch
git pull
git stash apply

echo "Начинаю сборку и перезапуск docker..."
docker compose build
docker compose up -d