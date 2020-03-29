L — Liskov Substitution Principle
--------------------------------

**Concepto**

- Si S es un subtipo de T, instancias de T deberían poderse sustituir por instancias de S sin alterar las propiedades del programa.

P.e.: RepositorioUsarioMysql es un subtipo de RepositorioUsario. Instancias de RepositorioUsario deberían poderse sustituirse por RepositorioUsarioMysql
sin alterar las propiedades del programa.

Si tienes una jerarquía (subtipos), en cualquier momento dado podría reemplazar los tipos y todo debería seguir funcionando como se espera. 
Cuando tenemos una jerarquía es porque estamos estableciendo un contrato en el padre. Si seguimos Liskov, si garantizamos que en el hijo
se cumple ese contrato, podemos ampliar ese padre, reemplazarlo por cualquier otro hijo y todo seguirá funcionando guay (podemos aplicar OCP).

**SRP para clases pequeñas y acotadas de responsabilidad y LSP son la premisa para poder aplicar OCP**: que nuestras clases sean pequeñas y que haya
un contrato robusto que se mantenga a lo largo de la jerarquía.


**Cómo**

- El comportamiento de subclases debe respetar el contrato de la superclase.

**Finalidad**

- Mantener correctitud para poder aplicar OCP.


Ejemplo Sencillo
---------------

En este [enlace](https://github.com/CodelyTV/java-solid-examples/tree/master/src/main/java/tv/codely/solid_principles/liskov_substitution_principle) 
tenéis el repo con todos los ejemplos que vemos en este video.

```php
class Rectangle {

    private Integer length;      
    private Integer width;

    Rectangle(Integer length, Integer width) {  
        this.length = length;
        this.width = width;
    }

    void setLength(Integer length) {
        this.length = length;
    }

    void setWidth(Integer width) {
        this.width = width;
    }

    Integer getArea() {
        return this.length * this.width;
    }
}
```

Podemos ver cómo nuestra clase Rectangle cuenta con dos atributos width y length y, además de un constructor y los 
setters de cada atributo, observamos una función getArea que implementa el comportamiento necesario para calcular el área 
del rectángulo (así nuestro modelo de dominio es mucho más rico en comportamiento => Tell, Don’t Ask!).


```php
final class Square extends Rectangle {
    Square(Integer lengthAndWidth) {
        super(lengthAndWidth, lengthAndWidth);
    }

    @Override
    public void setLength(Integer length) {
      super.setLength(length);
      super.setWidth(length);
    }
    @Override
    public void setWidth(Integer width) {
      super.setLength(width);
      super.setWidth(width);
    }
}
```

Nuestro Square es un tipo de Rectangle con la restricción de que su largo y ancho son iguales, es decir, si modificamos 
el largo, debemos modificar el ancho y viceversa. Así, la clase Square extiende de nuestra clase Rectangle.
Vemos así en el propio constructor cómo recibe un único parámetro, pues utilizará el mismo tanto para definir el
 ancho como el largo en la superclase.

Test SquareShould:

```php
final class SquareShould {
    @Test
    void not_respect_the_liskov_substitution_principle_breaking_the_rectangle_laws_while_modifying_its_length() {
        Integer squareLengthAndWidth = 2;
        Square square = new Square(squareLengthAndWidth);

        Integer newSquareLength = 4;
        square.setLength(newSquareLength);

        Integer expectedAreaTakingIntoAccountRectangleLaws = 8;

        assertNotEquals(expectedAreaTakingIntoAccountRectangleLaws, square.getArea());
	  }
}
```

Como vemos en el Test, cabría esperar que si Square extiende de Rectangle, mantenga el contrato establecido por éste 
y al modificar el tamaño del largo, su área se modifique como lo haría en el padre. Sin embargo, observamos que esto no se
 está cumpliendo en este caso, no se está cumpliendo el LSP 👎 .
Pese a que estemos permitiendo que compile nuestra aplicación, ya que estamos manteniendo las firmas de los métodos heredados, 
el propio cuerpo de esos métodos hace que se viole el correcto funcionamiento del programa.


Keep it real
------------

[CQRS DDD PHP Example](https://github.com/CodelyTV/cqrs-ddd-php-example/blob/master/src/Mooc/Students/Infrastructure/Persistence/StudentRepositoryMySql.php)

Repositorio del curso sobre CQRS en el que nos basaremos para ver nuestro caso de LSP

Clase UserRepositoryMySql:

```php
final class UserRepositoryMySql extends Repository implements UserRepository
{
    public function save(User $user): void{
        $this->entityManager()->persist($user);    
    }

    public function flush(User $user)
    {
        $this->entityManager()->flush($user);
    }

    public function saveAll(Users $users)
    {
        each($this->persister(),$users);
    }
}
```
Nuestra UserRepositoryMySql es una implementación de un repositorio MySql, pero ojo! 👀 con la característica de utilizar 
Doctrine como ORM[^ORM]. Doctrine implementa el `Unit of work` pattern, que nos ofrece algunas características:

Utiliza una ‘caché’ en memoria (unit of work) donde almacena inicialmente los datos antes de persistir en BD, haciendo más 
rápida su recuperación durante ese proceso.
Cuando editamos estos objetos, compara las diferencias con el estado almacenado para saber él mismo qué atributos deben 
actualizarse.
¿Por qué puede suponer una violación del LSP? 😮
Como clientes externos que sólo conocemos la interface publicada, podríamos esperar que cuando se llamase al método save, con 
nuestra implementación se persistieran la información en BD.

Interface UserRepository:

```php
interface UserRepository
{
    public function save(User $user): void;
    
    public function saveAll(Users $users): void;
    
    public function search(UserId $id): ?User;
    
    public function all(): Users;
}
```

Sin embargo nuestra implementación del método save internamente llama a persist, que simplemente almacenará los datos 
en la unit of work sin forzar la persistencia real en BD. Como vemos, aunque se ha mantenido la firma de los 
métodos definidos en la interface, estaríamos violando el LSP puesto que no podríamos utilizarla para reemplazar otras 
implementaciones que no utilizan el Unit of work pattern y si estarían persistiendo en su BD al llamar al método save.

[^ORM]: Un ORM es una biblioteca que nos permite comunicarnos con nuestra BD a través de clases/objetos.