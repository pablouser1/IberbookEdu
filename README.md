# IberbookEdu Backend
Official backend of IberbookEdu using PHP, for the official frontend click [here](https://github.com/pablouser1/IberbookEdu-frontend)

Generate yearbooks easily using info from local database

# Features
* Multilanguage yearbook (english, spanish, french).
* Works both in mobile phones and desktop.
* Generates a ZIP ready to be downloaded by the users.
* Accepts both teachers and students
  * They can add a photo and a video
  * Optionally, they can also add a link and a quote.
* General gallery
* Account administration:
  * Users: They can upload and administrate their own data.
  * Administrators: They can administrate their group's data.
  * Owner: Can change some options of the instance, like the schools allowed.
* Public yearbooks, ready to be seen by anyone
* Voting system

# Installation
You will need the following programs to make IberBookEdu work:
* PHP5.5 (PHP7 recommended)
* Extensions: php-mysqli, php-zip
* Database server MySQL 5.7.8 or higher / MariaDB

Once you finished installing everything and you had already copied the project to your server you can start the installation starting the script named "setup.php"

# TODO
* Rework setup
* Add server name and description input to setup
* Add chunked uploads to gallery items
* (Owner) Remove users
* (Owner) Better UI for adding users
* Maintenance mode
* Refresh token

# Credits
This project wouldn't be possible without the help of the following projects:

## PHP Libraries
* PHPMailer (https://github.com/PHPMailer/PHPMailer)
* PHP-JWT (https://github.com/firebase/php-jwt)
* PHP-i18n (https://github.com/Philipp15b/php-i18n)

## Templates
### Default
* Animista (https://animista.net)
* Confetti.js (https://github.com/mathusummut/confetti.js)
* Spinkit (https://github.com/tobiasahlin/SpinKit)
