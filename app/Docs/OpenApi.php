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
 *     ),
 *     @OA\Schema(
 *         schema="UserIsNotAuthorized",
 *         type="object",
 *         description="Пользователь не аутентифицирован и не авторизован.",
 *         properties={
 *             @OA\Property(
 *                 property="status",
 *                 ref="#/components/schemas/Status"
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 description="Полезная нагрузка ответа.",
 *                 properties={
 *                     @OA\Property(
 *                         property="message",
 *                         type="string",
 *                         description="Сообщение об ошибке аутентификации и авторизации."
 *                     ),
 *                     @OA\Property(
 *                         property="links,
 *                         type="object",
 *                         description="HATEOAS",
 *                         properties={
 *                             @OA\Property(
 *                                 property="auth",
 *                                 type="string",
 *                                 description="URL, чтобы аутентифицироваться и авторизоваться."
 *                             )
 *                         }
 *                     )
 *                 }
 *             )
 *         }
 *     )
 * )
 */

class OpenApi
{

}
