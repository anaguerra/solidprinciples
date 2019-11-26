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