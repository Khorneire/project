
Project Overview
This project is built using the following technologies:

Laravel 11 Framework
PHP 8.3

The project is designed to be mostly standalone and uses Docker for environment setup and builds.

Prerequisites
To get started, you need to have Docker installed on your system. You can either:

Install Docker Desktop (recommended for Windows and Mac), or

Use Docker CLI on Linux or WSL2 (Windows Subsystem for Linux).

Installing Docker on Linux / WSL2
If you prefer to run Docker without Docker Desktop, follow these steps to install Docker Engine and Docker Compose CLI plugin:

1. Install Docker Engine (No GUI)
For Ubuntu/Debian systems, run:
sudo apt update
sudo apt install -y docker.io

3. Install Docker Compose CLI Plugin
mkdir -p ~/.docker/cli-plugins
curl -SL https://github.com/docker/compose/releases/download/v2.24.6/docker-compose-linux-x86_64 -o ~/.docker/cli-plugins/docker-compose
chmod +x ~/.docker/cli-plugins/docker-compose

4. Verify Installation
Check the installed version:
docker compose version

Running the Project
First-time Build and Start
docker compose build --no-cache && docker compose up --force-recreate

Clean Rebuild (Remove containers, volumes, and networks)
docker compose down -v && docker compose build --no-cache && docker compose up --force-recreate

If you need any help or run into issues, feel free to reach out!
