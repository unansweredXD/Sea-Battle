<?php

namespace site\Controller;

use core\Controller\Controller;
use core\View\JsonView;
use core\View\View;
use site\Model\MessageModel;

class MessageController extends Controller {
    public function loadAction(): View {
        $get = $this->request->get();

        $message = new MessageModel($this->param['gameId'], $this->param['playerId']);

        // во время начала игры $get['lastTime'] = false, в остальные моменты - unix timestamp
        $isUpdate = filter_var($get['lastTime'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

        if (is_null($isUpdate)) {
            $messageList = $message->getMessageInfo($this->param['playerId'], (int)$get['lastTime']);
        } else {
            $messageList = $message->getMessageInfo($this->param['playerId']);
        }

        $info['lastTime'] = time();
        $info['messages'] = $messageList;
        $info['success']  = true;

        return new JsonView($info);
    }

    public function sendAction(): View {
        $post = $this->request->post();

        $playerMessage = substr($post['message'], 0, 250);

        $playerMessage = htmlspecialchars($playerMessage);

        $message = new MessageModel($this->param['gameId'], $this->param['playerId'], $playerMessage);

        $info = $message->send();

        return new JsonView($info);
    }
}
