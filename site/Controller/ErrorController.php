<?php

namespace site\Controller;

use core\Controller\Controller;
use core\View\JsonView;
use core\View\View;
use site\Helper\GeneratorModel;

class ErrorController extends Controller {
    public function notFoundAction(): View {
        return new JsonView(
            GeneratorModel::generateError('Page-not-found', 'Страница не найдена. Сервер не может найти совпадения с полученным URI'),
            404
        );
    }

    public function badRequestAction(): View {
        return new JsonView(
            GeneratorModel::generateError('Bad-Request', 'Страница не найдена. Сервер не может обработать полученный URI'),
            400
        );
    }
}
