# Teamtools SDK

#### Installation

TeamTools PHP SDK is on Packagist: https://packagist.org/packages/teamtools/php-sdk

TeamTools PHP SDK can be installed using composer (https://getcomposer.org) by issuing following command:

```sh
composer require teamtools/php-sdk
```
All dependencies will be imported into working directory and you can start using SDK by including appropriate file.
_Example:_ if you create your application directory in same level as `vendor` directory, and your directory structure looks like this: 
```
teamtools-php-sdk
├── app
│   └── index.php
├── vendor
├── composer.json
└── composer.lock
```

than you can start using teamtools SDK by including `autoload.php` in your `app/index.php` file like this:
```sh
require '../vendor/autoload.php';
```

You should not modify contents of `vendor` directory since its contents will be overwritten by composer updates.

#### Registering client

Before calling any method, client must be authenticated to work with API. This is achieved by supplying client credentials to static method `initialize`:

```sh
use teamtools\TeamToolsClient;

TeamToolsClient::initialize([
    'client_id'     => 'your_api_client_id',
    'client_secret' => 'your_api_client_secret',
    'salt'          => 'your_salt'
]);
```

If the authentication is successful, from now on we can make calls to SDK methods. 


### Team entity

#### Team namespace

To work with teams, include the following namespace:
```sh
use teamtools\Entities\Team;
```

Additionally, when working with team attributes, `Attribute` namespace must be included:
```sh
use teamtools\Entities\Attribute;
```

### Attributes

Attributes are properties that can be defined and attached to entity. There is a set of predefined attributes 
for each entity and arbitrary number of additional user-defined attributes may be created.

#### Get team attributes

Retrieve a list of attributes for an entity. Contains default and custom attributes, as well as description
of relationships with other entities.

```sh
Team::getAttributes();
```

#### Create or update team attribute

Custom attributes can be defined by instantiating `Attribute` object with corresponding properties and calling
`saveAttribute` method on corresponding entity, as shown below. New custom attribute `department` will be created and
attached to `Team`. This attribute will be included in validations when working with `Team` entity. For example:
after creating this attribute, it won't be possible to create `Team` entity if `department` is not provided, since
`department` is required custom attribute (`'required' => true`).

```sh
$data = [
    'name' => 'department',
    'prettyName' => 'Department',
    'type' => 'text',
    'description' => "Team's department",
    'required' => true,
    'editable' => true,
    'searchable' => true,
    'default' => false,
    'defaultValue' => ''
];

$attribute = new Attribute($data);
Team::saveAttribute($attribute);
```

#### Delete team attribute

Attribute can be deleted by supplying its `id` to static method `deleteAttribute` of corresponding entity. Attributes are soft-deleted, 
ie. record is marked as deleted and not physically removed from database.

```sh
Team::deleteAttribute('56571718095747cc4b9215f4')
```

#### Create team
Team creation is acomplished by instantiating `Team` object and calling its `save` method. `Team` constructor expects array of properties,
which should include all attributes that are defined on `Team` (this includes default and all custom attributes that may be defined). 
Attributes will be validated and appropriate response returned, which also may contain error message if arguments are missing or
supplied in wrong format.

```sh
$data = [
    'name' => 'Sales team'
];

$team = new Team($data);
$team->save();
```

If `id` is provided, update of existing team will be performed. A simpler way to update entity is shown in next section (Update team).
```sh
$data = [
    'id' => '565719f3095747906a9215f5',
    'name' => 'New sales team'
];

$team = new Team($data);
$team->save();
```

#### Update team
Updating entity flow is: instantiate object from database, set its attributes to desired value and save back to database. Entity can be 
retrieved by `id`, or `tag` and search in which case a collection of objects will be returned. Ways of retrieving entities
are described in next section (Get teams).

```sh
$team = Team::getByID('565719f3095747906a9215f5');
$team->name = 'Aftersales team';

$team->save();
```

#### Get team
Single team object can be retrived by its `id`.
```sh
$team = Team::getByID('565719f3095747906a9215f5');
```

It's also possible to retrieve entities by tag, in which case a collection of entities will be returned. 
```sh
$team = Team::getByTag('new');
```

Finally, entities can be searched by keyword using static method `getAll` which is provided in all entities.
Also returns collection of entities.
```sh
// all teams
$teams = Team::getAll();

foreach ($teams as $team) {
    var_dump($team->name);
}

// search teams for sales in searchable attributes
$teams = Team::getAll(['keyword' => 'sales']);

foreach ($teams as $team) {
    var_dump($team->name);
}
```

#### Delete team
Deleting team is done by instantiating it from database and calling its `delete` method. Data is being soft-deleted.
```sh
$team = Team::getByID('565719f3095747906a9215f5');

$team->delete();
```

### TeamTools SDK response formats
Every interaction with TeamTools SDK will return resoponse, even delete operation returns deleted resource. 
There are two types of responses: PHP object and raw JSON response as API returns it. Default format is 
PHP object (Entity or ArrayIterator when collections are returned) and raw response can be received by:

* supplying `true` to instance methods
  * Update team and return raw response
  	```sh
	$team = Team::getByID('56582c53095747b64b9215f7');
	$team->name = 'SDK awesome team';

	$response = $team->save(true);
	```
  * Update team and return `teamtools\Entities\Team` object
  	```sh
	$team = Team::getByID('56582c53095747b64b9215f7');
	$team->name = 'SDK awesome team';

	$response = $team->save();
	```

* calling methods with suffix `Raw` when working with static methods
  * `$team = Team::getByID('565719f3095747906a9215f5');` - returns `teamtools\Entities\Team` object
  * `$team = Team::getByIDRaw('565719f3095747906a9215f5');` - returns raw JSON response

##### Methods with corresponding "raw response" methods:

Object 		    | Raw
----------------|------------------
`getByID`  	    | `getByIDRaw`
`getByTag` 	    | `getByTagRaw`
`getAll` 	 	| `getAllRaw`
`getAttributes` | `getAttributesRaw`
				|

### Billing package entity

#### Billing package namespace
```sh
use teamtools\Entities\Package;
```

#### Get billing package attributes

```sh
$attributes = Package::getAttributes();
```

#### Create billing package attributes



```sh
$data = [
    'name' => 'calculationBase',
    'prettyName' => 'Calculation Base',
    'type' => 'text',
    'description' => 'Base for calculation',
    'required' => true,
    'editable' => true,
    'searchable' => true,
    'default' => false,
    'defaultValue' => ''
];

$attribute = new Attribute($data);
Package::saveAttribute($attribute);
```

#### Update billing package attributes

```sh
$data = [
    'id' => '5655b89bbffebc40078b4595',
    'name' => 'Calculation Base',
    'prettyName' => 'Calculation Base',
    'type' => 'text',
    'description' => 'Base for calculation changed',
    'required' => true,
    'editable' => true,
    'searchable' => true,
    'default' => false,
    'defaultValue' => ''
];

$attribute = new Attribute($data);
Package::saveAttribute($attribute);
```

#### Delete billing package attribute

```sh
Package::deleteAttribute('5655bc9bbffebc40078b4598');
```

### Billing package entity

#### Creating billing packages

```sh
$data = [
    'name' => 'perUser',
    'description' => 'Billing per user',
    'packageType' => 'custom',
    'trial'       => '30',
];

$package = new Package($data);
$package->save();

```

#### Create billing package with features
```sh
$data = [
    'name' => 'perUser',
    'description' => 'Billing per user',
    'packageType' => 'custom',
    'trial'       => '30',
    'calculationBase' => 'asdf',
    'calculationBase2' => 'asdf',
    'feature_ids' => [
        '5655c5edbffebc40078b459c',
        '5655c5f6bffebc40078b459e'
    ]
];

$package = new Package($data);

$package->save();
```

#### Retrieving billing packages

```sh
$package = Package::getByID('5655b34abffebc3f078b458e');
```

#### Updating billing package

```sh
$package = Package::getByID('5655b34abffebc3f078b458e');

$package->description = 'New package description';
$package->save();
```

#### Deleting billing package

```sh
$package = Package::getByID('5655b34abffebc3f078b458e');
$package->delete();
```

## Features

#### Feature namespace
To work with features, include the following namespace:
```sh
use teamtools\Entities\Feature;
```

#### Get feature attributes

```sh
$attributes = Feature::getAttributes();
```

#### Create feature attribute

```sh
use teamtools\Entities\Feature;
use teamtools\Entities\Attribute;

$data = [
    'name' => 'status',
    'prettyName' => 'Feature status',
    'type' => 'number',
    'description' => 'Status of feature',
    'required' => true,
    'editable' => true,
    'searchable' => true,
    'default' => false,
    'defaultValue' => ''
];

$attribute = new Attribute($data);
Feature::saveAttribute($attribute);
```

#### Update feature attributes

```sh
use teamtools\Entities\Feature;
use teamtools\Entities\Attribute;

$data = [
    'id' => '5656c899bffebc47078b456e',
    'name' => 'status',
    'prettyName' => 'Feature status',
    'type' => 'number',
    'description' => 'Status of feature. 1 - active; 0 - inactive.',
    'required' => true,
    'editable' => true,
    'searchable' => true,
    'default' => false,
    'defaultValue' => ''
];

$attribute = new Attribute($data);
Feature::saveAttribute($attribute);
```

#### Delete feature attribute

```sh
Feature::deleteAttribute('5656c899bffebc47078b456e');
```
#### Get feature by ID
```sh
$feature = Feature::getByID('5655c5f6bffebc40078b459e');
```

#### Get feature by tag
```sh
$feature = Feature::getByTag('master');
```
#### Create feature
```sh
$data = [
    'name' => 'Feature B',
    'description' => 'Feature B',
    'uniqueKey' => 'feat-B'
];

$feature = new Feature($data);
$feature->save();
```

#### Update feature
```sh
$feature = Feature::getByID('5655c5f6bffebc40078b459e');
$feature->description = 'Feature B - extended trial period.';

$feature->save();
```

#### Delete feature

```sh
$feature = Feature::getByID('5655d765bffebc3f078b4595');
$feature->delete();
```


#### Relation examples

##### Update feature dependencies
```sh
$feature = Feature::getByID('5655c5edbffebc40078b459c');
$feature->dependency_ids = ['5655c5f6bffebc40078b459e'];
$feature->save();
```

##### Remove feature dependencies
```sh
$feature = Feature::getByID('5655c5edbffebc40078b459c');
$feature->dependency_ids = [];
$feature->save();
```