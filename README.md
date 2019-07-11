# PhpCompiler

## Run

    php __main__.php -c filename.f

### Example: ***filename.f*** 
__Source code__
```js
var1 = 4;
var2 = var1 + 3;
```
__lexical tokens__
```json
[{"ID":"var1","line":1},{"OPERADORASIGNACION":"=","line":1},{"NUMERO":"4","line":1},{"PUNTOYCOMA":";","line":1},{"ID":"var2","line":2},{"OPERADORASIGNACION":"=","line":2},{"ID":"var1","line":2},{"OPERADOR":"+","line":2},{"NUMERO":"3","line":2},{"PUNTOYCOMA":";","line":2}]
```
__syntax tree__
```
<Programa>
├───<Sentencia>
│   └───<Asignacion>
│       ├───ID (var1)
│       ├───OPERADORASIGNACION (=)
│       └───<Expresion>
│           └───<ExpresionAritmetica>
│               ├───NUMERO (4)
│               └───<ExpresionAritmeticaFinal>
│                   └───EPSILON
├───PUNTOYCOMA (;)
└───<ProgramaFin>
    ├───<Sentencia>
    │   └───<Asignacion>
    │       ├───ID (var2)
    │       ├───OPERADORASIGNACION (=)
    │       └───<Expresion>
    │           └───<ExpresionAritmetica>
    │               ├───ID (var1)
    │               └───<ExpresionAritmeticaFinal>
    │                   ├───OPERADOR (+)
    │                   └───<ExpresionAritmetica>
    │                       ├───NUMERO (3)
    │                       └───<ExpresionAritmeticaFinal>
    │                           └───EPSILON
    ├───PUNTOYCOMA (;)
    └───<Programa>
        └───EPSILON
```
