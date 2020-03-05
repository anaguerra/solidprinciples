S — Single Responsability Principle
===================================

**Introducción conceptual**

Concepto:
- Una clase = Un concepto y responsabilidad
- Una clase debería tener sólo 1 razón para cambiar

Cómo conseguirlo:
- Clases pequeñas con objetivos acotados

Finalidad:
- Alta cohesión y robustez
- Permitir composición de clases (inyectar colaboradores)
- Evitar duplicidad de código


    Nota: MODELO DE DOMINIO: Aquella clase que representa un concepto de nuestro contexto: un usuario, un producto, un carrito 
    de la compra...

Una forma rápida de saber si estamos respetando el SRP es ir a nuestra capa de servicios y mirar si tienen 
más de un método público. Esto implica dos puntos de entrada a esa clase, por tanto hace dos cosas diferentes.
También se ve cuando tienen nombres muy abstractos. P.e. se llama EmailService en vez de llamarse EnviadorDeEmails.
EmailService se encarga de enviar emails y de más cosas, o sea tiene varios métodos públicos


**Alta cohesión y robustez**

Alta cohesión: que las cosas relacionadas entre ellas estén más juntitas. P.e., en el caso de un usuario el método que comprueba si 
es su cumpleaños estará en User (modelo de dominio). Escondemos los detalles de implementación.

Robustez: más tolerante al cambio, se pueden reemplear las cosas

**Permitir composición de clases**

En lugar de tener 1 clase con 5 métodos, puedes tener una clase a la cual se le inyectan 5 clases. 
Y si se quiere reusar alguna de estas internas (p.e. EnviadorEmails y PonerEnBlacklistEmail) en más de un caso de uso, se inyectan 
en cualquier otro sitio.

El SRP nos abre la puerta a implementar la inyección de dependencias, inversión de dependencias, etc. PAra llegar a ese punto  es
necesario que nuestras clases sean acotaditas.

**Evitar duplicidad de código**
La composición evita la necesidad de copiar y pegar.

Niveles de granularidad
---
SRP deja mucho lugar a la interpretación así que necesitamos criterios para decidir si lo estamos respetando y hasta qué nivel.


- Order | User: Tenemos que discernir en qué tipo de elemento estamos (modelo de dominio o servicios). 
En este caso son modelos de dominio, no servicios. Vídeo sobre modelos de dominio anémicos y principio de diseño Tell don’t ask: https://www.youtube.com/watch?v=Be-ULOIGAZk

- OrderAnalyzer | OrderProcessor (también OrderManager, OrderService...). Los términos genéricos llevan a más de 1 responsabilidad. Podemos meter cualquier lógica relacionada
con los pedidos sin caer en que no estamos respetando el SRP. 

- OrderTrustabilityChecker | OrderMarginCalculator. Son más específicos, no abren la puerta a añadir más funcionalidad. Más explícitos (esto relacionado con Clean Code)

- Ejemplo: Conexión a base de datos representada en una clase aislada de la clase que obtiene los usuarios o vídeos. P.e. Repository


Modelo de dominio Book:

```php
final class Book
{
    public String getTitle()
    {
        return "A great book";
    }
    public String getAuthor()
    {
        return "John Doe";
    }
    public void printCurrentPage()
    { 
        System.out.println("current page content");
    }
```

Servicio cliente del modelo de dominio:

```php
final class Client
{
    public Client() {
        Book book = new Book(…);
        book.printCurrentPage();
    }
}
```

⚠️ Motivo del por qué no respetamos SRP: Book está acoplada al canal estándar de salida al imprimir la página actual. 
Sabe cómo modelar los datos y cómo imprimirlos.

Está acoplado el mecanismo de entrega de los datos al modelado de los datos.


**Refactor respetando SRP**

Ahora lo que hacemos en getCurrentPage es devolver el txto, y ya vendrá otra clase cuyo propósito es imprimir.

Clase Book:

```php
final class Book
{
    public String getTitle()
    {
        return "A great book";
    }
    public String getAuthor()
    {
        return "John Doe";
    }
    public String getCurrentPage()
    {
        return "current page content";
    }
}
```

Implementación de la impresora:

```php
final class StandardOutputPrinter
{
    public void printPage(String page)
    {
        System.out.println(page);
    }
}
```

Servicio cliente:

```php
final class Client
{
    public Client() {
        Book book = new Book(…);
        String currentPage = book.getCurrentPage();
        StandardOutputPrinter printer = new StandardOutputPrinter();
        printer.printPage(currentPage);
    }
}
```


**¿Esto a dónde nos lleva? Modularidad: más de una implementación**

Modularización: Ahora podemos extraer a una interfaz el pintar.

Interface Printer:

```php
interface Printer
{
    public void printPage(String page);
}
```
Con el `void` sabemos que no estamos devolviendo nada, estamos generando un side effect. 


Impresora por el canal estándar de salida:

```php
final class StandardOutputPrinter implements Printer
{
    public void printPage(String page)
    {
        System.out.println(page);
    }
}
```

Impresora por el canal estándar de salida pero en HTML:

```php
final class StandardOutputHtmlPrinter implements Printer
{
    public void printPage(String page)
    {
        System.out.println("<div>" + page + "</div>");
    }
}
```





