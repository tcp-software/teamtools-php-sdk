### List of all SDK functions

#### Customer functions

```
public static function getByID($id, $include = null);
public static function getByIDRaw($id, $include = null);
public static function getByTag($tag, $include = false);
public static function getByTagRaw($tag, $include = false);
public static function getAll($args);
public static function getAllRaw($args);
public static function getAttributes();
public static function getAttributesRaw();
public static function saveAttribute(Attribute $attribute, $raw = false);
public static function deleteAttribute($id, $raw = false);
public function save($raw = false);
public static function saveAll(array $data, $raw = false);
public function delete($raw = false);
public function getEndUsers($raw = false);
public function getEvents($raw = false);
public function subscribe(array $data, $raw = false);
public function unsubscribe($raw = false);
public function getSubscription($raw = false);
public function restore($raw = false);
public function migrateEndusers($newCustomerId, array $ids = [], $raw = false);
```

#### Enduser functions

```
public static function getByID($id, $include = null);
public static function getByIDRaw($id, $include = null);
public static function getByTag($tag, $include = false);
public static function getByTagRaw($tag, $include = false);
public static function getAll($args);
public static function getAllRaw($args);
public static function getAttributes();
public static function getAttributesRaw();
public static function saveAttribute(Attribute $attribute, $raw = false);
public static function deleteAttribute($id, $raw = false);
public function save($raw = false);
public static function saveAll(array $data, $raw = false);
public function getEvents($raw = false);
public function restore($raw = false);

```

#### Enduser functions

```
public static function getByID($id, $include = null);
public static function getByIDRaw($id, $include = null);
public static function getByTag($tag, $include = false);
public static function getByTagRaw($tag, $include = false);
public static function getAll($args);
public static function getAllRaw($args);
public static function getAttributes();
public static function getAttributesRaw();
public static function saveAttribute(Attribute $attribute, $raw = false);
public static function deleteAttribute($id, $raw = false);
public function save($raw = false);

```

#### Feature functions

```
public static function getByID($id, $include = null);
public static function getByIDRaw($id, $include = null);
public static function getByTag($tag, $include = false);
public static function getByTagRaw($tag, $include = false);
public static function getAll($args);
public static function getAllRaw($args);
public static function getAttributes();
public static function getAttributesRaw();
public static function saveAttribute(Attribute $attribute, $raw = false);
public static function deleteAttribute($id, $raw = false);
public function save($raw = false);

```

#### Group functions

```
public static function getByID($id, $include = null);
public static function getByIDRaw($id, $include = null);
public static function getByTag($tag, $include = false);
public static function getByTagRaw($tag, $include = false);
public static function getAll($args);
public static function getAllRaw($args);
public static function getAttributes();
public static function getAttributesRaw();
public static function saveAttribute(Attribute $attribute, $raw = false);
public static function deleteAttribute($id, $raw = false);
public function save($raw = false);
public function getPackages($raw = false);

```

#### Invoice functions

```
public static function getByID($id, $include = null);
public static function getByIDRaw($id, $include = null);
public static function getByTag($tag, $include = false);
public static function getByTagRaw($tag, $include = false);
public static function getAll($args);
public static function getAllRaw($args);
public static function getAttributes();
public static function getAttributesRaw();
public static function saveAttribute(Attribute $attribute, $raw = false);
public static function deleteAttribute($id, $raw = false);
public function save($raw = false);
public function settle($raw = false);
public function applyPayment(array $data, $raw = false);
```

#### Package functions

```
public static function getByID($id, $include = null);
public static function getByIDRaw($id, $include = null);
public static function getByTag($tag, $include = false);
public static function getByTagRaw($tag, $include = false);
public static function getAll($args);
public static function getAllRaw($args);
public static function getAttributes();
public static function getAttributesRaw();
public static function saveAttribute(Attribute $attribute, $raw = false);
public static function deleteAttribute($id, $raw = false);
public function save($raw = false);

```

#### Payment functions

```
public static function getByID($id, $include = null);
public static function getByIDRaw($id, $include = null);
public static function getByTag($tag, $include = false);
public static function getByTagRaw($tag, $include = false);
public static function getAll($args);
public static function getAllRaw($args);
public static function getAttributes();
public static function getAttributesRaw();
public static function saveAttribute(Attribute $attribute, $raw = false);
public static function deleteAttribute($id, $raw = false);
public function save($raw = false);

```

#### Plan functions

```
public static function getByID($id, $include = null);
public static function getByIDRaw($id, $include = null);
public static function getByTag($tag, $include = false);
public static function getByTagRaw($tag, $include = false);
public static function getAll($args);
public static function getAllRaw($args);
public static function getAttributes();
public static function getAttributesRaw();
public static function saveAttribute(Attribute $attribute, $raw = false);
public static function deleteAttribute($id, $raw = false);
public function save($raw = false);

```

#### Refund functions

```
public static function getByID($id, $include = null);
public static function getByIDRaw($id, $include = null);
public static function getByTag($tag, $include = false);
public static function getByTagRaw($tag, $include = false);
public static function getAll($args);
public static function getAllRaw($args);
public static function getAttributes();
public static function getAttributesRaw();
public static function saveAttribute(Attribute $attribute, $raw = false);
public static function deleteAttribute($id, $raw = false);
public function save($raw = false);

```

#### Subscription functions

```
public static function getByID($id, $include = null);
public static function getByIDRaw($id, $include = null);
public static function getByTag($tag, $include = false);
public static function getByTagRaw($tag, $include = false);
public static function getAll($args);
public static function getAllRaw($args);
public static function getAttributes();
public static function getAttributesRaw();
public static function saveAttribute(Attribute $attribute, $raw = false);
public static function deleteAttribute($id, $raw = false);
public function save($raw = false);

```

#### Member functions

```
public static function getByID($id, $include = null);
public static function getByIDRaw($id, $include = null);
public static function getByTag($tag, $include = false);
public static function getByTagRaw($tag, $include = false);
public static function getAll($args);
public static function getAllRaw($args);
public static function getAttributes();
public static function getAttributesRaw();
public static function saveAttribute(Attribute $attribute, $raw = false);
public static function deleteAttribute($id, $raw = false);
public function save($raw = false);

```

#### Team functions

```
public static function getByID($id, $include = null);
public static function getByIDRaw($id, $include = null);
public static function getByTag($tag, $include = false);
public static function getByTagRaw($tag, $include = false);
public static function getAll($args);
public static function getAllRaw($args);
public static function getAttributes();
public static function getAttributesRaw();
public static function saveAttribute(Attribute $attribute, $raw = false);
public static function deleteAttribute($id, $raw = false);
public function save($raw = false);

```