# Rent Car Project

This project can help to find cars for rent in different regions, countries.\
Also project supports different languages and currencies.

## Implementation

App directory contains app.php for run project and all controllers with logic. 
Model directory contains models (work with database tables). 
Helper and Service directories help project (for example, database class for work with database). 
Html directory is directory for all templates (html, css, javascript, images).

## Requirements

 - PHP 7
 - php composer
 - php unit tests
 - php mpdf
 - mariadb (or mysql) database
 - SSL (HTTPS)
 - COOKIES

## Running/Installation

- Get project and move files on server.
- Install and/or run composer (for getting dependencies).
- Create database from file db.sql (in root of project).
- Run project in browser from project folder on server.

## Settings

Change some constants in config.php (in root of project). For example, data for database connection, domain name and other.

Change some variables in App/App.php (if needed). For example, firstUriItem variable - by default 0, it means that project is in root of domain (https://domain.com), 1 means that project in folder of domain (https://domain.com/project_folder/). Also can change default country, region, currency.