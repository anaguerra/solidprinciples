O — Liskov Substitution Principle
--------------------------------

El principio de Sustitución Liskov fue definido por Barbara Liskov. 
Básicamente este principio nos dice que si en nuestro código estamos usando una clase, y esta clase es extendida, 
tenemos que poder utilizar cualquiera de las clases hijas y que el programa siga siendo válido. 

Esto nos obliga a asegurarnos de que cuando extendemos una clase no estamos alterando el comportamiento de la padre.


**¿Cómo detectar que estamos violando el principio de sustitución de Liskov?**

Seguro que te has encontrado con esta situación muchas veces: creas una clase que extiende de otra, 
pero de repente uno de los métodos te sobra, y no sabes que hacer con él. Las opciones más rápidas son 
bien dejarlo vacío, bien lanzar una excepción cuando se use, asegurándote de que nadie llama incorrectamente a 
un método que no se puede utilizar. Si un método sobrescrito no hace nada o lanza una excepción, es muy probable que
 estés violando el principio de sustitución de Liskov. Si tu código estaba usando un método que para algunas 
 concreciones ahora lanza una excepción, ¿cómo puedes estar seguro de que todo sigue funcionando?
 

Continuando con la clase AreaCalculator, ahora tenemos una clase VolumeCalculator que extiende la clase AreaCalculator:


```php
class VolumeCalculator extends AreaCalculator
{
    public function __construct($shapes = array())
    {
        parent::__construct($shapes);
    }

    public function sum()
    {
        // Calcula el volumen y devuelve un array de salida
        $summedData = '';
        return $summedData;
    }
}
```

VolumeCalculator se podría sustituir por AreaCalculator.

La clase SumCalculatorOutputter quedará:

```php
class SumCalculatorOutputter {

    protected $calculator;

    public function __construct(AreaCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function toJson()
    {
        $data = array (
          'sum' => $this->calculator->sum()
        );

        return json_encode($data);
    }
    
    public function toHtml()
    {
        return implode('', array(
            '<h1>',
                'Suma de las áreas de las figuras: ',
                $this->calculator->sum(),
            '</h1>'
        ));
    }
}
```

**Concepto**

- Si S es un subtipo de T, instancias de T deberían poderse sustituir por instancias de S sin alterar las propiedades del programa.

P.e.: RepositorioUsarioMysql es un subtipo de RepositorioUsario. Instancias de RepositorioUsario deberían poderse sustituirse por RepositorioUsarioMysql
sin alterar las propiedades del programa.

Si tienes una jerarquía (subtipos), en cualquier momento dado podría reemplazar los tipos y todo debería seguir funcionando como se espera. 
Cuando tenemos una jerarquía es porque estamos estableciendo un contrato en el padre. Si seguimos Liskov, si garantizamos que en el hijo
se cumple ese contrato, podemos ampliar ese padre, reemplazarlo por cualquier otro hijo y todo seguirá funcionando guay (podemos aplicar OCP).

SRP para clases pequeñas y acotadas de responsabilidad y LSP son la premisa para poder aplicar OCP: que nuestras clases sean pequeñas y que haya
un contrato robusto que se mantenga a lo largo de la jerarquía.


**Cómo**

- El comportamiento de subclases debe respetar el contrato de la superclase.

**Finalidad**

- Mantener correctitud para poder aplicar OCP.

    
