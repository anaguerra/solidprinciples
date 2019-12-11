D — Dependency Inversion Principle
--------------------------------

Un paso más allá de la inyección.
Qué implicaciones tiene inyectar e invertir una dependencia.

**Concepto**

Módulos (clases) de alto nivel no deberían depender de los de bajo nivel. 
Ambos deberáin depender de abstracciones.

Si tengo un notificador por Slack y hay un notificador abstracto como vimos antes mi caso
de uso no debería depender del notificador de Slack sino de la abstracción.

El caso de uso estaría a alto nivel y la interfaz del notificador que estaría al mismo nivel.

El otro escenario es donde no habría interfaz sino que el caso de uso estaría hablando 
directamente con la implementación de Slack. Estarían hablando desde un nivel (capa) superior a un
nivel inferior (implementación)


**Cómo**

- Inyectar dependencias (parámetros recibidos idealmente en el constructor)
- Depender de las interfaces (contratos) de estas dependencias y no de implementaciones concretas
- LSP como premisa

**Finalidad**

- Facilitar la modificación y substitución de implementaciones
- Mejorar testabilidad de clases


**Ejemplo**

