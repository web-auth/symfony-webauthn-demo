Webauthn Demo
=============

This project is a simple demo of the Webauthn API and a Symfony application that uses it.

To use it, please install [Git](https://git-scm.com/), [Docker](https://www.docker.com/), [Make](https://en.wikipedia.org/wiki/Make_(software)) and run the following commands:

```bash
git clone git@github.com:web-auth/symfony-demo.git
cd symfony-demo
make build
make up
make init
make frontend
```

Then open your browser at https://localhost

Live Demo
---------

This demo is live on https://webauthn.spomky-labs.com/ (or https://spomky-webauthn.herokuapp.com/ if the first link is down).

It runs on a DigitalOcean Droplet. Please follow the steps from [dunglas/symfony-docker documentation](https://github.com/dunglas/symfony-docker/blob/main/docs/production.md).
Then, run the following command:

```bash
RELYING_PARTY_NAME="My application" \
SERVER_NAME=your-domain-name.example.com \
APP_SECRET=ChangeMe \
docker compose -f docker-compose.yml -f docker-compose.prod.yml up --wait
```

Be sure to replace the environment variable values by your actual configuration.
