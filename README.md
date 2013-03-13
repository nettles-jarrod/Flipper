Flipper - (!ORM)
===

Flipper is a simple way of writing SQL and having it map to objects. You can use it to map data to any type of class; even your Doctrine or Propel entities! It was born out of frustration with having to specify complex associations between Doctrine entities in order to build complicated reports.

**This project is still very much in development! Certain aspects may change significantly, or disappear altogether! Proceed with caution!**


Installation
---

Flipper can be easily installed and loaded with Composer. You are using [Composer](http://getcomposer.org/), right?

```json
{
    "require": {
        "blackshawk/flipper": "dev-master@dev"
    }
}
```

Another ORM?!
---
First things first: **Flipper is not meant to replace your ORM**. Think of Flipper as the lovable companion to your day-to-day ORM. It lets you drop down into normal SQL and quickly map the results to your existing entities (or however you handle your models).
  
There are no relationships to define and there is no special query syntax. Just write a query that returns a response and tell Flipper where you want it to go.

The Flipper class itself is a thin layer that takes a DBAL database connection as its constructor. At that point, you can issue queries through Flipper and have them automatically map to your requested objects.

Here are the design goals of the project.

1. Be data source agnostic.
2. Be as hands-off concerning SQL as possible.
3. Don't try to be Doctrine, Propel, or some other heavy-weight ORM.
4. Provide a *simple* way to split result sets across desired objects without specifying relationships between those objects.
5. Be fully compatible with existing ORMs like Doctrine and Propel. You should never need Flipper specific code or classes.


Enough talking, you. Show me the code.
---
Here is the simplest Flipper example - querying a single row from a database.

```php
require_once('vendor/autoload.php');

use \Flipper\Flipper;

$flipper = new Flipper($connection); //where $connection is a DBAL instance

$post = $flipper->queryOne('Post', 'select * from post where post_id = :id', ['id' => 35487]);

//print_r result

/**
..Post Object
(
    [id]    => 35487
    [title] => Call of the Wild
    [body]  => Dark spruce forest frowned on either side the frozen waterway.
)
**/

```

This is an extremely simple example, and could also be achieved with PDO. Suppose though, that we wanted to get information about the author of the post?

```php

$query = 'select *
          from post p
          left join author a on p.author_id = a.author_id
          where p.post_id = :id';
          
$result = $flipper->queryOne(['Post', 'Author'], $query, ['id' => 35487], $split = 'author_id');

//print_r result

/**
Array
(
    [post] => Post Object
        (
            [post_id] => 35487
            [author_id] => 1
            [created_date] => 2013-01-01
            [title] => Call of the Wild
            [body] => Dark spruce forest frowned on either side the frozen waterway.
        )
    [author] => Author Object
        (
            [author_id] => 1
            [first_name] => Jack
            [last_name] => London
        )
)
**/

```

The ```$split``` parameter might be confusing at first. Flipper operates on your data, not on your objects. In our previous example, we've specified that we would like two different classes to be created and filled with data. With the split parameter, we're telling Flipper where to stop filling the first object and to start filling the second. You will typically need to specify a split for each additional object that you request.


Features
---

1. Toss virtually any data source at it - Flipper will work with it. This includes arrays and anything implementing the Iterator or IteratorAggregate interfaces. If it works with ```foreach()```, it works with Flipper.
2. Map your results to one object - or many. Flipper cares not.
3. When working with Doctrine entities, Flipper will respect your ORM annotations like ```column_name```. That way you can return ```post_id``` and still have it seamlessly map with ```id``` in your entity **(still in development!)**.


FAQ
---

##### My ORM can already do all this! All I have to do is specify this relation here…

Yes, you could very easily specify a relationship in your favorite ORM to handle the queries in the above examples. The problem is that other ORMs scale in complexity proportionally with the complexity of your database. How many times have you had to drop into your annotations or YAML/XML configs, hunting out why something is not behaving properly?

Flipper stays simple the entire way since it doesn't care what your database looks like. The complexity of your query is irrelevant, since Flipper will never have to operate on anything more complicated than array(array(), …).

##### Where's the query builder?

Flipper doesn't come with a query builder. There are a million of them out there. So long as your favorite implements __toString() though, Flipper will convert the object to a SQL string for you when its time for execution.

##### My ORM already let's me drop down into raw SQL.

Great! Like was said earlier, Flipper is *not* meant to replace your day-to-day ORM. However, if it is anything like [Doctrine's Native-SQL](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/native-sql.html) implementation, you might want to take a closer look at Flipper.

##### How are you using Flipper?

Right now Flipper is being put to use handling queries that would otherwise become unwieldy in Doctrine. So far it has been the perfect complement. My general rule is that if Doctrine didn't configure it for me during its entity generation, then I drop into Flipper instead.

There is still a lot of development to go in order to complete my vision for Flipper but it has a good start and is already highly useful.

##### Can I issue insert/update/delete statements?

Right now you can't. This would be extremely easy to add in the future but I made the decision not to bother with it right now since I'm perfectly happy letting Doctrine handle these things for me. Plus you get all the benefits of things like lifecycle callbacks which aren't possible with Flipper.

##### How does Flipper handle things like lifecycle callbacks?

It doesn't.

##### Does Flipper come with any auto-generation tools?

No. Doctrine [does a great job](http://docs.doctrine-project.org/en/2.0.x/reference/tools.html) of this, though.

##### Can Flipper do "x"??

Probably not!

##### Should I rip out all my (insert-ORM-here) stuff and use Flipper instead?

***NO.***

##### What is the point of Flipper?

Flipper was born out of my frustration with large ORMs like Doctrine. While it is an *amazing* project, I feel that the abstraction has started to go too far in some areas. Abstractions can be powerful but when you start to see an entirely new environment sprout up around your abstraction, there's a bit of a problem. Suddenly we're in an entirely different atmosphere. Programmers have gone from learning to write SQL - a very useful skill - to learning how to endlessly abstract away the need to write SQL. We've built a fifty mile bypass to avoid a small hill. Flipper gives you a tunnel.

1. Abstracting all our writes to the database is good - it promotes data integrity. 
2. Abstracting all our reads can be bad.




