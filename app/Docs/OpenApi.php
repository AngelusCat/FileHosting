<?php

namespace App\Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *      openapi="3.0.2",
 *      @OA\Info(
 *          title="FileHosting API",
 *          version="1.0",
 *          description="API позволяет загружать файлы на сервер, получать их метаданные и содержимое после загрузки, а также изменять некоторые метаданные загруженного файла."
 *      ),
 *      @OA\Server(
 *          url="http://file/api/",
 *          description="Основной сервер FileHosting API"
 *      )
 *  )
 *
 * @OA\Components(
 *      @OA\Schema(
 *          schema="Status",
 *          type="string",
 *          description="Статус выполнения запроса.",
 *          enum={"success", "fail", "error"}
 *      ),
 *      @OA\Schema(
 *          schema="HATEOAS_Content",
 *          type="string",
 *          description="URL, чтобы получить содержимое загруженного файла."
 *      ),
 *      @OA\Schema(
 *          schema="HATEOAS_Metadata",
 *          type="string",
 *          description="URL, чтобы получить метаданные загруженного файла."
 *      ),
 *      @OA\Response(
 *          response="422",
 *          description="Ошибки валидации входных данных.",
 *          @OA\JsonContent(
 *              type="object",
 *              properties={
 *                  @OA\Property(
 *                      property="message",
 *                      type="string"
 *                  ),
 *                  @OA\Property(
 *                      property="errors",
 *                      type="object",
 *                      description="Ошибки валидации.",
 *                      properties={
 *                          @OA\Property(
 *                              property="Название поля, не прошедшее проверку валидации.",
 *                              type="string"
 *                          )
 *                      }
 *                  )
 *              }
 *          )
 *      ),
 *      @OA\Schema(
 *          schema="UserIsNotAuthorized",
 *          type="object",
 *          description="Пользователь не аутентифицирован и не авторизован.",
 *          properties={
 *              @OA\Property(
 *                  property="status",
 *                  ref="#/components/schemas/Status"
 *              ),
 *              @OA\Property(
 *                  property="data",
 *                  type="object",
 *                  description="Полезная нагрузка ответа.",
 *                  properties={
 *                      @OA\Property(
 *                          property="message",
 *                          type="string",
 *                          description="Сообщение об ошибке аутентификации и авторизации."
 *                      ),
 *                      @OA\Property(
 *                          property="links",
 *                          type="object",
 *                          description="HATEOAS",
 *                          properties={
 *                              @OA\Property(
 *                                  property="auth",
 *                                  type="string",
 *                                  description="URL, чтобы аутентифицироваться и авторизоваться."
 *                              )
 *                          }
 *                      )
 *                  }
 *              )
 *          }
 *      ),
 *      @OA\Parameter(
 *          parameter="fileId",
 *          name="id",
 *          in="path",
 *          description="ID загруженного файла.",
 *          required=true,
 *          allowEmptyValue=false,
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      )
 *  )
 */

class OpenApi
{

}
