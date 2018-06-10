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
VolumeCalculator se podría sustituir por AreaCalculator.

La clase SumCalculatorOutputter quedará:

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