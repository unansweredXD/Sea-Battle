<?php

namespace site\Controller;

use core\Controller\Controller;
use core\View\JsonView;
use core\View\View;

class IndexController extends Controller {
    public function startAction(): View {
        $info = $this->gameModel->generateGame();

        return new JsonView($info);
    }
}
