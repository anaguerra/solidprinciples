I — Interface Segregation Principle
--------------------------------

**Concepto**

Ningún cliente debería verse forzado a depender de métodos que no usa.

P.e.: un Notificador. Tendríamos un INotificador, con los métodos EnviarNotificacion, ObtenerUltimasNotificaciones,
MarcarNotificacionesLeidas, etc...

y luego un cliente (caso de uso) que haría "EnviarNotificaciones". 
El cliente en el momento que conoce a la interfaz, no va a necesitar todos esos métodos. 


**Cómo**

- Definir contratos de interfaces basándonos en los clientes (casos de uso) que las usan y no en las implementaciones
que pudiéramos tener. Cuando definamos los contratos (cabeceras de los métodos) es decir: nombres, argumentos tipo de retorno, etc... hay 
que hacerlo pensando en esos clientes.

    Está relacionado con TDD, primero haces el test, después llega el caso de uso y cuando te hace falta la interfaz
    la haces con lo que te hace falta.
    Primero el caso de uso, y después de lo que te haga falta, de eso deriva la interfaz. "de fuera hacia dentro" tdd -->
    partir de lo más externo que se pueda. 
    Evitar optimizaciones y abstracciones prematuras, etc.
   

- Evitar "Header Interfaces" promoviendo "Role Interface". 

En vez de definir el caso de uso y la interfaz en base a ese caso de uso, tenemos p.e. todo el Repositorio de usuario con
todos los métodos y a partir de ahí hacemos la interfaz. 
Deben ir creciendo a la par. 
Las cabeceras de los métodos tienen que estar d


**Finalidad**

- Alta cohesión y bajo acoplamiento estructural.


**Ejemplos**

- Queremos enviar notificaciones via email, Slack, o fichero txt. ¿qué firma tendrá la interfaz?

    a) $notifier($content)
    b) $notifier($slackChannel, $msgTitle, $msgContent, $msgStatus)
    c) $notifier($receiverEmail, $emailSubject, $emailContent)
    d) $notifier($destination, $subject, $content)
    e) $notifier($filename, $tag, $description)
    

b,c,e descartadas de principio. Están haciendo "Header Interface".

La d hace un intento de abstracción. El problema vendría con los tipos. $destination qué es? file, string, channel, email?
Si no podemos ser específicos en el tipo, es un problema a la hora de validar.

La "a", que es más genérica. Pero si solo recibe el "content, es sólo el contenido de la notificación. 
La implementación de las particularidades (email, asunto, chanel, etc) tendrían que venir dados por el constructor. 
Y no siempre se podría.

En realidad la respuesta correcta es "ninguna de las anteriores".

La decisión se tomaría por la diferencia: nivel de abstracción de la implementación:

    -la opción a) Si sabemos destinatario en teimpo de compilación (params por constructor en adaptador)
    -la opción d) Si no sabemos destinatario en teimpo de compilación (perdemos tipado)
    -nueva opción f) Habría una infraestructura de contextos y módulos con un
     contexto de notificaciones el cual tendría módulo de email, otro de slack, etc. y escucharían eventos (ver susbscribers)



**¿Cómo detectar que estamos violando el Principio de segregación de interfaces?**
  
Si al implementar una interfaz ves que uno o varios de los métodos no tienen sentido y te hace falta dejarlos vacíos 
o lanzar excepciones, es muy probable que estés violando este principio. Si la interfaz forma parte de tu código, divídela 
en varias interfaces que definan comportamientos más específicos.
  
Recuerda que no pasa nada porque una clase ahora necesite implementar varias interfaces. El punto importante es 
que use todos los métodos definidos por esas interfaces


Más: https://devexperto.com/principio-de-segregacion-de-interfaces/