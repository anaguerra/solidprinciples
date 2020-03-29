D — Dependency Inversion Principle
--------------------------------

**Introducción Conceptual**

Concepto:

Módulos de alto nivel no deberían depender de los de bajo nivel. Ambos deberían depender de abstracciones

Cómo:

Inyectar dependencias (parámetros recibidos en constructor)

Depender de las interfaces (contratos) de estas dependencias y no de implementaciones concretas
LSP como premisa

Finalidad:

- Facilitar la modificación y substitución de implementaciones
- Mejor testabilidad de clases




**Ejemplo Sencillo**

Repo: https://github.com/CodelyTV/java-solid-examples/tree/master/src/main/java/tv/codely/solid_principles/dependency_inversion_principle


Etapa 1 - Instanciación desde los clientes 🔒

Clase UserSearcher:

```
final class UserSearcher {
    private HardcodedInMemoryUsersRepository usersRepository = new HardcodedInMemoryUsersRepository();

    public Optional<User> search(Integer id) {
        return usersRepository.search(id);
    }
  }
```

Clase HardcodedInMemoryUsersRepository:

```
final class HardcodedInMemoryUsersRepository {
    private Map<Integer, User> users = Collections.unmodifiableMap(new HashMap<Integer, User>() {
        {
            put(1, new User(1, "Rafa"));
            put(2, new User(2, "Javi"));
        }
    });

    public Optional<User> search(Integer id) {
        return Optional.ofNullable(users.get(id));
    }
}
```

En esta primera fase, estaríamos instanciando en la propia clase el repositorio que vamos a utilizar en el método search, es 
decir, cuando instanciemos nuestro UserSearcher, esta clase internamente estaría haciendo un new de HardcodedInMemoryUsersRepository,
 lo cual nos lleva inevitablemente a estar fuertemente acoplados a dicho repositorio 🔒.

Test UserSearcherShould:

```
final class UserSearcherShould {
    @Test
    void find_existing_users() {
        UserSearcher userSearcher = new UserSearcher();

        Integer existingUserId = 1;
        Optional<User> expectedUser = Optional.of(new User(1, "Rafa"));

        assertEquals(expectedUser, userSearcher.search(existingUserId));
    }

    @Test
    void not_find_non_existing_users() {
        UserSearcher userSearcher = new UserSearcher();

        // We would be coupled to the actual HardcodedInMemoryUsersRepository implementation.
        // We don't have the option to set test users as we would have to do if we had a real database repository.
        Integer nonExistingUserId = 5;
        Optional<User> expectedEmptyResult = Optional.empty();

        assertEquals(expectedEmptyResult, userSearcher.search(nonExistingUserId));
    }
}
```

Desde el propio Test ya se observa este acoplamiento, obligando a saber, en este caso, que el usuario tiene que existir en el 
HashMap (caso de find_existing_users) o que no va a existir un usuario con un id concreto (caso de not_find_non_existing_users).

Etapa 2.0 Inyección de Dependencias 💉

Clase UserSearcher:

```
final class UserSearcher {
    private HardcodedInMemoryUsersRepository usersRepository;

    public UserSearcher(HardcodedInMemoryUsersRepository usersRepository) {
        this.usersRepository = usersRepository;
    }

    public Optional<User> search(Integer id) {
        return usersRepository.search(id);
    }
}
```

Vamos un paso más allá en para reducir el acoplamiento en nuestra UserSearcher, para ello inyectaremos la dependencia que 
nuestra clase tiene respecto a HardcodedInMemoryUsersRepository en el propio constructor. De este modo, el punto de nuestro 
aplicación que instancie a nuestro UserSearcher será el responsable de saber cómo debe hacerlo y que otras dependencias puede 
haber detrás.


Test UserSearcherShould:

```
final class UserSearcherShould {
    @Test
    void find_existing_users() {
        // Now we're injecting the HardcodedInMemoryUsersRepository instance through the UserSearcher constructor.
        // 👍 Win: We've moved away from the UserSearcher the instantiation logic of the HardcodedInMemoryUsersRepository class allowing us to centralize it.
        // 👍 Win: We're exposing the couplings of the UserSearcher class.
        HardcodedInMemoryUsersRepository usersRepository = new HardcodedInMemoryUsersRepository();
        UserSearcher userSearcher = new UserSearcher(usersRepository);

        Integer existingUserId = 1;
        Optional<User> expectedUser = Optional.of(new User(1, "Rafa"));

        assertEquals(expectedUser, userSearcher.search(existingUserId));
    }

    @Test
    void not_find_non_existing_users() {
        HardcodedInMemoryUsersRepository usersRepository = new HardcodedInMemoryUsersRepository();
        UserSearcher userSearcher = new UserSearcher(usersRepository);

        Integer nonExistingUserId = 5;
        Optional<User> expectedEmptyResult = Optional.empty();

        assertEquals(expectedEmptyResult, userSearcher.search(nonExistingUserId));
    }
}
```

A nivel de Test observamos que, aunque no hemos ganado mucho en términos de acoplamiento, si que conseguimos exponer el 
acoplamiento de nuestras clases.

Etapa 2.1 Inyección de Dependencias de Parámetros 💉

Clase UserSearcher:

```
final class UserSearcher {
    private HardcodedInMemoryUsersRepository usersRepository;

    public UserSearcher(HardcodedInMemoryUsersRepository usersRepository) {
        this.usersRepository = usersRepository;
    }

    public Optional<User> search(Integer id) {
        return usersRepository.search(id);
    }
}
```

Clase HardcodedInMemoryUsersRepository:

```
final class HardcodedInMemoryUsersRepository {
    private Map<Integer, User> users;

    public HardcodedInMemoryUsersRepository(Map<Integer, User> users) {
        this.users = users;
    }

    public Optional<User> search(Integer id) {
        return Optional.ofNullable(users.get(id));
    }
}
```

Aunque la clase UserSearcher no ha cambiado, hemos dado un paso más al realizar la inyección de dependencias de forma 
recursiva con el HardcodedInMemoryUsersRepository, que ahora recibiría como argumento en el constructor su atributo de clase users.

Test UserSearcherShould:

```
final class UserSearcherShould {
    @Test
    void find_existing_users() {
        // Now we're also injecting the constant parameters needed by the HardcodedInMemoryUsersRepository through its constructor.
        // 👍 Win: We can send different parameters depending on the environment.
        // That is, in our production environment we would have actual users,
        // while in our tests cases we will set only the needed ones to run our test cases
        int rafaId = 1;
        User rafa = new User(rafaId, "Rafa");

        Map<Integer, User> users = Collections.unmodifiableMap(new HashMap<Integer, User>() {
            {
                put(rafaId, rafa);
            }
        });
        HardcodedInMemoryUsersRepository usersRepository = new HardcodedInMemoryUsersRepository(users);
        UserSearcher userSearcher = new UserSearcher(usersRepository);

        Optional<User> expectedUser = Optional.of(rafa);

        assertEquals(expectedUser, userSearcher.search(rafaId));
    }

    @Test
    void not_find_non_existing_users() {
        Map<Integer, User> users = Collections.emptyMap();
        HardcodedInMemoryUsersRepository usersRepository = new HardcodedInMemoryUsersRepository(users);
        UserSearcher userSearcher = new UserSearcher(usersRepository);

        // 👍 Win: Now we don't have to be coupled of the actual HardcodedInMemoryUsersRepository users.
        // We can send a random user ID in order to force an empty result because we've set an empty map as the system users.
        Integer nonExistingUserId = 1;
        Optional<User> expectedEmptyResult = Optional.empty();

        assertEquals(expectedEmptyResult, userSearcher.search(nonExistingUserId));
    }
}
```

Si echamos un vistazo a los Test, vemos cómo ya no tenemos por qué saber qué usuarios existen en nuestro repositorio, 
por lo que conseguimos aislar nuestros Test sin que dependan de la infraestructura (Profundizamos en esto y mucho más en el 
curso de Testing: Introducción y buenas prácticas).

Etapa 3 - Inversión de Dependencias 🤹‍♀️

Clase UserSearcher:

```
final class UserSearcher {
    private UsersRepository usersRepository;

    public UserSearcher(UsersRepository usersRepository) {
        this.usersRepository = usersRepository;
    }

    public Optional<User> search(Integer id) {
        return usersRepository.search(id);
    }
}
```
Interface UsersRepository:

```
public interface UsersRepository {
    Optional<User> search(Integer id);
}
```
Vemos como ahora la clase UserSearcher lo que recibe por argumento en el constructor no es una implementación de UserRepository, sino una interface que define únicamente el contrato de un método search.

Test UserSearcherShould:

```
final class UserSearcherShould {
    @Test
    void find_existing_users() {
        // Now we're injecting to the UserSearcher use case different implementation of the new UserRepository interface.
        // 👍 Win: We can replace the actual implementation of the UsersRepository used by the UserSearcher.
        // That is, we'll not have to modify a single line of the UserSearcher class despite of changing our whole infrastructure.
        // This is a big win in terms of being more tolerant to changes.
        // 👍 Win: It also make it easier for us to test the UserSearcher without using the actual implementation of the repository used in production.
        // This is another big win because this way we can have test such as the following ones which doesn't actually go to the database in order to retrieve the system users.
        // This has a huge impact in terms of the time to wait until all of our test suite is being executed (quicker feedback loop for developers 💪).
        // 👍 Win: We can reuse the test environment repository using test doubles. See CodelyTvStaffUsersRepository for its particularities
        UsersRepository codelyTvStaffUsersRepository = new CodelyTvStaffUsersRepository();
        UserSearcher userSearcher = new UserSearcher(codelyTvStaffUsersRepository);

        Optional<User> expectedUser = Optional.of(UserMother.rafa());

        assertEquals(expectedUser, userSearcher.search(UserMother.RAFA_ID));
    }

    @Test
    void not_find_non_existing_users() {
        // 👍 Win: Our test are far more readable because they doesn't have to deal with the internal implementation of the UserRepository.
        // The test is 100% focused on orchestrating the Arrange/Act/Assert or Given/When/Then flow.
        // More info: http://wiki.c2.com/?ArrangeActAssert and https://www.martinfowler.com/bliki/GivenWhenThen.html
        UsersRepository emptyUsersRepository = new EmptyUsersRepository();
        UserSearcher userSearcher = new UserSearcher(emptyUsersRepository);

        Integer nonExistingUserId = 1;
        Optional<User> expectedEmptyResult = Optional.empty();

        assertEquals(expectedEmptyResult, userSearcher.search(nonExistingUserId));
    }
}
```
A nivel de Test ya vemos cómo podemos cambiar la implementación de UserRepository sin necesidad de tocar nuestro 
UserSearcher, es decir, podemos pasarle como argumento cualquier clase que implemente la interface.

**Conclusión**

Ahora el acoplamiento irá de nuestro caso de uso a la interface y las diferentes implementaciones se dirigirán 
hacia nuestra interface, en lugar de acoplar acoplar nuestro caso de uso a la implementación.