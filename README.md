# Teamtools PHP SDK
- List of all [SDK methods](functions.md)

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

#### Retrieving access token
If access token is needed for making additional requests to TeamTools API, it can be retrieved after registering client, like this:
```sh
$accessToken = TeamToolsClient::getInstance()->getAccessToken();
```

### Customer entity

#### Customer namespace

To work with customers, include the following namespace:
```sh
use teamtools\Entities\Customer;
use teamtools\Exceptions\TTException;   // this will be needed for error-handling
```

#### Create customer

```sh
$data = [
    'name'    => 'Awesome customer',
    'email'   => 'customer@awesome.com',
    'phone'   => '+1234567890',
    'city'    => 'Berlin',
    'country' => 'Germany'
];

$customer = new Customer($data);

try {
    var_dump($customer->save(true));
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Update customer
Updating entity flow is: instantiate object from database, set its attributes to desired value and save back to database. Entity can be retrieved by `id`, or `tag` and search in which case a collection of objects will be returned. Ways of retrieving entities are described in next section (Get customers).

```sh
$customer = Customer::getByID('580a0ffcbffebc8b0e8b456a');
$customer->name = 'New Customer Name';

try {
    var_dump($customer->save());
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Get customer
Single team object can be retrived by its `id`.
```sh
try {
    $customer = Customer::getByID('580a0ffcbffebc8b0e8b456a');
    var_dump($customer);
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

It's also possible to retrieve entities by tag, in which case a collection of entities will be returned. 
```sh
try {
    $customers = $customer = Customer::getByTag('new');
    var_dump($customers);
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

Finally, entities can be searched by keyword using static method `getAll` which is provided in all entities.
Also returns collection of entities.
```sh
// all customers
$customers = Customer::getAll();

foreach ($customers as $customer) {
    var_dump($customer->name);
}

// search customers for 'awesome' in searchable attributes
$customers = Customer::getAll(['keyword' => 'awesome']);

foreach ($customers as $customer) {
    var_dump($customer->name);
}
```

#### Delete customer
Deleting customer is done by instantiating it from database and calling its `delete` method. Data is being soft-deleted.
```sh
try {
    $customer = Customer::getByID('580a0ffcbffebc8b0e8b456aff');
    $customer->delete();
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Migrate endusers to another customer
Deleting customer is done by instantiating it from database and calling its `delete` method. Data is being soft-deleted.
```sh
try {
    $customer = Customer::getByID('57e3a147bffebc75388b4571');
    $newCustomerId = '57ecf1f6bffebcc5098b4585';

    //migrate specific endusers
    $ids = ['57ecf1b0bffebcc3098b4582', '57ecf1b0bffebcc3098b4587'];

    var_dump($customer->migrateEndusers($newCustomerId, $ids));
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

```sh
try {
    $customer = Customer::getByID('57e3a147bffebc75388b4571');
    $newCustomerId = '57ecf1f6bffebcc5098b4585';

    //migrate all endusers

    var_dump($customer->migrateEndusers($newCustomerId));
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

### Attributes

Attributes are properties that can be defined and attached to entity. There is a set of predefined attributes 
for each entity and arbitrary number of additional user-defined attributes may be created.

#### Get customer attributes

Retrieve a list of attributes for an entity. Contains default and custom attributes, as well as description
of relationships with other entities.

```sh
Customer::getAttributes();
```

#### Create or update customer attribute

Custom attributes can be defined by instantiating `Attribute` object with corresponding properties and calling
`saveAttribute` method on corresponding entity, as shown below. New custom attribute `department` will be created and
attached to `Customer`. This attribute will be included in validations when working with `Customer` entity. For example:
after creating this attribute, it won't be possible to create `Customer` entity if `department` is not provided, since
`department` is required custom attribute (`'required' => true`).

```sh
$data = [
    'name'         => 'department',
    'prettyName'   => 'Department',
    'type'         => 'text',
    'description'  => "Customer's department",
    'required'     => true,
    'editable'     => true,
    'searchable'   => true,
    'default'      => false,
    'defaultValue' => ''
];

$attribute = new Attribute($data);

try {
    Customer::saveAttribute($attribute);
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Delete customer attribute

Attribute can be deleted by supplying its `id` to static method `deleteAttribute` of corresponding entity. Attributes are soft-deleted, 
ie. record is marked as deleted and not physically removed from database.

```sh
try {
    Team::deleteAttribute('56571718095747cc4b9215f4');
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

### TeamTools SDK response formats
Every interaction with TeamTools SDK will return response, even delete operation returns deleted resource. 
There are two types of responses: PHP object and raw JSON response as API returns it. Default format is 
PHP object (Entity or ArrayIterator when collections are returned) and raw response can be received by:

* supplying `true` to instance methods
  * Update team and return raw response
    ```sh
    try {
        $team       = Team::getByID('56582c53095747b64b9215f7');
        $team->name = 'SDK awesome team';

        $response   = $team->save(true);
    } catch (TTException $ex) {
        echo $ex->getMessage();
    }
    ```
  * Update team and return `teamtools\Entities\Team` object
    ```sh
    try {
        $team       = Team::getByID('56582c53095747b64b9215f7');
        $team->name = 'SDK awesome team';

        $response   = $team->save();
    } catch (TTException $ex) {
        echo $ex->getMessage();
    }
    ```

* calling methods with suffix `Raw` when working with static methods
  * `$team = Team::getByID('565719f3095747906a9215f5');` - returns `teamtools\Entities\Team` object
  * `$team = Team::getByIDRaw('565719f3095747906a9215f5');` - returns raw JSON response

##### Methods with corresponding "raw response" methods:

Object          | Raw
----------------|------------------
`getByID`       | `getByIDRaw`
`getByTag`      | `getByTagRaw`
`getAll`        | `getAllRaw`
`getAttributes` | `getAttributesRaw`
                |

### Embedding related objects in response

Normally, related objects will be represented with ID in response. For example, customer will contain subscription ID in response which can be used to fetch subscription object. It is possible to embed related object directly into response and save additional server request. 

Example embedding subscription object into customer response:
```
try {
    $customer = Customer::getByID('5739c7fbbffebc4c0b8b4567', 'subscription');
    var_dump($customer);
} catch (TTException $ex) {
    echo $ex->getMessage();
}

```

Related data can be nested, and more than one relation can be included. Parameter format should be string with comma separated relations to embed.

Examples for include parameter are:

- embed single relation:
  - include=events
- embed multiple relations:
  - include=events,customer
- nesting data:
  - include=events,customer.invoices

Some example of related object manipulations (not wrapped in try - catch blocks for the sake of clarity):
```
// retrieve all features from package that customer is currently subscribed to:
$customer = Customer::getByID('5739c7fbbffebc4c0b8b4567', 'subscription.package.features');
var_dump($customer->subscription->package->features);

// change property of related object
$customer = Customer::getByID('5739c7fbbffebc4c0b8b4567', 'subscription.package');
$package = $customer->subscription->package;
$package->name = 'Pro plan changed name';

var_dump($package->save());

// include more than one relation and response examples
$customer = Customer::getByID('5739c7fbbffebc4c0b8b4567', 'subscription.package,users');
var_dump($customer);
var_dump($customer->subscription);
var_dump($customer->subscription->package);
var_dump($customer->users);

// search and include
$customers = Customer::getAll([
    'filter'  => 'country{ct}jamaica',
    'include' => 'subscription.package'
]);

var_dump($customers);

```

## Features

#### Feature namespace
To work with features, include the following namespace:
```sh
use teamtools\Entities\Feature;
use teamtools\Exceptions\TTException;   // this will be needed for error-handling
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
    'name'         => 'status',
    'prettyName'   => 'Feature status',
    'type'         => 'number',
    'description'  => 'Status of feature',
    'required'     => true,
    'editable'     => true,
    'searchable'   => true,
    'default'      => false,
    'defaultValue' => ''
];

try {
    $attribute = new Attribute($data);
    Feature::saveAttribute($attribute);
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Update feature attributes

```sh
use teamtools\Entities\Feature;
use teamtools\Entities\Attribute;

$data = [
    'id'           => '5656c899bffebc47078b456e',
    'name'         => 'status',
    'prettyName'   => 'Feature status',
    'type'         => 'number',
    'description'  => 'Status of feature. 1 - active; 0 - inactive.',
    'required'     => true,
    'editable'     => true,
    'searchable'   => true,
    'default'      => false,
    'defaultValue' => ''
];

try {
    $attribute = new Attribute($data);
    Feature::saveAttribute($attribute);
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Delete feature attribute

```sh
try {
    Feature::deleteAttribute('5656c899bffebc47078b456e');
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```
#### Get feature by ID
```sh
try {
    $feature = Feature::getByID('5655c5f6bffebc40078b459e');
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Get feature by tag
```sh
try {
    $feature = Feature::getByTag('master');
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Create feature
```sh
$data = [
    'name'        => 'Feature B',
    'description' => 'Feature B',
    'uniqueKey'   => 'feat-B'
];

try {
    $feature = new Feature($data);
    $feature->save();
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Update feature
```sh
$feature              = Feature::getByID('5655c5f6bffebc40078b459e');
$feature->description = 'Feature B - extended trial period.';

try {
    $feature->save();
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Delete feature

```sh
try {
    $feature = Feature::getByID('5655d765bffebc3f078b4595');
    $feature->delete();
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Relation examples

##### Update feature dependencies
```sh
try {
    $feature                 = Feature::getByID('5655c5edbffebc40078b459c');
    $feature->dependency_ids = ['5655c5f6bffebc40078b459e'];
    $feature->save();
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

##### Remove feature dependencies
```sh
try {
    $feature                 = Feature::getByID('5655c5edbffebc40078b459c');
    $feature->dependency_ids = [];
    $feature->save();
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

### Groups, Packages and Plans
#### Groups, Packages and Plans namespaces:
To work with groups, packages or plans, include respective namespace:
```sh
use teamtools\Entities\Group;
use teamtools\Entities\Package;
use teamtools\Entities\Plan;
```

#### Step 1: Create Group
Groups have following parameters:
- name - a user-friendly label for the Group that’ll be seen by you in your dashboard, and possibly by your customers.
- default - should be set as true or false, depending if you want to set Group as default
- defaultPackageId - each Group should contain at least one Package. If Group is having more than one Package, you should determine, which Package is getting defaultPackageId.

Code example: 
```
$data = [
    'name'    => 'Standard group',
    'default' => true   //if ommited and this is first group in the system, group will become default
];

$group = new Group($data);

try {
    var_dump($group->save());
} catch (TTException $ex) {
    echo $ex->getMessage();
}
``` 

Example with default package and assign as default group:

```
$data = [
    'name'           => 'Basic group',
    'defaultPackage' => '5733052dbffebc46088b456b',
    'default'        => true
];

$group = new Group($data);

try {
    var_dump($group->save());
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

Retrieve packages by group ID:
```
try {
    $group = Group::getByID('573301dbbffebc46088b4567');
    var_dump($group->getPackages());
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Step 2: Create Packages
Packages have following parameters:
- name - a user-friendly label for the package that’ll be seen by you in your dashboard, and possibly by your customers.
- description - additional description of Package for providing more information.
- default - Indicates if Package is default for the group.
- groupId - represents Package group. 
- featureIds - represents Feature(s) you want to assign to Package.

Code example:
```
$data = [
    'name'    => 'Basic package',
    'groupId' => '573301dbbffebc46088b4567',
    'default' => 'true'     // assign this package as default in its group
];

$package = new Package($data);

try {
    var_dump($package->save());
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

Example creating package with features:
```
$data = [
    'name'       => 'Pro package',
    'featureIds' => [
        '573305cdbffebc46088b4571',
        '573305e5bffebc46088b4575'
    ],
    'groupId'    => '573301dbbffebc46088b4567'
];

$package = new Package($data);

try {
    var_dump($package->save());
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

Upon creation of Package, you will get packageId from teamtools.
Each Package requires a unique ID. You’ll provide this value in API requests to subscribe a customer to one of your Packages.

> **Default Package**

> In case there is only one Package created in the Group, this Package will get defaultPackageId.
> If there are two or more Packages created in the Group, you'll need to decide which Package gets to be default.


#### Step 3: Create Plan

Plan includes following parameters:
- name - a user-friendly label for the Plan which will be inherited from Package name 
- description - additional description of Plan for providing more information, which will be inherited from Package name 
- trial - number of days available for trial, used in case Trial is offered by your service.
- initialFee - amount, used in case you want to charge specific fee for service setup.
- pricing - described below

Example creating unit plan:
```
$data = [
    'packageId' => '57330639bffebc46088b4579',
    'trial'     => '30',
    'currency'  => 'USD',
    'pricing'   => [
        'type' => 'unit',
        'interval' => [
            'type' => 'month',
            'amount' => 2
        ],
        'unit'   => 'enduser',
        'amount' =>  200    //amount in cents
    ]
];

$plan = new Plan($data);

try {
    var_dump($plan->save());
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```
Example creating flat plan:
```
$data = [
    'name'      => 'Enterprise',
    'packageId' => '57330639bffebc46088b4579',
    'trial' => '30',
    'currency'  => 'USD',
    'pricing'   => [
        'type' => 'flat',
        'interval' => [
            'type'   => 'month',
            'amount' => 2
        ],
        'amount' =>  1500
    ]
];
```

Example creating tier plan: 
```
$data = [
    'packageId' => '57330639bffebc46088b4579',
    'trial'     => '30',
    'currency'  => 'USD',
    'pricing'   => [
        'type' => 'tier',
        'interval' => [
            'type' => 'month',
            'amount' => 2
        ],
        'unit' => 'enduser',
        'levels' => [
            [
                'condition' => [
                    'min' => 1,
                    'max' => -1
                ],
                'expression' => [
                    [
                        'type'   => 'unit',
                        'unit'   => 'enduser',
                        'amount' => 120     //amount in cents
                    ]
                ]
            ]
        ]
    ]
];

$plan = new Plan($data);

try {
    var_dump($plan->save());
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

Get plan by ID:
```sh
try {
    $plan = Plan::getByID('5673eff3bffebc4e078b4569');
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Start subscribing Customer to a Package

There are two ways to create customer subscription: via customer create / update request and through dedicated endpoint. 

Create subscription using customer update request (returns `customer` in response):
```
try {
    $customer = Customer::getByID('5730838fbffebc290b8b4591');
    $customer->groupId = 'default';

    var_dump($customer->save());
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

Create subscription using dedicated endpoint (default group). Returns `subscription` in response:
```
try {
    $customer = Customer::getByID('5730838fbffebc290b8b4591');

    $subscriptionData = [
        'groupId' => 'default'
    ];

    var_dump($customer->subscribe($subscriptionData));
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

Create subscription using dedicated endpoint:
```
try {
    $customer = Customer::getByID('5730838fbffebc290b8b4591');

    $subscriptionData = [
        'groupId'     => '573301dbbffebc46088b4567',
        'packageId'   => '5733052dbffebc46088b456b',
        'manual'      => 'false',
        'stripeToken' => 'xxxx'
    ];

    var_dump($customer->subscribe($subscriptionData));
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

Retrieve customer's subscription
```
try {
    $customer = Customer::getByID('5730838fbffebc290b8b4591');
    var_dump($customer->getSubscription());
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Unsubscribe customer from package

By calling following SDK function customer will be unsubscribed from current package. If subscription exists on payment gateway, it will also be cancelled.
Return value: `subscription` object.

```
try {
    $customer = Customer::getByID('56c73ce5bffebc47078b4619');
    var_dump($customer->unsubscribe());
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Add invoice item
Invoice item can be added and picked up by next invoice generation. If subscription exist on payment gateway, invoice item will be created on gateway. Otherwise it's created in TeamTools database.

```
use teamtools\Entities\Subscription;

$data = [
    'description' => 'This item will appear on next invoice',
    'currency'    => 'usd',
    'amount'      => 1800
];

try {
    $subscription = Subscription::getByID('56cc46f2bffebc5b078b4571');
    $subscription->addInvoiceItem($data);
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

## Invoices

#### Get invoice by ID

```
use teamtools\Entities\Invoice;

try {
    $invoice = Invoice::getByID('56cc581abffebc5b078b4575');
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Create invoice

```
use teamtools\Entities\Invoice;

$data = [
    'invoiceDate' => '2016-01-22',
    'dueDate'     => '2016-02-22',
    'customerId'  => '56c73ce5bffebc47078b4619',
    'items'       => [
        [
            'description' => 'Initial account setup',
            'amount'      => 1750
        ],
        [
            'description' => 'Application adjustments',
            'amount'      => 450000
        ]
    ]
];

$invoice = new Invoice($data);

try {
    $invoice->save();
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Settle invoice

Used to manually mark invoice as settled.

```
use teamtools\Entities\Invoice;

try{
    $invoice = Invoice::getByID('56cc581abffebc5b078b4575');
    $invoice->settle();
    $invoice->save();
} catch (TTException $ex) {
    echo $ex->getMessage();
}

```

#### Apply payment on invoice

Manually apply payment to invoice total. If applied amount is equal to invoice total, invoice will be marked as paid. If amount is larger than open invoice amount, invoice will be closed and remaining amount added to balance on subscription object.

```
use teamtools\Entities\Invoice;

try {
    $invoice = Invoice::getByID('56cc581abffebc5b078b4575');
    $invoice->applyPayment(['amount' => 350]);
    $invoice->save();
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

#### Bulk insert and update

Customers and Endusers support bulk operations like inserting and updating multiple records in single request. For this operation a structure named "data" should be provided, which contains array of entities to be persisted. If array element contains element "id", entity with that specific ID will be updated. Otherwise, entity will be inserted. All validations are still valid, like when working with single entity.

Customers example:

```
use teamtools\Entities\Customer;

$customers = [
    [
        'id'      => '5704f67cbffebc47078b4574',
        'name'    => 'My Customer XXY',
        'email'   => 'customerCHANGE@email.com',
        'phone'   => '+1234123412',
        'country' => 'USA',
        'city'    => 'Chicago',
    ],
    [
        'name'    => 'My Customer YXY',
        'email'   => 'customer@email.com',
        'phone'   => '+1234123412',
        'country' => 'USA',
        'city'    => 'Chicago'
    ]
];

try {
    var_dump(Customer::saveAll($customers, false));
} catch (TTException $ex) {
    echo $ex->getMessage();
}

```

Endusers example:
```
use teamtools\Entities\EndUser;

$endusers = [
    [
        'firstName' => 'Mary',
        'lastName'  => 'Jones',
        'email'     => 'customerCHANGE@email.com',
        'phone'     => '+1234123412',
        'country'   => 'USA',
        'city'      => 'Chicago'
    ],
    [
        'firstName' => 'Peter',
        'lastName'  => 'Johnson',
        'email'     => 'customer@email.com',
        'phone'     => '+1234123412',
        'country'   => 'USA',
        'city'      => 'Chicago'
    ],
    [
        'id'        => '57050433bffebc46078b457f',
        'firstName' => 'John',
        'lastName'  => 'Smith',
        'email'     => 'customer@email.com',
        'phone'     => '+1234123412',
        'country'   => 'USA',
        'city'      => 'Chicago'
    ]
];

try {
    var_dump(EndUser::saveAll($endusers, false));
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

### Retrieve webhook event
```
try {
    $webEvent = WebEvent::getByID('57334232bffebc77088b4574');
    var_dump($webEvent);
} catch (TTException $ex) {
    echo $ex->getMessage();
}
```

### Webhook event format examples

Customer name updated:
```
{
  "data": {
    "id": "5729eb7bbffebc48088b456e",
    "timestamp": "2016-05-04 12:30:51",
    "source": "UI",
    "memberId": "5729c6ebbffebc47088b458a",
    "url": null,
    "action": "updated",
    "data": {
      "type": "customer",
      "value": {
        "id": "5729e7c8bffebc47088b458b",
        "name": "ShiftPlanning 2",
        "email": "sp@shiftplanning.com",
        "country": "USA",
        "phone": "123",
        "city": "Belgrade",
        "joinDate": {
          "date": "2016-05-01 00:00:00.000000",
          "timezone_type": 3,
          "timezone": "UTC"
        },
        "updated_at": {
          "date": "2016-05-04 12:30:51.000000",
          "timezone_type": 3,
          "timezone": "UTC"
        },
        "created_at": {
          "date": "2016-05-04 12:15:04.000000",
          "timezone_type": 3,
          "timezone": "UTC"
        },
        "tags": [],
        "notes": []
      },
      "old": {
        "id": "5729e7c8bffebc47088b458b",
        "name": "ShiftPlanning",
        "email": "sp@shiftplanning.com",
        "country": "USA",
        "phone": "123",
        "city": "Belgrade",
        "joinDate": {
          "date": "2016-05-01 00:00:00.000000",
          "timezone_type": 3,
          "timezone": "UTC"
        },
        "updated_at": {
          "date": "2016-05-04 12:15:04.000000",
          "timezone_type": 3,
          "timezone": "UTC"
        },
        "created_at": {
          "date": "2016-05-04 12:15:04.000000",
          "timezone_type": 3,
          "timezone": "UTC"
        },
        "tags": [],
        "notes": []
      }
    },
    "status": "failed",
    "updated_at": {
      "date": "2016-05-04 12:30:51.000000",
      "timezone_type": 3,
      "timezone": "UTC"
    },
    "created_at": {
      "date": "2016-05-04 12:30:51.000000",
      "timezone_type": 3,
      "timezone": "UTC"
    }
  }
}
```

Group created:
```
{
  "data": {
    "id": "57332e6dbffebc78088b4573",
    "timestamp": "2016-05-11 13:06:53",
    "source": "API",
    "memberId": null,
    "url": null,
    "action": "created",
    "data": {
      "type": "group",
      "value": {
        "id": "57332e6dbffebc78088b4571",
        "default": true,
        "updated_at": {
          "date": "2016-05-11 13:06:53.000000",
          "timezone_type": 3,
          "timezone": "UTC"
        },
        "created_at": {
          "date": "2016-05-11 13:06:53.000000",
          "timezone_type": 3,
          "timezone": "UTC"
        },
        "tags": [],
        "defaultPackageId": null
      },
      "old": null
    },
    "status": "failed",
    "updated_at": {
      "date": "2016-05-11 13:06:53.000000",
      "timezone_type": 3,
      "timezone": "UTC"
    },
    "created_at": {
      "date": "2016-05-11 13:06:53.000000",
      "timezone_type": 3,
      "timezone": "UTC"
    }
  }
}
```

Package deleted:
```
{
  "data": {
    "id": "57334232bffebc77088b4574",
    "timestamp": "2016-05-11 14:31:14",
    "source": "API",
    "memberId": null,
    "url": null,
    "action": "deleted",
    "data": {
      "type": "package",
      "value": {
        "id": "5733052dbffebc46088b456b",
        "name": "Basic package",
        "default": false,
        "updated_at": {
          "date": "2016-05-11 10:10:53.000000",
          "timezone_type": 3,
          "timezone": "UTC"
        },
        "created_at": {
          "date": "2016-05-11 10:10:53.000000",
          "timezone_type": 3,
          "timezone": "UTC"
        },
        "groupId": "573301dbbffebc46088b4567",
        "deleted_at": "2016-05-11 14:31:14",
        "tags": [],
        "featureIds": []
      },
      "old": null
    },
    "status": "failed",
    "updated_at": {
      "date": "2016-05-11 14:31:14.000000",
      "timezone_type": 3,
      "timezone": "UTC"
    },
    "created_at": {
      "date": "2016-05-11 14:31:14.000000",
      "timezone_type": 3,
      "timezone": "UTC"
    }
  }
}
```

