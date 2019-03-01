# Webauthn + Symfony 4 + Bootstrap 4 Demo

**Live Demo**

You will find this demo on a real server at https://webauthn.spomky-labs.com/

**WORK IN PROGRESS**

This is a simple demo application that can be used as a base for bigger projects.
The application firewall is configured to only accept FIDO2 based authentication (passwordless).

## Prerequisites

You need to install [Docker](https://www.docker.com/) first.

Depending on your OS, you should create a custom host:

    127.0.0.1 client

## Install

    make          # self documented makefile
    make install  # install and start the project

## How to use

Open your browser and go to https://client

*Your browser will warn you about an insecured connection. You can safely trust the certificate.*
