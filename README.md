# comentarios-API
Simplemente, un muro donde la gente pueda poner frases y que el resto de la gente las pueda ver.

## Llamadas API

Llamadas disponibles:

[POST] http://localhost/comentarios-API/crear (inicializar contador)

[POST] http://localhost/comentarios-API/datos_prueba (añadir un par de comentarios de prueba)

[GET] http://localhost/comentarios-API/comentarios (datos en formato json)

[GET] http://localhost/comentarios-API/comentarios/:id (datos en formato json)

[POST] http://localhost/comentarios-API/comentarios/:usuario
parámetro: comment

[POST] http://localhost/comentarios-API/comentarios/:usuario/favoritos/:id

[GET] http://localhost/comentarios-API/comentarios/:usuario/favoritos

[GET] http://localhost/comentarios-API/comentarios/favoritos