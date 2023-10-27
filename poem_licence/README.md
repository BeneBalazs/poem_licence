Poem licence
---

## Layering rules

* Only repositories and services inside `Drupal\poem_licence\PoemLicence\Api` can
depend on `Drupal\poem_licence\PoemLicence\Client`.
* Only services inside `Drupal\poem_licence\PoemLicence\Integration` can
depend on repositories and services inside `Drupal\poem_licence\PoemLicence\Api`.
* Every other code inside and outside the `poem_licence` module can only
depend on services inside `Drupal\poem_licence\PoemLicence\Integration` - they also
known as "use cases". If there is no use case for given task, it must be created to keep dependency
layering intact.
