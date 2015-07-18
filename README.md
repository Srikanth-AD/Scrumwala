### Scrumwala
Your very own Scrum/Agile web app built with Laravel

### Features
* Create and manage projects with plan and work views
* Group issues in a project into sprints
* Set deadlines for issues, active sprints and projects
* Get reminders via email listing issues nearing deadline
* Responsive UI, *thanks to Bootstrap*

### Screenshots

*Project: Plan View*
![alt tag](https://raw.githubusercontent.com/modestkdr/Scrumwala/master/screenshots/project-plan-view.png)


*Project: Work View*
![alt tag](https://raw.githubusercontent.com/modestkdr/Scrumwala/master/screenshots/project-show-view.png)

### Install Instructions
To install Scrumwala you can clone the repository:

```
$ git clone https://github.com/modestkdr/scrumwala.git.
```


Next, enter the project's root directory and install the project dependencies:

```
$ composer install
```

Next, configure your .env file (root directory) and database (config/database.php). Subsequently, create the database and then run the migrations:

```
$ php artisan migrate
```

### License
Scrumwala is licensed under the MIT license. If you find something wrong with the code or think it could be improved, I welcome you to create an <a href="https://github.com/modestkdr/scrumwala/issues">issue</a> or submit a pull request!
