<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *     title="PTIT eCommerce API",
 *     version="1.0.0",
 *     description="Tài liệu API cho PTIT eCommerce, bao gồm các luồng của Admin, Nhân viên và Khách hàng."
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Current host"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Get(
 *     path="/api/ping",
 *     summary="Health check",
 *     @OA\Response(response=200, description="OK")
 * )
 */
class OpenApi
{
    // Chỉ chứa annotation cho swagger-php
}

