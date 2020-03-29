I — Interface Segregation Principle
--------------------------------

**Concepto**

Ningún cliente debería verse forzado a depender de métodos que no usa.

P.e.: un Notificador. Tendríamos un INotificador, con los métodos EnviarNotificacion, ObtenerUltimasNotificaciones,
MarcarNotificacionesLeidas, DesmarcarNotificationLeida etc...

y luego un cliente (caso de uso, p.e. Slack) que haría "EnviarNotificaciones". 

El cliente en el momento que conoce a la interfaz puede que no necesite todos esos métodos. 


**Cómo**

- Definir contratos de interfaces basándonos en los clientes (casos de uso) que las usan y no en las implementaciones
que pudiéramos tener. Cuando definamos los contratos (cabeceras de los métodos) es decir: nombres, argumentos tipo de retorno, etc... 
tenemos que hacerlo pensando en esos clientes.


    Está relacionado con TDD, primero haces el test, después llega el caso de uso y cuando te hace falta la interfaz
    la haces con lo que te hace falta.
    Primero el caso de uso, y después de lo que te haga falta, de eso deriva la interfaz. "de fuera hacia dentro" tdd -->
    partir de lo más externo que se pueda. 
    
    Esto evita optimizaciones y abstracciones prematuras, etc.
   

- Evitar "Header Interfaces" promoviendo "Role Interface". 

Header Interface: (cuando violamos este principio) Cuando p.e. picamos todo el Repositorio mysql de usuario con todos los métodos
y a partir de extraemos la interfaz con los métodos de la implementación. 
O sea, escribimos las cabeceras de los métodos en base a las implementaciones.

Role Interface: Se trata de definir el caso de uso y la interfaz en base a ese caso de uso.  
Las cabeceras de los métodos tienen que estar tienen que estar definidas en base a los roles.


**Finalidad**

- Alta cohesión y bajo acoplamiento estructural.


Ejemplo sencillo
-------------------------

Queremos poder enviar notificaciones vía email, Slack, o fichero txt ¿Qué firma tendrá la interface? 📨

    a) $notifier($content)
    b) $notifier($slackChannel, $messageTitle, $messageContent, $messageStatus) ❌
    c) $notifier($recieverEmail, $emailSubject, $emailContent) ❌
    d) $notifier($destination, $subject, $content) ❌
    e) $notifier($filename, $tag, $description) ❌

Podemos descartar que las opciones B, C y E fueran interfaces válidas, puesto que se estaría haciendo Header Interface en base 
a la implementación (para Slack, email y fichero respectivamente).

En el caso de la opción D, podríamos considerarlo inválido dado que el tipo $destination no nos ofrece ninguna especificidad 
(no sabemos si es un email, un canal…).

Por último, en la opción A, sólo estaríamos enviando el contenido, por lo que las particularidades de cada uno de los tipos de notificación tendrían que venir dados en el constructor (dependiendo del caso de uso no siempre se podría).

Las interfaces pertenecen a los clientes y no a quienes las implementan

Diferencia: Nivel de abstracción de la implementación

a) Si sabemos destinatario en tiempo de compilación (parámetros por constructor)
d) Si no sabemos destinatario hasta tiempo de ejecución (perdemos el tipado)
f) Dos subscribers diferentes (email y slack) que leen de módulos…

[Principio de Segragación de Interfaces](https://youtu.be/EzUIbMdxJTk)

[Errores comunes al diseñar Interfaces - #SOLID - ISP](https://youtu.be/mDAQLkdNGHU)


**¿Cómo detectar que estamos violando el Principio de segregación de interfaces?**
  
Si al implementar una interfaz ves que uno o varios de los métodos no tienen sentido y te hace falta dejarlos vacíos 
o lanzar excepciones, es muy probable que estés violando este principio. Si la interfaz forma parte de tu código, divídela 
en varias interfaces que definan comportamientos más específicos.
  
Recuerda que no pasa nada porque una clase ahora necesite implementar varias interfaces. El punto importante es 
que use todos los métodos definidos por esas interfaces


Ver también: https://devexperto.com/principio-de-segregacion-de-interfaces/



Keep it real
-------------------------

Clase UserRepositoryMySql:

```php
final class UserRepositoryMySql extends Repository implements UserRepository
{
    public function save(User $user): void
    {
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

Dejándonos llevar por la prisa para ver datos, hemos comenzado con la implementación del repositorio para MySql, para lo cual, 
si recordamos la lección anterior, con el patrón Unit of work necesitábamos dos métodos save y flush para persistir en BD.

Interface UserRepository

```php
interface UserRepository
{
    public function save(User $user): void;
    
    public function flush(User $user): void;

    public function saveAll(Users $users): void;
    
    public function search(UserId $id): ?User;
    
    public function all(): Users;
}
```

De este modo, nuestra interface UserRepository tendría que tener el aspecto que vemos arriba, haciendo Header Interface 
hemos definido la cabecera de los métodos save y flush en nuestra interface.

Clase UserTotalVideosCreatedIncreaser:

```php
public function __invoke(UserId $id)
{
    $user = $this->finder->__invoke($id);
       
    $user->increaseTotalVideosCreated();
       
    $this->repository->save($user);
    $this->repository->flush($user);
}
```
Finalmente, nuestro caso de uso se vería de esta manera, podemos observar que una vez llamado al método invoke, buscará 
al usuario en BD para incrementar el total de videos creados y posteriormente persistir este nuevo estado con los
 métodos definidos previamente.

¿Y si queremos hacer una implementación para otra BD como Redis?
Si quisiéramos implementar ciertas bases de datos como Redis, que no contemplan el patrón Unit of work, nos estaría sobrando la llamada al método flush.

¿Cómo lo solucionamos?
Nuestro caso de uso no tiene por qué conocer qué hace la implementación por detrás, de modo que simplemente llamaría 
al método save

Clase UserTotalVideosCreatedIncreaser:

```php
public function __invoke(UserId $id)
{
    $user = $this->finder->__invoke($id);
      
    $user->increaseTotalVideosCreated();
        
    $this->repository->save($user);
}
```
Interface UserRepository

```php
interface UserRepository
{
    public function save(User $user): void;

    public function saveAll(Users $users): void;
    
    public function search(UserId $id): ?User;
    
    public function all(): Users;
}
```
Así, nuestra interface tampoco tendría por qué aludir al método flush, sino que será nuestra implementación de 
UserRepositoryMySql la que se encargue de manera interna de que el usuario persista correctamente en BD.

Clase UserRepositoryMySql:

```php
final class UserRepositoryMySql extends Repository implements UserRepository
{
    public function save(User $user): void
    {
        $this->entityManager()->persist($user);    
        $this->entityManager()->flush($user);
    }

    public function saveAll(Users $users)
    {
        each($this->persister(),$users);
    }
}
```

Aunque el código inicial podríamos pensar que estaba desacoplado de la implementación gracias a la interface, si 
que estaba acoplado estructuralmente 🧩:

Desde nuestro caso de uso sabíamos que debíamos de llamar primero al método save y después al método flush
Ojo! 👀, con este cambio conseguimos cumplir con el ISP, pero a cambio estamos perdiendo las ventajas que puede ofrecernos 
la Unit of work, si dado el caso necesitásemos aprovecharla en nuestra aplicación, tendríamos que plantear un diseño diferente
