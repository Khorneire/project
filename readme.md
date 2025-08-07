# Foobar

Foobar is a Python library for dealing with word pluralization.

## Installation

Use the package manager [pip](https://pip.pypa.io/en/stable/) to install foobar.

```bash
pip install foobar
```

## Usage

```python
import foobar

# returns 'words'
foobar.pluralize('word')

# returns 'geese'
foobar.pluralize('goose')

# returns 'phenomenon'
foobar.singularize('phenomena')
```

## Contributing

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

Please make sure to update tests as appropriate.


## Project Overview
This project is built using the following technologies:

- Laravel 11 Framework
- PHP 8.3
- Vue 3

The project is designed to be mostly standalone and uses Docker for environment setup and builds.

Prerequisites
To get started, you need to have Docker installed on your system. You can either:

- Install Docker Desktop (recommended for Windows and Mac), or

- Use Docker CLI on Linux or WSL2 (Windows Subsystem for Linux).

Installing Docker on Linux / WSL2
If you prefer to run Docker without Docker Desktop, follow these steps to install Docker Engine and Docker Compose CLI plugin:

1. Install Docker Engine (No GUI)
For Ubuntu/Debian systems, run:
```
sudo apt update
sudo apt install -y docker.io
```

2. Install Docker Compose CLI Plugin
```
mkdir -p ~/.docker/cli-plugins
curl -SL https://github.com/docker/compose/releases/download/v2.24.6/docker-compose-linux-x86_64 -o ~/.docker/cli-plugins/docker-compose
chmod +x ~/.docker/cli-plugins/docker-compose
```

3. Verify Installation
Check the installed version:
```
docker compose version
```

4. Running the Project

```
docker compose build --no-cache
```

If you need any help or run into issues, feel free to reach out!
