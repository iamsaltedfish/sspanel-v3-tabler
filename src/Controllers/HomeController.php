<?php

namespace App\Controllers;

use App\Utils\Telegram\Process;
use App\Utils\TelegramProcess;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 *  HomeController
 */
class HomeController extends BaseController
{
    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function index($request, $response, $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('index.tpl'));
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function tos($request, $response, $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('tos.tpl'));
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function staff($request, $response, $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('staff.tpl'));
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function telegram($request, $response, $args): ResponseInterface
    {
        $token = $request->getQueryParam('token');
        if ($token === $_ENV['telegram_request_token']) {
            if ($_ENV['use_new_telegram_bot']) {
                Process::index();
            } else {
                TelegramProcess::process();
            }
            $result = '1';
        } else {
            $result = '0';
        }
        return $response->write($result);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function page404($request, $response, $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('404.tpl'));
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function page405($request, $response, $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('405.tpl'));
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function page500($request, $response, $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('500.tpl'));
    }
}
