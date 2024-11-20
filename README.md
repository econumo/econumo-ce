<p align="center">
    <picture>
        <img src="https://github.com/econumo/.github/raw/master/profile/econumo.png" width="194">
    </picture>
</p>

<p align="center">
    A getting started guide to self-hosting <a href="https://econumo.com/docs/edition" target="_blank">Econumo One</a>
</p>

---

### Prerequisites

- **[Docker](https://docs.docker.com/engine/install/)** and **[Docker Compose](https://docs.docker.com/compose/install/)** must be installed on your machine.
- At least **256 MB of RAM** is recommended.

### Quick start

1. Clone this repository:

    ```console
    $ git clone --single-branch https://github.com/econumo/econumo-one econumo
    Cloning into 'econumo'...
    remote: Enumerating objects: 13, done.
    remote: Counting objects: 100% (10/10), done.
    remote: Compressing objects: 100% (9/9), done.
    remote: Total 13 (delta 0), reused 7 (delta 0), pack-reused 3 (from 1)
    Receiving objects: 100% (13/13), done.

    $ cd econumo

    $ ls -1
    README.md
    docker-compose.yml
    .env.example
    ```

1. Create and configure your [environment](https://docs.docker.com/compose/environment-variables/) file:

    ```console
    $ cp .env.example .env
    ```

1. Start the services with Docker Compose:

    ```console
    $ docker-compose up -d
    ```

1. Visit your instance at `http://localhost:8080` and create the first user.

> [!NOTE]
> Econumo One is funded by our `GitHub Sponsors` and `Econumo` subscribers.
>
> If you know someone who might [find Econumo useful](https://econumo.com/?utm_medium=Social&utm_source=GitHub&utm_campaign=readme), we'd appreciate if you'd let them know.

### Documentation

For more information on installation, upgrades, configuration, and integrations please see our [documentation.](https://econumo.com/docs/)

### Contact

- For release announcements please go to [GitHub releases.](https://github.com/econumo/econumo-one/releases)
- For a question or advice please use [GitHub discussions](https://github.com/orgs/econumo/discussions)
