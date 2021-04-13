# IberbookEdu Backend
Official backend of IberbookEdu using Leaf PHP, for the official frontend click [here](https://github.com/pablouser1/IberbookEdu-frontend)

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
  * Moderator: They can administrate their group's data.
  * Owner: Can change some options of the instance, like the schools allowed.
* Public yearbooks, ready to be seen by anyone
* Voting system

# Installation
You will need the following programs to make IberBookEdu work:
* PHP 7.3
* Extensions: php-pdo, php-zip
* Database server MySQL 5.7.8 or higher / MariaDB

Once you finished installing everything and you had already copied the project to your server you can modify the .env file with
your values.

You can start the setup on the /setup endpoint

# TODO
* (Owner) Add remove options to frontend

# Credits
This project wouldn't be possible without the help of the following projects:

## PHP Libraries/Frameworks
* LeafPHP (https://github.com/leafsphp/leaf)

## Templates
### Default
* Animista (https://animista.net)
* Confetti.js (https://github.com/mathusummut/confetti.js)
* Spinkit (https://github.com/tobiasahlin/SpinKit)
