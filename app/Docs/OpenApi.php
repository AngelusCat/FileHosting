<?php

namespace App\Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     openapi="3.0.2",
 *     @OA\Info(
 *         title="FileHosting API",
 *         version="1.0",
 *         description="API позволяет загружать файлы на сервер, получать их метаданные и содержимое после загрузки, а также изменять некоторые метаданные загруженного файла."
 *     ),
 *     @OA\Server(
 *         url="http://file/api/",
 *         description="Основной сервер FileHosting API"
 *     )
 * )
 */

class OpenApi
{

}
