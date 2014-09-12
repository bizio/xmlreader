README
======

Since the use of frameworks was no allowed I have created a very 
simple (and incomplete) application framework to serve the purpose of 
manipulating XML data (parse, save to db, retrieve from db).

The application has been designed using SOA principles, the application layer 
(controllers) uses services to handle the user request and the services use 
models and repositories to create, modify, delete or get domain models.

Database schema can be found in data/schema.sql, please change the $config array
in public/index.php if needed.

An apache web server with mod_rewrite enabled is required to run the application, 
the DocumentRoot has to point to the "public" folder.
Set the "APPLICATION_ENV" enviroment variable to "development" for debuging.

Recommended virtual host configuration:

<VirtualHost *:80>
   ServerName xmlreader.local
   DocumentRoot "/var/www/vhosts/xmlreader/public"

   SetEnv APPLICATION_ENV development

   <Directory "/var/www/vhosts/xmlreader/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>
</VirtualHost>

