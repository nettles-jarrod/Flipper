Flipper - ORM
===

Flipper is a simple micro-mapper. You can use it to map data to a class, existing object, or even your Doctrine or Propel entities. It was born out of frustration with having to specify complicated associations between Doctrine entities in order to run reports.

Installation
---

Flipper can easily be installed through Composer.

```json
{
    "require": {
        "blackshawk/flipper": "dev-master"
    }
}
```

Design Goals - Another ORM?!
---
Flipper is not meant to replace your ORM. It was built to run alongside Doctrine, for those times when Doctrine just isn't cutting it for you. How many times have you wished that you could just drop down into raw SQL, while still using your existing Doctrine entities?
  
It's been stripped of everything you don't need. There are no relationships to define, and there is no special query syntax.

Here are the stated design goals of the project.

1. Be data source agnostic.
2. Don't try to be Doctrine or some other heavy-weight ORM.
4. Provide a simple way to split result sets across desired objects without specifying relationships between those objects.
5. Be fully compatible with existing ORMs like Doctrine and Propel. You should never need Flipper specific code or classes.

Show me the code
---
Here is the simplest Flipper example.

```php
use Flipper\Flipper;

$data = [
    'id' => 35487,
    'title' => 'Call of the Wild',
    'body' => 'Dark spruce forest frowned on either side the frozen waterway.'
];

$flipper = new Flipper();
$post = $flipper->mapOne('Post', $data);

//print_r result

..Post Object
(
    [id:public] => 35487
    [title:public] => Call of the Wild
    [body:public] => Dark spruce forest frowned on either side the frozen waterway.
)

//You can also statically create the Flipper object, and achieve everything with one line.

$post = Flipper::_()->mapOne('Post', $data);

```

This is an extremely simple example, and could also be achieved directly with PDO. Flipper's power is in diseminating result sets to multiple objects with minimal effort on your part. Suppose you want your results mapped to two different objects? With Doctrine this means specifying some sort of relationship betwen the two objects.













