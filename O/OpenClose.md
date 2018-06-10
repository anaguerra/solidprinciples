O — Open Close Principle
------------------------

El principio Abierto/Cerrado especifica que una entidad software (una clase, módulo o función) debe estar abierta a 
extensiones pero cerrada a modificaciones. 

Dado por hecho que el software requiere cambios (diariamente) y que unas entidades dependen de otras, 
las modificaciones en el código de una de ellas puede generar indeseables efectos colaterales en cascada en todo el 
software.o. Es decir, el diseño debe ser abierto para poderse extender pero cerrado para poderse modificar.

Seguir este principio favorece un diseño que permite escalar la aplicación y extender la funcionalidad del software sin 
afectar a otros módulos de este.

El principio Open/Closed se suele resolver utilizando polimorfismo y herencia. En vez de obligar a la clase principal a saber 
cómo realizar una operación, delega esta a los objetos que utiliza, de tal forma que no necesita saber explícitamente 
cómo llevarla a cabo.

Este principio quiere decir que una clase debería ser fácilmente extendible sin modificar la propia clase. 


Vamos a ver ahora el método sum de la clase AreaCalculator:


**Ejemplo 1**

    public function sum()
    {
        foreach ($this->shapes as $shape) {
            if(is_a($shape, 'Square')){
                $area[] = pow($shape->length, 2);
            } elseif (is_a($shape, 'Circle')){
                $area[] = pi() * pow($shape->radius, 2);
            }
        }
        return array_sum($area);
    }
Si quisiéramos que el métdo sum pudiera calcular la suma de más figuras, tendríamos que seguir añadiendo bloques 
if/else, lo que va en contra del principio Open/Closed.

Una forma de hacer este método sum mejor es moviendo la lógica de calcular el area a la clase de cada figura, 
añadiendo un método area() en cada clase:

    Class Square 
    {
    // ...
        public function area()
        {
            return pow($this->length, 2);
        }
    }
    
Lo mismo se hará en la clase Circle:

    Class Circle
    {
    // ...
        public function area()
        {
            return pi() * pow($this->radius, 2);
        }
    }

Ahora para calcular la suma de las figuras proporcionadas dejaremos el método sum de la siguiente forma:

    public function sum()
    {
        foreach ($this->shapes as $shape)
        {
            $area[] = $shape->area;
        }
    
        return array_sum($area);
    }

Ahora podemos crear cualquier otra figura y pasarla para calcular la suma que no se romperá el código. Ahora la pregunta es la siguiente: ¿Cómo sabemos que el objeto que se pasa a AreaCalculator es realmente una figura o si la figura tiene un método llamado área?

Crear interfaces es una parte integral de los principios SOLID. Vamos a crear una interface que ha de implementar cada figura:

    interface ShapeInterface {
        public function area();
    }
    Ahora todas las figuras deberán implementarla:
    
    Class Circle implements ShapeInterface
    {
        // ...
    }
    Class Square implements ShapeInterface
    {
        // ...
    }

En el método sum de AreaCalculator podemos comprobar si las figuras proporcionadas son realmente instancias de ShapeInterface, y sino, lanzar una excepción:

    public function sum()
    {
        foreach ($this->shapes as $shape) {
            if($shape instanceof ShapeInterface)){
                $area[] = $shape->area;
                continue;
            }
            throw new AreaCalculatorInvalidShapeException;
        }
    
        return array_sum($area);
    }


**Ejemplo 2**

[openclose_violation.php](openclose_violation.php)

[openclose.php](openclose.php)
