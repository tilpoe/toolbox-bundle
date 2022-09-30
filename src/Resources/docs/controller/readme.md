# Controller

The `\Feierstoff\ToolboxBundle\Controller\Controller` class extends the standard
`\Symfony\Bundle\FrameworkBundle\Controller\AbstractController` class from Symfony and provides
some functions to access some factories easier than normal.

## Setup

Inside your symfony project, create a normal controller, but instead of extending from
`\Symfony\Bundle\FrameworkBundle\Controller\AbstractController`, extend from `\Feierstoff\ToolboxBundle\Controller\Controller`.

```php
namespace App\Controller;

use Feierstoff\ToolboxBundle\Controller;

class TestController extends Controller {

}
```

## Functions

### Getting the entity manager

Get your entity manager by using `$this->Em()`.

###### Attributes

| Name       | Type   | Default   | Description                                      |
| ---------- | ------ | --------- | ------------------------------------------------ |
| connection | string | "default" | Name of the connection defined in doctrine.yaml. |

###### Example

```php
function getPersonByName(string $name) {
    return $this->Em()
        ->getRepository(Person::class)
        ->findOneBy(["name" => $name])
}
```

### Getting a query builder

Get a query builder by using `$this->Qb()`.

###### Attributes

| Name       | Type   | Default   | Description                                      |
| ---------- | ------ | --------- | ------------------------------------------------ |
| connection | string | "default" | Name of the connection defined in doctrine.yaml. |

###### Example

```php
function getPersonByName(string $name) {
    return $this->Qb()
        ->select("p")
        ->from(Person::class, "p")
        ->where("p.name = :name")
        ->setParameter("name", $name)
        ->getQuery()
        ->execute()
}
```

### Getting an instance of a serializer

Get an instance of the **FeierstoffToolbox** Serializer with `$this->Serializer()`.

### Get the current session

Get an object of the current session by using `$this->Session()`.

### Get the current environment of the webpage

Check if you are in a specific environment by using `$this->isDevEnv()`, `$this->isProdEnv()`, `$this->isTestEnv()` or 
check a custom environment by using `$this->isEnv($env)`.