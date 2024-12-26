# ABNTense

Proof of concept of a plataform for collaborative writing of academic works conforming to the [ABNT](https://abnt.org.br/), which should be able to provide a Notion-like, user-friendly interface, which wouldn't require knowledge about the norms. Formatting to PDF would be automated and provided by using [Typst](https://typst.app) on the server.

The project is still in its infancy and is not anywhere near production-ready. 

## Usage

Run migrations and seed the database:

```shell
php artisan migrate --seed
```

Run the dev server:

```shell
php artisan serve
```

Run tests:

```shell
php artisan test
```
