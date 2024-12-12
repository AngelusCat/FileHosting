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
 *
 * @OA\Components(
 *     @OA\Schema(
 *         schema="Status",
 *         type="string"
 *         description="Статус выполнения запроса.",
 *         enum={"success", "fail", "error"}
 *     ),
 *     @OA\Schema(
 *         schema="HATEOAS_Content",
 *         type="string",
 *         description="URL, чтобы получить содержимое загруженного файла."
 *     )
 * )
 */

class OpenApi
{

}
