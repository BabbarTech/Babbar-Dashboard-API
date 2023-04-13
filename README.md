# Babbar Dashboard API

[![Actions Status](https://github.com/BabbarTech/Babbar-Dashboard-API/workflows/Tests/badge.svg)](https://github.com/BabbarTech/Babbar-Dashboard-API/actions)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHPStan: Level 9](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)](https://github.com/BabbarTech/Babbar-Dashboard-API/actions)

## A SAVOIR AVANT D'INSTALLER L'OUTIL

Le Babbar Dashboard API (BDA) est un outil d'aide à l'audit de sites web qui utilise les APIs babbar.tech et yourtext.guru.
Si vous n'avez aucune des deux APIs l'outil ne fonctionne pas.

Pour en savoir plus sur les deux APIs :

https://www.babbar.tech/settings#/api

https://yourtext.guru/profil/api

## Lancement avec Docker

Utilisateur ou développeur ?

ATTENTION, si vous êtes un "simple" utilisateur, vous devez utiliser le fichier [docker-compose.yml](docker%2Fdocker-compose.yml) qui se trouve dans le repertoire docker de ce repository. Pas celui qui est à la racine !

C'est ce fichier qui est utilisé pour lancer le service avec la commande :

```
docker compose -f ./docker/docker-compose.yml up
```

Par défaut, le service est disponible sur : http://localhost:8080

## Build des images dockers

Image du dashboard :
```
docker build . -t babbar-dashboard-api:latest -f ./docker/prod/Dockerfile
```

Image de trafilatura :
```
docker build . -t babbar-trafilatura:latest -f ./docker/trafilatura/Dockerfile
```

## Installation pour les développeurs

Pour les développeurs, il est possible d'utiliser le fichier docker-compose à la racine comme avec un projet Laravel classique.

Aller à la racine du projet et lancer le script d'installation

`make install`

Une fois le script terminé, l'outil sera accessible ici (adapter le port selon la config APP_PORT paramétré dans le .env) :

`http://localhost:8080/`

Vous serez invité à créer un premier user (Admin).
Puis à renseigner votre `API Token` de Babbar.tech et YourTextGuru

## Quelques commandes utiles

Liste les commandes disponibles

`make help`

Démarrer l'environnement Docker pour faire fonctionner l'outil (en mode daemon).

`make start`

Stopper l'environnement Docker. Attention, les benchmarks en cours de traitements seront stoppés.

`make stop`

Recréer l'environnement Docker et tout supprimer
```
./vendor/bin/sail down -v
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d
```

