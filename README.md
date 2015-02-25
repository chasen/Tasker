# TASKER

[![Build Status](https://travis-ci.org/chasen/Tasker.svg)](https://travis-ci.org/chasen/Tasker)

Tasker is a simple application designed to meet a few goals:

- Use API to create, read, and remove tasks.
- Display a GUI to create, read and remove tasks. The GUI must use AJAX submissions.
- The API may not use a database for the storage.

### Installation

Clone the repository to your local computer:

```
git clone http://github.com/chasen/Tasker
```

Move into the new project folder:

```
cd Tasker/
```

Install the vendor libs:

```
composer install
```

Start your web server with the site web root being the web folder. (For php 5.4+)

```
cd web/
php -S localhost:80
```

Then visit the site in your web browser @ http://localhost