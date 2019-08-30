deskpro
=======

A Symfony project created on August 30, 2019.

Usage:
Main page: / e.g. http://deskpro.local/
Users page: /users e.g. http://deskpro.local/users
Tickets page: /tickets e.g.http://deskpro.local/tickets
Organizations page: /organizations e.g. http://deskpro.local/organizations

Controllers: src/AppBundle/Controllers
Service: src/AppBundle/Service
Forms: src/AppBundle/Form
Views: app/Resources/views/default

Custom parameters in config.yaml:
  api_base_uri: "https://site40008.deskprodemo.com"
  api_key: "key 1:dev-admin-code"
  api_users_endpoint: "/api/v2/people/"
  api_tickets_endpoint: "/api/v2/tickets/"
  api_organizations_endpoint: "/api/v2/organizations/"