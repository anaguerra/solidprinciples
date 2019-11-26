I — Interface Segregation Principle
--------------------------------

**Concepto**

Ningún cliente debería verse forzado a depender de métodos que no usa.

P.e.: un Notificador. Tendríamos un INotificador, con los métodos EnviarNotificacion, ObtenerUltimasNotificaciones,
MarcarNotificacionesLeidas

y luego un cliente (caso de uso) que haría "EnviarNotificaciones"


**Cómo**

- Definir contratos de interfaces basándonos en los clientes que las usan y no en las implementaciones
que pudiéramos tener.

- Evitar "Header Interfaces" promoviendo "Role Interface"






**¿Cómo detectar que estamos violando el Principio de segregación de interfaces?**
  
Si al implementar una interfaz ves que uno o varios de los métodos no tienen sentido y te hace falta dejarlos vacíos 
o lanzar excepciones, es muy probable que estés violando este principio. Si la interfaz forma parte de tu código, divídela 
en varias interfaces que definan comportamientos más específicos.
  
Recuerda que no pasa nada porque una clase ahora necesite implementar varias interfaces. El punto importante es 
que use todos los métodos definidos por esas interfaces


Más: https://devexperto.com/principio-de-segregacion-de-interfaces/