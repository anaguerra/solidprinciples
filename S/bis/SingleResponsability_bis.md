S — Single Responsability Principle
------------------------------------

Principio de única responsabilidad

Cada clase debe ocuparse de una sola responsabilidad, o dicho de otra manera: Cada clase debería tener una única razón para 
ser modificada. Si identificas que alguna de tus clases están desempeñando más de una responsabilidad, 
deberías partirla en n clases, una por cada responsabilidad. 


En muchas ocasiones estamos tentados a poner un método reutilizable que no tienen nada que ver con la clase 
simplemente porque lo utiliza y nos pilla más a mano. En ese momento pensamos "Ya que estamos aquí, para que 
voy a crear una clase para realizar esto. Directamente lo pongo aquí".

El problema surge cuando tenemos la necesidad de utilizar ese mismo método desde otra clase. 
Si no se refactoriza en ese momento y se crea una clase destinada para la finalidad del método, 
nos toparemos a largo plazo con que las clases realizan tareas que no deberían ser de su responsabilidad.

Es uno de los principios más fáciles de entender y uno de los menos cumplidos :D
Está relacionado con los conceptos de Cohesión y SoC (separación de responsabilidades)

"El propósito de las clases es organizar el código de tal manera que se minimize la complejidad
Por lo tanto las clases deben ser":

* Lo suficientemente pequeñas para minimizar el **acoplamiento**
* Lo suficientemente grandes para maximizar la **cohesión**


**Ejemplo 1**

Ejemplo típico de separación entre la gestión del estado de un objeto y su representación


Tenemos varias figuras de las que después queremos calcular su área total:

    Class Circle 
    {
        public $radius;
    
        public function __construct($radius) 
        {
            $this->radius = $radius;
        }
    }

    Class Square 
    {
        public $length;
    
        public function __construct($length) 
        {
            $this->length = $length;
        }
    }
Primero creamos las clases de las figuras y dejamos que los constructores se encarguen de recibir las medidas necesarias.

Ahora creamos la clase **AreaCalculator**, que recibe un array con los objetos de cada una de las figuras para ser sumadas:

    class AreaCalculator
    {
        protected $shapes;
    
        public function __construct($shapes = array())
        {
            $this->shapes = $shapes;
        }
    
        public function sum()
        {
            // Aquí va la lógica para sumar todas las áreas
        }
    
        public function output()
        {
            return implode('', array(
                "<h1>",
                    "Suma de todas las áreas: ",
                    $this->sum(),
                "</h1>"
            ));
        }
    }
    
Para utilizar la clase AreaCalculator simplemente instanciamos la clase y le pasamos un array con las figuras, 
mostrando el output al final:

    $shapes = array (
        new Circle(3),
        new Square(4)
    );

    $areas = new AreaCalculator($shapes);
    
    echo $areas->output();
    
El problema del método output es que la clase AreaCalculator además de calcular las áreas maneja la lógica de la salida 
de los datos. El problema surge cuando queremos mostrar los datos en otros formatos como json, por ejemplo.

El principio Single responsibility determinaría en este caso que AreaCalculator sólo calculase el área, y que la 
funcionalidad de la salida de los datos de produjera en otra entidad. Para ello podemos crear la clase SumCalculatorOutputter, que determinará como mostraremos los datos de las figuras. Con esta clase el código quedaría así:

    $shapes = array (
        new Circle(3),
        new Square(4)
    );
    
    $areas = new AreaCalculator($shapes);
    $output = new SumCalculatorOutputter($areas);
    
    echo $output->toJson();
    echo $output->toHtml();



**Ejemplo 2**


[srp_violation.php](srp_violation.php)

[srp.php](srp_good.php)



