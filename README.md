# Yii2 CRUD
This extension gives you the ability to easily add CRUD (Create Read Update Delete) functionality into your Yii project, which works out of the box with useful features and can be customized.

## Installation
The extension can be installed via Composer.

### Adding the repository
Add this repository in your composer.json file, like this:
```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/H3Tech/yii2-crud"
    }
],
```
### Adding dependency
Add an entry for the extension in the require section in your composer.json:
```
"h3tech/yii2-crud": "~16.0"
```
After this, you can execute `composer update` in your project directory to install the extension.

### Enabling the extension
The extension must be enabled in Yii's web.php by adding an entry for it in the modules section, for example:
```
'modules' => [
    'h3tech-crud' => [
        'class' => 'h3tech\crud\Module',
    ],
],
```
Then it must be added to the list of bootstrapped items, for example:
```
'bootstrap' => ['log', 'h3tech-crud'],
```

### The media table
In order for the file upload functionality of the extension to work, a media table named must be created. To do this, run the migrations of the extension:
```
./yii migrate/up --migrationPath=@vendor/h3tech/yii2-crud/migrations
```

### Enabling the CRUD generator
If you would like to use the built-in generator for starting up a CRUD quickly, make sure you have something like this at the end of your web.php file:
```
$config['bootstrap'][] = 'gii';
$config['modules']['gii'] = [
    'class' => 'yii\gii\Module',
    'generators' => [
        'h3tech' => [
            'class' => 'h3tech\crud\generators\crud\Generator',
        ],
    ],
];
```
Then you can access the generator in Gii.
