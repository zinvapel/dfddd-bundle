# Symfony Basis Bundle
Contains common things for creation of Symfony application. Basically, can be used for every PHP application.

## Versions
Version X.Y.Z imply:
- X - const 1 before concepts does not changed
- Y - every update contains new features
- Z - bug fixes

## BasisBundle for Symfony users
Plug BasisBundle into your `bundles.php`.

Explain configuration:
```yaml
basis:
    routes:
        get_user:
            context:
                factory_type: denormalize
                data_extractor: 'route', 'empty', 'json', 'post_json'
                dto_class: Zinvapel\Basis\BasisBundle\Regular\Dto\Service\EmptyDto
                constraints_provider: Zinvapel\Basis\BasisBundle\Regular\Dto\Service\EmptyDto::getConstraints
            service: some.service.name
            responses:
                Some\Dto\Name:
                    factory_type: no_content
                    status_code: 204
                    custom: factory
```
This config will generate `basis.routes.get_user.controller` service, instance of `Zinvapel\Basis\BasisBundle\Http\Flow\Controller`.
- `context.factory_type` defines factory, instance of `Zinvapel\Basis\BasisBundle\Http\Flow\Context\Factory\ContextFactoryInterface`. 
This factory respond for transformation Request into `Zinvapel\Basis\BasisBundle\Core\Dto\ServiceDtoInterface`. Can be:
    - `denormalize` - extracts data from Request, validates it and assemble `ServiceDtoInterface`
    - `custom` - indicates to take service name from custom parameter.
- `context.custom` - user defined factory.
- `context.data_extractor` define how to extract data from request. Can be:
    - `route` - from route parameters
    - `empty` - returns empty array
    - `json` - gets json from body and converts into associative array
    - `post_json` - combines `route` and `json`
- `context.dto_class` defines class, instance of `Zinvapel\Basis\BasisBundle\Core\Dto\ServiceDtoInterface`, into which Request will be converted.
- `context.constraints_provider` defines callable, which returns Symfony constraints array for Request validation.
- `service` defines instance of `Zinvapel\Basis\BasisBundle\Core\ServiceInterface`. Presents route 
business logic, accepts the above `ServiceDtoInterface`, returns `Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInteface`.
- `responses.Some\Dto\Name` defines how convert `Some\Dto\Name`, instance of `Zinvapel\Basis\BasisBundle\Core\Dto\StatefulDtoInteface`,
to Symfony Response.
- `responses.X.factory_type` defines response factory, can be:
    - `no_content`
    - `json`
    - `custom`
- `responses.X.status_code` defines status code.

Use env `ZINVAPEL_BASIS_HTTP_FLOW_DEBUG` for force display all HTTP exceptions

## OpenApiAssertionBundle (deep beta)
Generate assertions and classes from Swagger documentation. Provide one command:
```bash
$ php bin/console zinvapel:oa:parse-swagger <swagger.yaml> [--target <target> [--class <className>]]
Where:
<swagger.yaml> - path to yaml file with swagger spec
<target> - one of 'full', 'object', 'http'
<className> - for target 'object'. Generate just this class
```

Result examples:
- Class
```php
ChatIdMessageMessageIdPatchDto0DtoErrorsItemDto:
final class ChatIdMessageMessageIdPatchDto0DtoErrorsItemDto
{
        /**
         * @Serializer\Groups({"body"})
         */
        private ?string $path;

        /**
         * @Serializer\Groups({"body"})
         */
        private ?string $error;

        public function setPath(?string $path = null): self
        {
                $this->path = path;

                return $this;
        }

        public function getPath(): ?string
        {
                return $this->path;
        }

        public function setError(?string $error = null): self
        {
                $this->error = error;

                return $this;
        }

        public function getError(): ?string
        {
                return $this->error;
        }

}
```
- Assertions
```php
ChatWithStatDto1StatDto:
new Assert\Collection([
    'allowExtraFields' => true,
    'fields' => [
        'unreadCount' => [
            new Assert\Type([
                'type' => 'integer',
            ]),
            new Assert\GreaterThanOrEqual([
                'value' => 0,
            ]),
        ],
        'mentionsCount' => [
            new Assert\Type([
                'type' => 'integer',
            ]),
            new Assert\GreaterThanOrEqual([
                'value' => 0,
            ]),
        ],
    ],
])

```