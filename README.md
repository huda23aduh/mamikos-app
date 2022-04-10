## About Project

MamiKos is a web app where users can search kost that have been added by the owner. Also, users can ask about room availability using the credit system.

create API to:
- Register as owner / regular user / premium user
- Allow owner to add, update, and delete kost
- Allow owner to see his kost list
- Allow user to search kost that have been added by owner
- Allow user to see kost detail
- Allow user to ask about room availability

## Requirements

- Regular user will be given 20 credit, premium user will be given 40 credit after register. Owner will have no credit.
- Owner can add more than 1 kost
- Search kost by several criteria: name, location, price
- Search kost sorted by: price
- Ask about room availability will reduce user credit by 5 point
- Owner API & ask room availability API need to have authentication
- Implement scheduled command to recharge user credit on every start of the month
- Bonus point if you can create Owner dashboard that use your API
## Task

- Create this project using Laravel PHP Framework
- Consider this as production-ready project, implement industry standard (security, design pattern, code style, unittest, organized commit, etc)
- Create step by step guide of how to install and use your project in normal environment
- Create this project on public code repository & share the link to us

## Setup Project On Your Local Machine

## Prerequisites
1. Web Server (apache / nginx, etc)
2. PHP => 8.0
3. Composer
4. MySQL
5. Git

## Setup Guide
1. Make sure you install all the Prerequisite

2. Clone the project from github to your directory
    https://github.com/huda23aduh/mamikos-app.git

3. Install the dependencies using this command
    composer install

4. Copy the env.example file and rename the new one with .env. Update if needed

5. Setup the database by create new database on you local machine, and run these commands
    php artisan migrate

6. Run the project using this command
    php artisan serve
    
    and the project will be running on http://localhost:8000. Note : (you can adjust / modif port number).

7. If there's a problem with Personal Access Client, run

    php artisan passport:install
