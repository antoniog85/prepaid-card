<?php

namespace PrepaidCard;

interface HttpStatusCodes
{
    public const HTTP_OK                    = 200;
    public const HTTP_CREATED               = 201;
    public const HTTP_ACCEPTED              = 201;
    public const HTTP_NO_CONTENT            = 204;
    public const HTTP_BAD_REQUEST           = 400;
    public const HTTP_UNAUTHORIZED          = 401;
    public const HTTP_NOT_FOUND             = 404;
    public const HTTP_CONFLICT              = 409;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
}
